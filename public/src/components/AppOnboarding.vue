<template>
  <main class="pa3 ph3-m pa5-ns bg-nearer-white overflow-y-scroll">
    <div class="mt4">
      <div
        class="w-100 center mw-doc pt4 pb5 ph5 bg-white br2 css-white-box"
        style="margin-bottom: 15rem"
      >
        <div class="tc" style="margin-top: -76px">
          <img src="../assets/logo-square-80x80.png" alt="Flex.io" class="br-100 ba bw1 b--white" style="width: 84px; box-shadow: 0 0 3px rgba(0,0,0,0.4)">
        </div>

        <!-- step: welcome -->
        <div v-if="active_step == 'welcome'">
          <h1 class="fw6 f2 tc">Welcome to Flex.io!</h1>
          <p class="center mw7">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Exercitationem corporis accusantium, blanditiis nostrum unde dolores totam iste. Blanditiis voluptates consectetur laudantium, repudiandae voluptatibus ducimus fugit rem sequi, corporis nesciunt quas?</p>
          <p class="center mw7">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Reiciendis aut ut dicta fuga id nobis qui, dignissimos totam neque rerum perspiciatis ratione rem nemo in! Distinctio corporis nobis, illo veniam.</p>
          <div v-if="false">
            <p>What would you like to do today?</p>
            <div class="flex flex-row center">
              <div
                class="flex-fill onboarding-big-button"
                :class="{ 'active': onboarding_method == 'spreadsheet-user' }"
                @click="chooseOnboardingMethod('spreadsheet-user')"
              >
                <h4>I'd like to join a team.</h4>
                <p class="tl mt1 mb0">Choose this option if you are primarily a spreadsheet user and have already been invited to join someone's team.</p>
              </div>
              <div
                class="flex-fill onboarding-big-button"
                :class="{ 'active': onboarding_method == 'technical-user' }"
                @click="chooseOnboardingMethod('technical-user')"
              >
                <h4>I'd like to build functions.</h4>
                <p class="tl mt1 mb0">Choose this option if you are primarily a technical user or developer and would like to build functions to share with your team.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- step heading when going down the "technical user" route (e.g. selecting integrations, etc.) -->
        <el-steps
          class="mv4 pb2"
          align-center
          finish-status="success"
          :active="active_step_idx - 1"
          v-if="active_step != 'welcome' && onboarding_method == 'technical-user'"
        >
          <el-step title="Choose Integrations" />
          <el-step title="Set Up" />
          <el-step title="Invite Others" />
          <el-step title="Get Add-ons " />
        </el-steps>

        <!-- step: install add-ons -->
        <div v-if="active_step == 'install-add-ons'">
          <h3 class="fw6 f3 tc">Get Add-Ons</h3>
          <p>Welcome aboard! To join your team, install the add-on for either Microsoft Excel or Google Sheets below. After installing the add-on, sign in with your Flex.io account, select the team name and begin working with your functions.</p>
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

        <!-- step: choose integrations -->
        <div v-if="active_step == 'choose-integrations'">
          <h3 class="fw6 f3 tc">Choose Your Integrations</h3>
          <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Tenetur ipsum a eaque odit ut magnam architecto voluptate quae commodi optio quisquam praesentium illo natus dolor assumenda doloremque, suscipit deserunt nostrum?</p>
          <p>Please select the integrations you would like to add. Once you have selected all of the integrations you would like to add, click the <strong>Continue</strong> button below to continue with the setup process.</p>
          <IconList
            class="mt4 mb5"
            :items="integrations"
            :selected-items.sync="selected_integrations"
            :allow-selection="true"
            :allow-multiple="true"
          />
        </div>

        <!-- step: set up integrations -->
        <FunctionMountConfigWizard
          :manifest="active_manifest"
          @submit="saveIntegration"
          v-if="active_step == 'set-up-integrations' && has_active_manifest"
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
        </FunctionMountConfigWizard>

        <!-- step: invite others -->
        <ServiceIconWrapper
          v-if="active_step == 'invite-members'"
        >
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

        <!-- button bar for the entire onboarding wizard -->
        <ButtonBar
          class="mt5"
          :utility-button-visible="true"
          :utility-button-type="'text'"
          :utility-button-text="'Skip setup'"
          :cancel-button-visible="cancel_button_visible"
          :submit-button-visible="submit_button_visible"
          :cancel-button-text="'Back'"
          :submit-button-text="active_step_idx == step_order.length - 1 ? 'Done' : 'Continue'"
          @utility-click="onSkipSetupClick"
          @cancel-click="onPrevStepClick"
          @submit-click="onNextStepClick"
        />
     </div>
      </div>
  </main>
