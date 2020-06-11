<template>
  <main class="pv4 pv5-ns ph4 ph5-l bg-nearer-white overflow-y-scroll">
    <div class="h2"></div>
    <div
      class="w-100 center mw-doc pt4 pb5 ph4 ph5-l bg-white br2 css-white-box"
      style="margin-bottom: 15rem"
    >
      <div class="tc" style="margin-top: -76px">
        <img
          src="../assets/logo-square-80x80.png"
          alt="Flex.io"
          class="br-100 ba bw1 b--white"
          style="width: 84px; box-shadow: 0 0 3px rgba(0,0,0,0.4)"
        >
      </div>

      <h1 class="fw6 f2 tc pb2">{{display_title}}</h1>

      <!-- step heading -->
      <div class="dn db-ns mv4 pv2">
        <el-steps
          align-center
          finish-status="success"
          :active="step_heading_idx"
        >
          <el-step :title="is_quick_start ? 'Sign Up' : 'Set Up Integration'" />
          <el-step title="Get Add-on" />
          <el-step title="Get Started" />
        </el-steps>
      </div>

      <!-- step: choose integrations -->
      <div v-if="active_step == 'integrations'">
        <p class="center mw7">Let's get started with an integration. Pick one of the services below and we'll add a few out-of-the-box functions you can use in your spreadsheet.</p>
        <IconList
          class="mv4"
          :items="integrations"
          :selected-items.sync="selected_integrations"
          :allow-selection="true"
          :allow-multiple="false"
        />
      </div>

      <!-- step: set up integrations -->
      <div v-if="active_step == 'integration-setup'">
        <!-- fetching config -->
        <div v-if="is_fetching_config">
          <div class="br2 ba b--black-10 pv5 ph4">
            <Spinner size="large" message="Loading configuration..." />
          </div>
        </div>

        <FunctionMountSetupWizard
          :setup-template="active_setup_template"
          @submit="saveIntegration"
          v-else
        >
          <div slot="no-prompts">
            <div class="mv2 tc f6 fw4 lh-copy moon-gray"><em>No configuration is required for this integration.</em></div>
            <ButtonBar
              class="mt4"
              :cancel-button-visible="false"
              :cancel-button-text="'Back'"
              :submit-button-text="'Next'"
              @submit-click="saveIntegration({})"
            />
          </div>
        </FunctionMountSetupWizard>
      </div>


      <!-- step: integration setup complete -->
      <div v-if="active_step == 'integration-setup-complete'">
        <ServiceIconWrapper :innerSpacing="10">
          <i
            class="el-icon-success bg-white f2 dark-green"
            slot="icon"
          ></i>
          <p class="tc f3 lh-title">Your integration was created successfully!</p>
          <div class="bg-nearer-white pa4 br2 tc">
            <p class="mb0 f4 fw6 flex flex-row items-center justify-center">
              <Spinner class="mr2" />
              <span>Importing functions...</span>
            </p>
            <p class="center mw6 mb0 f7 i light-silver lh-copy">NOTE: This process may take a couple of minutes to complete. You're free to continue with the setup &mdash; the import will continue in the background.</p>
          </div>
        </ServiceIconWrapper>
      </div>

      <!-- step: invite others -->
      <div v-if="active_step == 'members'">
        <p class="center mw7">It's easy to share your functions with co-workers. Simply add their emails below and they'll receive an invite to join your team. You can also skip this step and add people later.</p>
        <ServiceIconWrapper class="mv4">
          <i
            class="material-icons moon-gray bg-white" style="font-size: 4rem"
            slot="icon"
          >people</i>
          <MemberInvitePanel
            :show-header="false"
            :emails="email_invites"
            @submit="onMemberInviteSubmit"

          />
        </ServiceIconWrapper>
      </div>

      <!-- step: install add-ons -->
      <div v-if="active_step == 'addons'">
        <p class="center mw7">You're almost done! If you haven't done so already, please install the Flex.io Add-on for <span class="nowrap">Google Sheets</span> or <span class="nowrap">Microsoft Excel 365</span>. Once you've installed the add-on, you'll see your functions in the Flex.io sidebar and will be able to use them in your spreadsheet.</p>
        <AddonDownloadButtonPanel
          class="pv2-l"
          :open-links-in-new-window="true"
        />
        <p class="center mw7 f8 silver">* The Microsoft Excel 365 add-in will only function with an Excel for Office 365 subscription</p>
      </div>

      <!-- step: integration setup complete -->
      <IntegrationSetupCompletePanel
        :integration-info="integration_info"
        @template-click="onTemplateClick"
        @open-app-click="onNextStepClick"
        v-if="active_step == 'complete'"
      />

      <!-- button bar for the entire onboarding wizard -->
      <ButtonBar
        class="mt5"
        :utility-button-type="'text'"
        :utility-button-visible="is_start_over_button_visible"
        :utility-button-text="active_step_idx == 1 ? '← Back' : '← Start over'"
        :cancel-button-visible="false"
        :submit-button-visible="active_step != 'integration-setup' && active_step != 'complete'"
        :submit-button-text="active_step == 'integrations' ? 'Skip this step' : 'Continue'"
        @utility-click="onUtilityButtonClick"
        @submit-click="onNextStepClick"
        v-if="active_step_idx < step_order.length - 1"
      />
   </div>
  </main>
