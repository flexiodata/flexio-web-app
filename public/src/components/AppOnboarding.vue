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
          <el-step title="Invite Others" />
          <el-step title="Get Add-ons " />
        </el-steps>

        <!-- step: choose integrations -->
        <div v-if="active_step == 'choose-integrations'">
          <p>Let's get started with integrations. Pick one or more services below and we'll add a few out-of-the-box functions you can use in your spreadsheet.</p>
          <IconList
            class="mv4"
            :items="integrations"
            :selected-items.sync="selected_integrations"
            :allow-selection="true"
            :allow-multiple="true"
          />
        </div>

        <!-- step: set up integrations -->
        <div v-if="active_step == 'set-up-integrations' && has_active_setup_template">
          <FunctionMountSetupWizard
            :setup-template="active_setup_template"
            @submit="saveIntegration"
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
        <div v-if="active_step == 'invite-members'">
          <p>If you'd like, you can also share your functions with your team. Simply add their email addresses below and they'll get an invitation to join your team and use your functions. You can also skip this step and add people later in the <strong>Members</strong> tab.</p>
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
        <div v-if="active_step == 'install-add-ons'">
          <p>To use lookup functions in a spreadsheet, you just need to get the add-on for either Microsoft Excel or Google Sheets. Click on the spreadsheet you'd like to use below and then follow the instructions to install the add-on.</p>
          <p>Once you have the add-on, simply login with your Flex.io account and you can begin working with your Flex.io lookup functions.</p>
          <div class="flex flex-column flex-row-l mv3 nl3 nr3">
            <div class="flex-fill ma3 pa4 bg-nearer-white br3">
              <div class="flex flex-row items-center justify-center">
                <img src="../assets/icon/icon-google-sheets-128.png" alt="Google Sheets" style="height: 48px" />
                <div class="ml2 fw6 f4">Google Sheets</div>
              </div>
              <div class="center mw6 mt4">
                <a
                  href="https://chrome.google.com/webstore/detail/flexio/cklkghdhggmiooefncfkfgchgocceddj"
                  class="w-100 el-button el-button--default is-plain no-underline ttu fw6"
                  target="_blank"
                >
                  <span class="ph2">Get the Google Sheets add-on</span>
                </a>
              </div>
            </div>

            <div class="flex-fill ma3 pa4 bg-nearer-white br3">
              <div class="flex flex-row items-center justify-center">
                <img src="../assets/icon/icon-excel-128.png" alt="Microsoft Excel" style="height: 48px" />
                <div class="ml3 fw6 f4">Microsoft Excel</div>
              </div>
              <div class="center mw6 mt4">
                <a
                  href="https://appsource.microsoft.com/en-us/product/office/WA200000394?src=office"
                  class="w-100 el-button el-button--default is-plain no-underline ttu fw6"
                  target="_blank"
                >
                  <span class="ph2">Get the Excel add-in</span>
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- button bar for the entire onboarding wizard -->
        <ButtonBar
          class="mt5"
          :utility-button-type="'text'"
          :utility-button-visible="active_step_idx != step_order.length - 1"
          :utility-button-text="active_step_idx == 0 ? 'Skip setup' : '← Start over'"
          :cancel-button-visible="false"
          :submit-button-text="active_step_idx == step_order.length - 1 ? 'Done' : 'Continue'"
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
      onboarding_method: 'technical-user', // 'spreadsheet-user' or 'technical-user'
      active_step: 'choose-integrations',
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
       return ['choose-integrations', 'set-up-integrations', 'invite-members', 'install-add-ons']
      },
    },
    mounted() {
      var team_name = this.getActiveUsername()
      this.$store.dispatch('teams/changeActiveTeam', { team_name })

      // pre-select integrations
      var integrations = _.get(this.$route, 'query.integration', '')
      if (integrations.length > 0) {
        integrations = integrations.split(',')

        _.each(integrations, cname => {
          var selected_integration = _.find(this.integrations, f => _.get(f, 'connection.name', '') == cname)
          if (selected_integration) {
            this.selected_integrations = this.selected_integrations.concat([selected_integration])
          }
        })
      }
    },
    methods: {
      ...mapGetters('users', {
        'getActiveUsername': 'getActiveUsername'
      }),
      ...mapGetters('integrations', {
        'getProductionIntegrations': 'getProductionIntegrations',
      }),
      onUtilityButtonClick() {
        if (this.active_step_idx == 0) {
          this.$store.track('Skipped Onboarding')
          this.endOnboarding()
        } else {
          this.active_step = 'choose-integrations'
        }
      },
      onNextStepClick() {
        // we're on the last step; commit all changes to the backend and take the user to the app
        if (this.active_step_idx == this.step_order.length - 1) {
          this.submitOnboardingConfig()
          return
        }

        if (this.active_step == 'choose-integrations' && this.selected_integrations.length == 0) {
          // skip over integration set up if none were selected
          this.active_step = this.step_order[this.active_step_idx + 2]
        } else {
          this.active_step = this.step_order[this.active_step_idx + 1]
        }

        if (this.active_step == 'set-up-integrations') {
          this.fetchIntegrationConfig()
        }
      },
      onMemberInviteSubmit() {
        this.email_invites = []

        this.$message({
          message: "Your team members have been sent an invitation to join your team.",
          type: 'success'
        })
      },
      chooseOnboardingMethod(method) {
        this.onboarding_method = method
        this.active_step = method == 'spreadsheet-user' ? 'install-add-ons' : 'choose-integrations'
      },
      fetchIntegrationConfig() {
        var team_name = this.active_team_name
        var url = _.get(this.active_integration, 'connection.connection_info.url', '')

        api.fetchFunctionPackSetupTemplate(team_name, url).then(response => {
          this.active_setup_template = response.data
        }).catch(error => {
          // TODO: this just skips the rest of the integration setup;
          //       this needs to be thought through more...
          this.active_setup_template = null
          this.active_step == 'invite-members'
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
      endOnboarding() {
        var team_name = this.active_team_name

        this.$store.dispatch('teams/changeActiveTeam', { team_name }).then(response => {
          var new_route = _.pick(this.$route, ['name', 'meta', 'params', 'path', 'query'])
          this.$router.push({ path: `/${team_name}/pipes` })
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
