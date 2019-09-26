<template>
  <div class="flex flex-column bg-nearer-white overflow-y-scroll">
    <div class="ph4 pv5">
      <div
        class="w-100 center mw-doc pa4 bg-white br2 css-white-box overflow-hidden"
        style="margin-bottom: 15rem"
      >

        <!-- step 1: welcome -->
        <div v-if="active_step == 'welcome'">
          <h1 class="fw6 f2 tc">Welcome to Flex.io!</h1>
          <p class="center mw7">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Exercitationem corporis accusantium, blanditiis nostrum unde dolores totam iste. Blanditiis voluptates consectetur laudantium, repudiandae voluptatibus ducimus fugit rem sequi, corporis nesciunt quas?</p>
          <p class="center mw7">What would you like to do today?</p>
          <div class="flex flex-row center mw7">
            <div
              class="onboarding-big-button"
              :class="{
                'active': onboarding_method == 'spreadsheet-user'
              }"
              @click="chooseOnboardingMethod('spreadsheet-user')"
            >
              <h4>I'd like to join a team.</h4>
              <p class="tl mt1 mb0">Choose this option if you are primarily a spreadsheet user and have already been invited to join someone's team.</p>
            </div>
            <div
              class="onboarding-big-button"
              :class="{
                'active': onboarding_method == 'technical-user'
              }"
              @click="chooseOnboardingMethod('technical-user')"
            >
              <h4>I'd like to build functions.</h4>
              <p class="tl mt1 mb0">Choose this option if you are primarily a technical user or developer and would like to build functions to share with your team.</p>
            </div>
          </div>
        </div>

        <!-- step: install add-ons -->
        <div v-if="active_step == 'install-add-ons'">
          <h3 class="fw6 f3 tc">Get Add-Ons</h3>
          <p>Welcome aboard! To join your team, install the add-on for either Microsoft Excel or Google Sheets below. After installing the add-on, sign in with your Flex.io account, select the team name and begin working with your functions.</p>
          <p>[icon] Google Sheets</p>
          <p>[GET THE GOOGLE SHEETS ADD-ON]</p>
          <p>[icon] Microsoft Excel 365</p>
          <p>[GET THE EXCEL ADD-ON]</p>
        </div>

        <div v-if="active_step != 'welcome' && onboarding_method == 'technical-user'">
          <el-steps
            class="mt3 mb4 pb2"
            align-center
            finish-status="success"
            :active="active_step_idx - 1"
          >
            <el-step title="Choose Integrations" />
            <el-step title="Set Up" />
            <el-step title="Invite Others" />
            <el-step title="Get Add-ons " />
          </el-steps>

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
        </div>

        <ButtonBar
          class="mt4"
          :utility-button-visible="true"
          :utility-button-type="'text'"
          :utility-button-text="'Skip setup'"
          :cancel-button-visible="active_step != 'welcome'"
          :submit-button-visible="active_step != 'welcome'"
          :cancel-button-text="'Back'"
          :submit-button-text="active_step_idx == step_order.length - 1 ? 'Done' : 'Continue'"
          @utility-click="onSkipClick"
          @cancel-click="onBackClick"
          @submit-click="onNextClick"
        />
     </div>
      </div>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import ButtonBar from '@/components/ButtonBar'
  import IconList from '@/components/IconList'

  const getDefaultState = () => {
    return {
      active_step: 'welcome',
      onboarding_method: '', // 'spreadsheet-user' or 'technical-user'
      selected_integrations: []
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
      IconList
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
    mounted() {
      this.$store.dispatch('integrations/fetch')
    },
    methods: {
      ...mapGetters('users', {
        'getActiveUsername': 'getActiveUsername'
      }),
      ...mapGetters('integrations', {
        'getProductionIntegrations': 'getProductionIntegrations',
      }),
      onSkipClick() {
        var team_name = this.getActiveUsername()

        this.$store.dispatch('teams/changeActiveTeam', { team_name }).then(response => {
          var new_route = _.pick(this.$route, ['name', 'meta', 'params', 'path', 'query'])
          this.$router.push({ path: `/${team_name}/pipes` })
        })
      },
      onBackClick() {
        this.active_step = this.step_order[this.active_step_idx - 1]
      },
      onNextClick() {
        this.active_step = this.step_order[this.active_step_idx + 1]
      },
      chooseOnboardingMethod(method) {
        this.onboarding_method = method
        this.active_step = method == 'spreadsheet-user' ? 'install-add-ons' : 'choose-integrations'
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
      padding-right: 12px
</style>
