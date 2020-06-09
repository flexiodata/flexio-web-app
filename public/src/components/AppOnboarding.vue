<template>
  <main class="pv4 pv5-ns ph3 ph5-ns bg-nearer-white overflow-y-scroll">
    <div class="mt4">
      <div
        class="w-100 center mw-doc pt4 pb5 ph5 bg-white br2 css-white-box"
        style="margin-bottom: 15rem"
      >
        <div class="tc" style="margin-top: -76px">
          <img src="../assets/logo-square-80x80.png" alt="Flex.io" class="br-100 ba bw1 b--white" style="width: 84px; box-shadow: 0 0 3px rgba(0,0,0,0.4)">
        </div>

        <h1 class="fw6 f2 tc pb2">{{display_title}}</h1>

        <!-- step heading -->
        <el-steps
          class="mv4 pv2"
          align-center
          finish-status="success"
          :active="step_heading_idx"
        >
          <el-step title="Set Up Integration" />
          <el-step title="Get Add-on" />
          <el-step title="Get Started" />
        </el-steps>

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
        <div v-if="active_step == 'setup'">
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
              <p class="mb0 f7 i light-silver lh-copy">NOTE: This process may take a couple of minutes to complete</p>
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
          <p class="center mw7">You're almost done! If you haven't done so already, please install the Flex.io Add-on for <span class="nowrap">Google Sheets</span> or <span class="nowrap">Microsoft Excel 365</span>. Once you've installed the add-on, you'll see the functions in the Flex.io sidebar and will be able to use them in your spreadsheet.</p>
          <AddonDownloadButtonPanel
            class="pv2-l"
            :open-links-in-new-window="true"
          />
          <p class="center mw7 f8 silver">* The Microsoft Excel 365 add-in will only function with an Excel for Office 365 subscription</p>
        </div>

        <div v-if="active_step == 'complete'">
          <p class="center mw7">You're all set and ready to go. Here's a set of example spreadsheets and other resources to help get you started. If you need anything, please let us know!</p>
          <template v-if="all_templates.length > 0 ">
            <h3 class="mt5 mb4 tc">Select an example sheet</h3>
            <TemplateList
              :icon="t.icon"
              :templates="t.templates"
              @template-click="onTemplateClick"
              v-for="t in all_templates"
            />
          </template>
          <h3 class="mt5 mb4 tc">Get additional resources</h3>
          <div class="flex-l flex-row-l flex-wrap-l nl3 nr3">
            <a
              class="flex-fill flex flex-row db ma3 pv3 ph4 br2 fw4 dark-gray no-underline w-third-l last-step-item"
              target="_blank"
              :href="item.link"
              :key="item.title"
              v-for="item in last_step_items"
            >
              <div>
                <h3 class="mt2 mb0 fw6">{{item.title}}</h3>
                <p class="flex-fill f6 lh-copy mv3">{{item.description}}</p>
                <div class="mb3">
                  <span class="flex flex-row items-center blue fw6">
                    <span>View</span>
                    <i class="material-icons ml1">arrow_right_alt</i>
                  </span>
                </div>
              </div>
            </a>
          </div>
          <h3 class="mt5 mb4 tc">Get started with the Flex.io App</h3>
          <p class="center mw7">Go off road. Access just about any data with your own code and share integrations with your team without sharing your credentials.</p>
          <div class="mv4 tc">
            <button
              class="db dib-ns pv3 ph5 tc b ttu blue br2 ba b--blue hover-bg-blue hover-white"
              @click="onNextStepClick"
            >
              Open the Flex App
            </button>
          </div>
          <p class="tc">Want some code examples? Fork one of our open source function packs from GitHub: <a class="ml1 v-mid" href="https://github.com/flexiodata?utf8=%E2%9C%93&q=functions" target="_blank" title="View function packs on Github"><svg xmlns="http://www.w3.org/2000/svg" fill="black" width="32" height="32" viewBox="0 0 16 16"><path d="M8 .198c-4.418 0-8 3.582-8 8 0 3.535 2.292 6.533 5.47 7.59.4.075.548-.173.548-.384 0-.19-.008-.82-.01-1.49-2.227.485-2.696-.943-2.696-.943-.364-.924-.888-1.17-.888-1.17-.726-.497.055-.486.055-.486.802.056 1.225.824 1.225.824.714 1.223 1.872.87 2.328.665.072-.517.28-.87.508-1.07-1.776-.202-3.644-.888-3.644-3.954 0-.874.313-1.588.824-2.148-.083-.202-.357-1.015.077-2.117 0 0 .672-.215 2.2.82.64-.177 1.323-.266 2.003-.27.68.004 1.365.093 2.004.27 1.527-1.035 2.198-.82 2.198-.82.435 1.102.162 1.916.08 2.117.512.56.822 1.274.822 2.147 0 3.072-1.872 3.748-3.653 3.946.288.248.544.735.544 1.48 0 1.07-.01 1.933-.01 2.196 0 .213.145.462.55.384 3.178-1.06 5.467-4.057 5.467-7.59 0-4.418-3.58-8-8-8z"></path></svg></a></p>

          <p class="tc">
            <span>Need help with a script?</span>
            <el-button type="text" style="font-size: 100%" @click="onNeedHelpClick">Let us know and we'll point you in the right direction!</el-button>
          </p>
        </div>

        <!-- button bar for the entire onboarding wizard -->
        <ButtonBar
          class="mt5"
          :utility-button-type="'text'"
          :utility-button-visible="is_start_over_button_visible"
          :utility-button-text="active_step_idx == 1 ? '← Back' : '← Start over'"
          :cancel-button-visible="false"
          :submit-button-visible="active_step != 'setup' && active_step != 'complete'"
          :submit-button-text="active_step_idx == 0 ? 'Skip this step' : 'Continue'"
          @utility-click="onUtilityButtonClick"
          @submit-click="onNextStepClick"
          v-if="active_step_idx < step_order.length - 1"
        />
     </div>
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
  import { HOSTNAME } from '@/constants/common'
  import { buildQueryString } from '@/utils'
  import ButtonBar from '@/components/ButtonBar'
  import IconList from '@/components/IconList'
  import FunctionMountSetupWizard from '@/components/FunctionMountSetupWizard'
  import ServiceIconWrapper from '@/components/ServiceIconWrapper'
  import AddonDownloadButtonPanel from '@/components/AddonDownloadButtonPanel'
  import MemberInvitePanel from '@/components/MemberInvitePanel'
  import TemplateList from '@/components/TemplateList'

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
      active_step: 'integrations', // 'integrations', 'setup', 'addons', 'members'
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
      MemberInvitePanel,
      TemplateList
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
      step_order() {
       return ['integrations', 'setup', 'integration-setup-complete', 'addons', 'complete']
      },
      // this is just for display purposes
      step_heading_idx() {
        switch (this.active_step_idx) {
          case 0: return 0
          case 1: return 0
          case 2: return 0
          case 3: return 1
          case 4: return 2
        }
        return 0
      },
      has_active_setup_template() {
        return !_.isNil(this.active_setup_template)
      },
      integrations_from_route() {
        var integrations = _.get(this.$route, 'query.integration', '')
        return integrations.length > 0 ? integrations.split(',') : []
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
      all_templates() {
        return _.map(this.output_mounts, m => {
          return {
            icon:  _.get(m, 'icon', ''),
            templates: _.get(m, 'setup_template.templates', [])
          }
        })
      }
    },
    mounted() {
      var team_name = this.getActiveUsername()
      this.$store.dispatch('teams/changeActiveTeam', { team_name })

      // pre-select integrations
      this.selectIntegrationsFromRoute()

      // update the active step from the route
      this.active_step = _.get(this.$route, 'params.action', 'integrations')

      if (this.active_step == 'setup') {
        this.fetchIntegrationConfig()
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
            this.$nextTick(() => { this.onNextStepClick() })
          }
        })
      },
      onUtilityButtonClick() {
        // reset our local component data
        _.assign(this.$data, getDefaultState())
        this.selectIntegrationsFromRoute()

        /*
        if (this.active_step_idx == 0) {
          var msg = "Stepping through this setup can help you quickly get started using Flex.io. Are you sure you want to skip setup?"
          var title = 'Really skip setup?'

          this.$confirm(msg, title, {
            type: 'warning',
            confirmButtonClass: 'ttu fw6',
            cancelButtonClass: 'ttu fw6',
            confirmButtonText: 'Continue',
            cancelButtonText: 'Cancel',
            dangerouslyUseHTMLString: true,
          }).then(() => {
            this.$store.track('Skipped Onboarding')
            this.endOnboarding()
          })
        } else {
          var msg = "Looks like you want to start over; if so, you will lose any configuration details you've entered. Would you like to continue?"
          var title = 'Really start over?'

          this.$confirm(msg, title, {
            type: 'warning',
            confirmButtonClass: 'ttu fw6',
            cancelButtonClass: 'ttu fw6',
            confirmButtonText: 'Continue',
            cancelButtonText: 'Cancel',
            dangerouslyUseHTMLString: true,
          }).then(() => {
            // reset our local component data
            _.assign(this.$data, getDefaultState())
            this.selectIntegrationsFromRoute()
          })
        }
        */
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
          this.active_step = this.step_order[this.active_step_idx + 1]

          if (this.active_step == 'setup') {
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
        // TODO: *IF* we ever wanted to make this thing support adding
        //       multiple integrations at the same time again, we'd need
        //       to make sure this wasn't hard-coded like this
        var integration_name = _.get(this.output_mounts, '[0].name', '')

        var gsheets_spreadsheet_id = _.get(template, 'gsheets_spreadsheet_id', undefined)
        var excel_spreadsheet_path = _.get(template, 'excel_spreadsheet_path', undefined)

        var query_str = buildQueryString({
          gsheets_spreadsheet_id,
          excel_spreadsheet_path,
          context: 'app',
          title: 'TODO',
          description: 'TODO'
        })

        var url = `https://${HOSTNAME}/integrations/${integration_name}/template?${query_str}`

        if (this.onboarding_config_submitted) {
          window.open(url)
        } else {
          // submit the onboarding config and then go to the template download page
          this.submitOnboardingConfig(() => {
            this.onboarding_config_submitted = true
            window.open(url)
          })
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
      endOnboarding() {
        var team_name = this.active_team_name

        this.$store.dispatch('teams/changeActiveTeam', { team_name }).then(response => {
          var new_route = _.pick(this.$route, ['name', 'meta', 'params', 'path', 'query'])
          this.$router.push({ path: `/${team_name}/functions` })
        })
      }
    }
  }
</script>

<style lang="stylus">
  .last-step-item
    background-color: rgba(255,255,255,0.2)
    box-shadow: 0 8px 24px -1px rgba(0,0,0,0.075)
    transition: all 0.2s ease-out
    &:hover
      box-shadow: 0 8px 24px 0 rgba(0,0,0,0.2)
</style>
