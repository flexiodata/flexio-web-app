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

        <h1 class="fw6 f2 tc pb2">Welcome to Flex.io!</h1>

        <!-- step heading -->
        <el-steps
          class="mv4 pv2"
          align-center
          finish-status="success"
          :active="active_step_idx"
          v-show="active_step_idx > 0"
        >
          <el-step title="Choose Integrations" />
          <el-step title="Set Up" />
          <el-step title="Get Add-ons " />
          <el-step title="Invite (optional)" />
        </el-steps>

        <!-- step: choose integrations -->
        <div v-if="active_step == 'integrations'">
          <p class="center mw7">Let's get started with integrations. Pick one or more services below and we'll add a few out-of-the-box functions you can use in your spreadsheet.</p>
          <IconList
            class="mv4"
            :items="integrations"
            :selected-items.sync="selected_integrations"
            :allow-selection="true"
            :allow-multiple="true"
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
              <div class="tc f6 fw4 lh-copy moon-gray"><em>No configuration is required for this integration.</em></div>
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
          <p class="center mw7">You're almost done! The last step is to install the Flex.io Add-on for either Excel 365 or Google Sheets. Once you've installed an add-on, you'll see the functions in your spreadsheet.</p>
          <div class="flex flex-column flex-row-l mv3 nl3 nr3">
            <div class="flex-fill mv4 mh3 pa4 bg-nearer-white br3">
              <div class="flex flex-row items-center justify-center">
                <img src="../assets/icon/icon-google-sheets-128.png" alt="Google Sheets" style="height: 48px" />
                <div class="ml2 fw6 f4">Google Sheets</div>
              </div>
              <div class="center mw6 mt4">
                <el-button
                  class="w-100 ttu fw6"
                  plain
                  @click="openGoogleSheetsAddonDownload"
                >
                  <span class="ph2">Get the Google Sheets add-on</span>
                </el-button>
              </div>
            </div>

            <div class="flex-fill mv4 mh3 pa4 bg-nearer-white br3">
              <div class="flex flex-row items-center justify-center">
                <img src="../assets/icon/icon-excel-128.png" alt="Microsoft Excel" style="height: 48px" />
                <div class="ml3 fw6 f4">Microsoft Excel 365 *</div>
              </div>
              <div class="center mw6 mt4">
                <el-button
                  class="w-100 ttu fw6"
                  plain
                  @click="openExcelAddonDownload"
                >
                  <span class="ph2">Get the Excel add-in</span>
                </el-button>
              </div>
            </div>
          </div>
          <p class="center mw7 f8 nt3">* The Microsoft Excel 365 add-in will only function with an Excel for Office 365 subscription (currently on Targeted and Insider channels and soon for all Excel 365 users).</p>
        </div>

        <!-- button bar for the entire onboarding wizard -->
        <ButtonBar
          class="mt5"
          :utility-button-type="'text'"
          :utility-button-visible="active_step_idx != step_order.length - 1"
          :utility-button-text="active_step_idx == 0 ? 'Skip setup' : '← Start over'"
          :cancel-button-visible="false"
          :submit-button-disabled="active_step == 'addons' && !has_clicked_get_addon_button"
          :submit-button-visible="active_step != 'setup'"
          :submit-button-text="active_step_idx == step_order.length - 1 ? 'Finish Setup' : 'Continue'"
          @utility-click="onUtilityButtonClick"
          @submit-click="onNextStepClick"
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
  import ButtonBar from '@/components/ButtonBar'
  import IconList from '@/components/IconList'
  import FunctionMountSetupWizard from '@/components/FunctionMountSetupWizard'
  import ServiceIconWrapper from '@/components/ServiceIconWrapper'
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
      has_clicked_get_addon_button: false,
      onboarding_method: 'technical-user', // 'spreadsheet-user' or 'technical-user'
      active_step: 'integrations',
      active_integration_idx: 0,
      active_setup_template: null,
      selected_integrations: [],
      output_mounts: [],
      email_invites: []
    }
  }

  export default {
    metaInfo() {
      return {
        title: 'Welcome to Flex.io',
        titleTemplate: (chunk) => {
          return chunk
        }
      }
    },
    components: {
      Spinner,
      ButtonBar,
      IconList,
      FunctionMountSetupWizard,
      ServiceIconWrapper,
      MemberInvitePanel,
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
      has_active_setup_template() {
        return !_.isNil(this.active_setup_template)
      },
      step_order() {
       return ['integrations', 'setup', 'addons', 'members']
      },
      integrations_from_route() {
        var integrations = _.get(this.$route, 'query.integration', '')
        return integrations.length > 0 ? integrations.split(',') : []
      },
      is_welcome() {
        return this.integrations_from_route.length == 0
      }
    },
    mounted() {
      var team_name = this.getActiveUsername()
      this.$store.dispatch('teams/changeActiveTeam', { team_name })

      // pre-select integrations
      this.selectIntegrationsFromRoute()

      // make sure the route is in sync
      this.setRoute(this.active_step)
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
      setRoute(action) {
        // update the route
        var current_action = _.get(this.$route, 'params.action', '')
        var new_route = _.pick(this.$route, ['name', 'meta', 'params', 'path', 'query'])
        new_route.params = _.assign({}, new_route.params, { action })
        this.$router[current_action.length == 0 ? 'replace' : 'push'](new_route)
      },
      onUtilityButtonClick() {
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

            // make sure the route is in sync
            this.setRoute(this.active_step)
          })
        }
      },
      onNextStepClick() {
        // we're on the last step; commit all changes to the backend and take the user to the app
        if (this.active_step_idx == this.step_order.length - 1) {
          this.submitOnboardingConfig()
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
            this.active_step = this.step_order[this.active_step_idx + 2]
          })
        } else {
          this.active_step = this.step_order[this.active_step_idx + 1]
        }

        if (this.active_step == 'setup') {
          this.fetchIntegrationConfig()
        }

        // make sure the route is in sync
        this.setRoute(this.active_step)
      },
      onMemberInviteSubmit() {
        this.email_invites = []

        this.$message({
          message: "Your co-workers have been sent an invitation to join your team.",
          type: 'success'
        })
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
      processSetupConfig(setup_config, callback) {
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

            if (eid.length == 0) {
              // create the connection
              xhr = this.$store.dispatch('connections/create', { team_name, attrs })
            } else {
              // update the connection
              xhr = this.$store.dispatch('connections/update', { team_name, eid, attrs })
            }

            xhr.then(response => {
              // save only the minimal amount of information in the function mounth (e.g. { eid, eid_type, connection_type })
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
        this.processSetupConfig(setup_config, setup_config => {
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
      submitOnboardingConfig() {
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
            var xhr = this.$store.dispatch('connections/sync', { team_name, eid })
            sync_xhrs.push(xhr)
          })

          // syncing can take a long time; end the onboarding
          // while the syncing is going on
          this.endOnboarding()
        })
        .catch(error => {

        })
      },
      openGoogleSheetsAddonDownload() {
        this.$store.track('Clicked Google Sheets Add-on in Onboarding')
        this.has_clicked_get_addon_button = true
        window.open('https://chrome.google.com/webstore/detail/flexio/cklkghdhggmiooefncfkfgchgocceddj', '_blank')
      },
      openExcelAddonDownload() {
        this.$store.track('Clicked Excel Add-in in Onboarding')
        this.has_clicked_get_addon_button = true
        window.open('https://appsource.microsoft.com/en-us/product/office/WA200000394?src=office', '_blank')
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

<style lang="stylus" scoped>
  @import '../stylesheets/variables.styl'

  .onboarding-big-button
    cursor: pointer
    padding: 28px 24px
    margin: 12px
    border: 2px solid rgba(0,0,0,0.05)
    &.active
    &:hover
      border-color: $blue
    h4
      font-weight: 600
      margin-top: 0
    p
      font-size: 14px
</style>