</template>

<script>
  import axios from 'axios'
  import { mapGetters } from 'vuex'
  import api from '@/api'
  import ButtonBar from '@/components/ButtonBar'
  import IconList from '@/components/IconList'
  import FunctionMountConfigWizard from '@/components/FunctionMountConfigWizard'
  import ServiceIconWrapper from '@/components/ServiceIconWrapper'
  import MemberInvitePanel from '@/components/MemberInvitePanel'

  const getDefaultState = () => {
    return {
      onboarding_method: 'technical-user', // 'spreadsheet-user' or 'technical-user'
      active_step: 'welcome',
      active_integration_idx: 0,
      active_manifest: null,
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
      FunctionMountConfigWizard,
      ServiceIconWrapper,
      MemberInvitePanel,
    },
    data() {
      return getDefaultState()
    },
    computed: {
      integrations() {
        return this.getProductionIntegrations()
      },
      active_step_idx() {
        return _.indexOf(this.step_order, this.active_step)
      },
      active_integration() {
        return this.selected_integrations[this.active_integration_idx]
      },
      has_active_manifest() {
        return !_.isNil(this.active_manifest)
      },
      cancel_button_visible() {
        if (this.active_step == 'set-up-integrations') {
          return false
        }

        return this.active_step_idx == 1 || (this.active_step == 'invite-members' && this.selected_integrations.length == 0)
      },
      submit_button_visible() {
        return this.active_step != 'set-up-integrations'
      },
      step_order() {
        switch (this.onboarding_method) {
          case 'spreadsheet-user':
            return ['welcome', 'install-add-ons']
          case 'technical-user':
            return ['welcome', 'choose-integrations', 'set-up-integrations', 'invite-members', 'install-add-ons']
        }

        return ['welcome']
      },
    },
    methods: {
      ...mapGetters('users', {
        'getActiveUsername': 'getActiveUsername'
      }),
      ...mapGetters('integrations', {
        'getProductionIntegrations': 'getProductionIntegrations',
      }),
      onSkipSetupClick() {
        this.endOnboarding()
      },
      onPrevStepClick() {
        if (this.active_step == 'invite-members' && this.selected_integrations.length == 0) {
          // skip back over integration set up if none were selected
          this.active_step = this.step_order[this.active_step_idx - 2]
        } else {
          this.active_step = this.step_order[this.active_step_idx - 1]
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
        var team_name = this.getActiveUsername()
        var url = _.get(this.active_integration, 'connection.connection_info.url', '')

        api.fetchFunctionPackConfig(team_name, url).then(response => {
          var setup_template = response.data
          var prompts = _.get(setup_template, 'prompts', [])
          this.active_manifest = _.assign({}, setup_template)
        })
      },
      saveIntegration(setup_config) {
        var setup_template = this.active_manifest
        var mount = _.cloneDeep(_.get(this.active_integration, 'connection', {}))
        mount = _.assign({}, mount, { setup_config, setup_template })

        this.output_mounts = [].concat(this.output_mounts).concat([mount])

        if (this.active_integration_idx == this.selected_integrations.length - 1) {
          this.onNextStepClick()
        } else {
          this.active_integration_idx++
          this.fetchIntegrationConfig()
        }
      },
      submitOnboardingConfig() {
        var team_name = this.getActiveUsername()
        var xhrs = []

        _.each(this.output_mounts, attrs => {
          var xhr = this.$store.dispatch('connections/create', { team_name, attrs })
          xhrs.push(xhr)
        })

        // once all of the mounts have been created start syncing all of them
        // and take the user to the pipes area
        axios.all(xhrs)
        .then(axios.spread(responses => {
          this.endOnboarding()
        }))
        .catch(error => {

        })
      },
      endOnboarding() {
        var team_name = this.getActiveUsername()

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
