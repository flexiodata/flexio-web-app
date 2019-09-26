<template>
  <div class="flex flex-column bg-nearer-white overflow-y-scroll">
    <div class="ph4 pv5">
      <div
        class="w-100 center mw-doc pa4 bg-white br2 css-white-box overflow-hidden"
        style="margin-bottom: 15rem"
      >

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
        <div v-else>
          <!-- join a team steps -->
          <div v-if="onboarding_method == 'spreadsheet-user'">
            <div v-if="active_step == 'add-in-links'">
              Show blurb and links to add-ins here...
            </div>
          </div>

          <!-- build functions steps -->
          <div v-if="onboarding_method == 'technical-user'">
            <el-steps
              class="mb4 pb2"
              align-center
              finish-status="success"
              :active="0"
            >
              <el-step title="Choose Integrations" />
              <el-step title="Set Up" />
              <el-step title="Invite Your Team" />
              <el-step title="Get Add-ons " />
            </el-steps>
            <h3 class="fw6 f3 tc">Choose Your Integrations</h3>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Tenetur ipsum a eaque odit ut magnam architecto voluptate quae commodi optio quisquam praesentium illo natus dolor assumenda doloremque, suscipit deserunt nostrum?</p>
            <p>Please select the integrations you would like to add. Once you have selected all of the integrations you would like to add, click the <strong>Continue</strong> button below to continue with the setup process.</p>
            <IconList
              class="mv4"
              :items="integrations"
              v-if="active_step == 'choose-onboarding-items'"
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
          :submit-button-text="'Continue'"
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

      },
      onNextClick() {

      },
      chooseOnboardingMethod(method) {
        this.onboarding_method = method
        this.active_step = method == 'spreadsheet-user' ? 'add-in-links' : 'choose-onboarding-items'
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