</template>

<script>
  import axios from 'axios'
  import randomstring from 'randomstring'
  import Spinner from 'vue-simple-spinner'
  import { mapState, mapGetters } from 'vuex'
  import { OBJECT_TYPE_CONNECTION } from '@/constants/object-type'
  import api from '@/api'
  import { buildQueryString } from '@/utils'
  import ButtonBar from '@/components/ButtonBar'
  import IconList from '@/components/IconList'
  import ServiceIconWrapper from '@/components/ServiceIconWrapper'
  import FunctionMountSetupWizard from '@/components/FunctionMountSetupWizard'
  import AddonDownloadButtonPanel from '@/components/AddonDownloadButtonPanel'
  import IntegrationSetupCompletePanel from '@/components/IntegrationSetupCompletePanel'
  import MemberInvitePanel from '@/components/MemberInvitePanel'

  const getNameSuffix = (length) => {
    return randomstring.generate({
      length,
      charset: 'alphabetic',
      capitalization: 'lowercase'
    })
  }

  const getDefaultState = () => {
    return {
      is_fetching_config: false,
      onboarding_config_submitted: false,
      route_title: '',
      custom_title: '',
      active_step: 'integrations', // 'integrations', 'integration-setup', 'integration-setup-complete', 'addons', 'members'
      active_integration_idx: 0,
      active_setup_template: null,
      selected_integrations: [],
      output_mounts: [],
      email_invites: [],
      last_step_items: [{
        title: 'Getting Started Guide',
        description: 'Learn how to work with Flex.io in your spreadsheet',
        link: 'https://www.flex.io/resources/getting-started'
      },{
        title: 'Basic Examples',
        description: 'Explore some basic examples you can try immediately',
        link: 'https://www.flex.io/resources/examples'
      },{
        title: 'Explore Integrations',
        description: 'See how you can use Flex with other web services',
        link: 'https://www.flex.io/explore'
      }]
    }
  }

  export default {
    metaInfo() {
      return {
        title: this.title,
        titleTemplate: (chunk) => {
          return `${chunk} | Flex.io`
        }
      }
    },
    components: {
      Spinner,
      ButtonBar,
      IconList,
      FunctionMountSetupWizard,
      ServiceIconWrapper,
      AddonDownloadButtonPanel,
      IntegrationSetupCompletePanel,
      MemberInvitePanel
    },
    watch: {
      selected_integrations: {
        handler: 'onSelectedIntegrationChange'
      }
    },
    data() {
      return getDefaultState()
    },
    computed: {
      ...mapState({
        active_team_name: state => state.teams.active_team_name,
      }),
      integrations() {
        return this.getProductionIntegrations()
      },
      active_step_idx() {
        return _.indexOf(this.step_order, this.active_step)
      },
      active_integration() {
        return this.selected_integrations[this.active_integration_idx]
      },
      integrations_from_route() {
        var integrations = _.get(this.$route, 'query.integration', '')
        return integrations.length > 0 ? integrations.split(',') : []
      },
      is_quick_start() {
        return _.get(this.integrations_from_route, '[0]', '') == 'quick-start'
      },
      step_order() {
        if (this.is_quick_start) {
          return ['addons', 'complete']
        }

        return ['integrations', 'integration-setup', 'integration-setup-complete', 'addons', 'complete']
      },
      // this is just for display purposes
      step_heading_idx() {
        switch (this.active_step) {
          case 'integrations':               return 0
          case 'integration-setup':          return 0
          case 'integration-setup-complete': return 0
          case 'addons':                     return 1
          case 'complete':                   return 2
        }
        return 0
      },
      has_active_setup_template() {
        return !_.isNil(this.active_setup_template)
      },
      gsheets_spreadsheet_id() {
        return _.get(this.$route, 'query.gsheets_spreadsheet_id', undefined)
      },
      excel_spreadsheet_path() {
        return _.get(this.$route, 'query.excel_spreadsheet_path', undefined)
      },
      is_start_over_button_visible() {
        if (this.active_step_idx == 0) {
          return false
        } else if (this.active_step_idx == 1 && this.integrations_from_route.length > 0) {
          return false
        } else {
          return true
        }
      },
      title() {
        if (this.route_title.length > 0) {
          return this.route_title
        } else {
          return 'Welcome to Flex.io!'
        }
      },
      display_title() {
        if (this.custom_title.length > 0) {
          return this.custom_title
        } else {
          return this.title
        }
      },
      integration_info() {
        return _.map(this.output_mounts, m => {
          return {
            icon: _.get(m, 'icon', ''),
            templates: _.get(m, 'setup_template.templates', [])
          }
        })
      }
    },
    mounted() {
      var team_name = this.getActiveUsername()
      this.$store.dispatch('teams/changeActiveTeam', { team_name })

      if (this.is_quick_start) {
        // update the active step from the route
        this.active_step = 'addons'
      } else {
        // pre-select integrations
        this.selectIntegrationsFromRoute()

        // update the active step from the route
        this.active_step = _.get(this.$route, 'params.action', 'integrations')

        if (this.active_step == 'integration-setup') {
          this.fetchIntegrationConfig()
        }
      }

      // update title from route
      this.route_title = _.get(this.$route, 'query.title', '')
    },
    methods: {
      ...mapGetters('users', {
        'getActiveUsername': 'getActiveUsername'
      }),
      ...mapGetters('integrations', {
        'getProductionIntegrations': 'getProductionIntegrations',
      }),
      selectIntegrationsFromRoute() {
        _.each(this.integrations_from_route, cname => {
          var selected_integration = _.find(this.integrations, f => _.get(f, 'connection.name', '') == cname)
          if (selected_integration) {
            this.selected_integrations = this.selected_integrations.concat([selected_integration])
          }
        })
      },
      onUtilityButtonClick() {
        // reset our local component data
        _.assign(this.$data, getDefaultState())
        this.selectIntegrationsFromRoute()
        if (this.selected_integrations.length > 0) {
          this.onNextStepClick()
        }
      },
      onNextStepClick() {
        // we're on the last step; commit all changes to the backend and take the user to the app
        if (this.active_step_idx == this.step_order.length - 1) {
          this.submitOnboardingConfig(() => {
            // syncing can take a long time; end the onboarding
            // while the syncing is going on
            this.endOnboarding()
          })
          return
        }

        if (this.active_step == 'integrations' && this.selected_integrations.length == 0) {
          var msg = "Are you sure you'd like to continue without setting up an integration?"
          var title = 'Really continue without an integration?'

          this.$confirm(msg, title, {
            type: 'warning',
            confirmButtonClass: 'ttu fw6',
            cancelButtonClass: 'ttu fw6',
            confirmButtonText: 'Continue',
            cancelButtonText: 'Cancel',
            dangerouslyUseHTMLString: true,
          }).then(() => {
            // skip over integration set up if none were selected
            var next_idx = _.indexOf(this.step_order, 'addons')
            this.active_step = this.step_order[next_idx]
          })
        } else {
          // do this so we can check the next step before actually setting it
          // to be the active step (avoids flicker when moving directly to templates)
          var next_step = this.step_order[this.active_step_idx + 1]

          if (next_step == 'complete') {
            if (_.isString(this.gsheets_spreadsheet_id) || _.isString(this.excel_spreadsheet_path)) {
              var title = this.route_title.length > 0 ? this.route_title : undefined
              this.openTemplateChooserPage(this.gsheets_spreadsheet_id, this.excel_spreadsheet_path, title, false)
              return
            }
          }

          this.active_step = next_step

          if (this.active_step == 'integration-setup') {
            this.fetchIntegrationConfig()
          }

          if (this.active_step == 'complete') {
            this.custom_title = 'All systems go!'
          }
        }
      },
      onMemberInviteSubmit() {
        this.email_invites = []

        this.$message({
          message: "Your co-workers have been sent an invitation to join your team.",
          type: 'success'
        })
      },
      onNeedHelpClick() {
        if (window.Intercom) {
          window.Intercom('showNewMessage')
        }
      },
      onTemplateClick(template) {
        var gsheets_spreadsheet_id = _.get(template, 'gsheets_spreadsheet_id', undefined)
        var excel_spreadsheet_path = _.get(template, 'excel_spreadsheet_path', undefined)
        var title = _.get(template, 'title', undefined)

        this.openTemplateChooserPage(gsheets_spreadsheet_id, excel_spreadsheet_path, title, true)
      },
      onSelectedIntegrationChange(val, old_val) {
        if (old_val.length == 0 && val.length > 0) {
          this.onNextStepClick()
        }
      },
      fetchIntegrationConfig() {
        this.is_fetching_config = true

        var team_name = this.active_team_name
        var url = _.get(this.active_integration, 'connection.connection_info.url', '')

        api.fetchFunctionPackSetupTemplate(team_name, url).then(response => {
          this.active_setup_template = response.data
          this.is_fetching_config = false
        }).catch(error => {
          // TODO: this just skips the rest of the integration setup;
          //       this needs to be thought through more...
          this.active_setup_template = null
          this.active_step == 'addons'
          this.is_fetching_config = false
        })
      },
      processSetupConfig(setup_config, parent_eid, callback) {
        var team_name = this.active_team_name
        var setup_config = _.cloneDeep(setup_config)
        var xhrs = []

        // make sure we create/update any key values that are meant to be connections
        _.each(setup_config, (val, key) => {
          if (_.get(val, 'eid_type', '') == OBJECT_TYPE_CONNECTION) {
            var xhr
            var eid = _.get(val, 'eid', '')
            var attrs = _.omit(val, ['eid_type'])
            attrs.name = 'connection-' + getNameSuffix(4)
            attrs.parent_eid = parent_eid

            if (eid.length == 0) {
              // create the connection
              xhr = this.$store.dispatch('connections/create', { team_name, attrs })
            } else {
              // update the connection
              xhr = this.$store.dispatch('connections/update', { team_name, eid, attrs })
            }

            xhr.then(response => {
              // save only the minimal amount of information in the function mount (e.g. { eid, eid_type, connection_type })
              setup_config[key] = _.pick(response.data, ['eid', 'eid_type', 'connection_type'])
            })

            // store the promise so that we can issue our callback after all promises are resolved
            xhrs.push(xhr)
          }
        })

        // issue our callback when all of the above XHRs are finished
        axios.all(xhrs).then(responses => {
          callback(setup_config)
        })
      },
      saveIntegration(setup_config) {
        // if the builder item is an auth builder item, create the connection at this time
        // and then we can move forward to the next step
        this.processSetupConfig(setup_config, undefined, setup_config => {
          var setup_template = this.active_setup_template
          var mount = _.cloneDeep(_.get(this.active_integration, 'connection', {}))
          mount = _.assign({}, mount, { setup_config, setup_template })

          this.output_mounts = [].concat(this.output_mounts).concat([mount])

          if (this.active_integration_idx == this.selected_integrations.length - 1) {
            this.onNextStepClick()
          } else {
            this.active_integration_idx++
            this.fetchIntegrationConfig()
          }
        })
      },
      submitOnboardingConfig(successCb, errorCb) {
        var team_name = this.active_team_name
        var create_xhrs = []

        _.each(this.output_mounts, attrs => {
          var xhr = this.$store.dispatch('connections/create', { team_name, attrs })
          create_xhrs.push(xhr)
        })

        // create all of the function mounts
        axios.all(create_xhrs).then(responses => {
          var sync_xhrs = []

          // start syncing all of the function mounts
          _.each(responses, response => {
            var eid = _.get(response.data, 'eid', '')
            var setup_config = _.get(response.data, 'setup_config', {})
            var xhr = this.$store.dispatch('connections/sync', { team_name, eid })
            sync_xhrs.push(xhr)

            // make sure we add parent eids to all child connections if we need to
            this.processSetupConfig(setup_config, eid)
          })

          if (_.isFunction(successCb)) {
            successCb()
          }
        })
        .catch(error => {
          if (_.isFunction(errorCb)) {
            errorCb()
          }
        })
      },
      endOnboarding(next_path) {
        var team_name = this.active_team_name
        var path = _.isString(next_path) ? next_path : `/${team_name}/functions`

        this.$store.dispatch('teams/changeActiveTeam', { team_name }).then(response => {
          this.$router.push({ path })
        })
      },
      openTemplateChooserPage(gsheets_spreadsheet_id, excel_spreadsheet_path, title, open_new_window) {
        // TODO: *IF* we ever wanted to make this thing support adding
        //       multiple integrations at the same time again, we'd need
        //       to make sure this wasn't hard-coded like this
        var integration_name = this.is_quick_start ? 'quick-start' : _.get(this.output_mounts, '[0].name', '')

        var query_params = _.omitBy({
          gsheets_spreadsheet_id,
          excel_spreadsheet_path,
          title,
          context: 'app'
        }, _.isNil)

        var query_str = buildQueryString(query_params)

        var team_name = this.active_team_name
        var path = `/integrations/${integration_name}/template?${query_str}`
        var wnd_path = '/app' + path

        if (this.onboarding_config_submitted) {
          open_new_window === true ? window.open(wnd_path) : this.endOnboarding(path)
        } else {
          // submit the onboarding config and then go to the template download page
          this.submitOnboardingConfig(() => {
            this.onboarding_config_submitted = true
            open_new_window === true ? window.open(wnd_path) : this.endOnboarding(path)
          })
        }

      }
    }
  }
</script>
