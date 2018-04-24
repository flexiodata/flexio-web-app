<template>
  <div id="app" class="flex flex-column fixed absolute--fill overflow-hidden">
    <AppNavbar class="flex-none" v-if="show_intercom_button && show_navbar" />
    <router-view class="flex-fill"></router-view>

    <!-- onboarding dialog -->
    <div v-if="config_show_onboarding">
      <el-dialog
        custom-class="no-header no-footer"
        width="56rem"
        top="8vh"
        :modal-append-to-body="false"
        :close-on-click-modal="false"
        :close-on-press-escape="false"
        :visible.sync="show_onboarding_modal"
      >
        <OnboardingPanel @close="onOnboardingClose" />
      </el-dialog>
    </div>

    <button
      id="open-intercom-inbox"
      class="fixed bottom-0 right-0 pointer darken-10 bn br-100 white bg-blue"
      style="z-index: 2147482000; padding: 12px; margin: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.4)"
      v-if="show_intercom_button"
    ><i class="material-icons md-24 relative" style="top: 1px">chat</i>
    </button>
  </div>
</template>

<script>
  import {
    ROUTE_EMBED,
    ROUTE_SIGNIN,
    ROUTE_SIGNUP,
    ROUTE_FORGOTPASSWORD,
    ROUTE_RESETPASSWORD,
    ROUTE_PIPES,
    ROUTE_HOME_LEARN
  } from '../constants/route'
  import { mapState, mapGetters } from 'vuex'
  import AppNavbar from './AppNavbar.vue'
  import OnboardingPanel from './OnboardingPanel.vue'

  export default {
    name: 'App',
    components: {
      AppNavbar,
      OnboardingPanel
    },
    data() {
      return {
        show_onboarding_modal: true
      }
    },
    computed: {
      ...mapState([
        'active_user_eid'
      ]),
      route_name() {
        return _.get(this.$route, 'name', '')

      },
      show_navbar() {
        return this.route_name == ROUTE_PIPES ? false : true
      },
      show_intercom_button() {
        switch (this.route_name)
        {
          case ROUTE_EMBED:
          case ROUTE_SIGNIN:
          case ROUTE_SIGNUP:
          case ROUTE_FORGOTPASSWORD:
          case ROUTE_RESETPASSWORD:
            return false

          // 404
          case null:
            return true
        }

        return true
      },
      config_show_onboarding() {
        // we have to do 'config_show_onboarding' as a computed value since
        // we need to wait to check if we have an active user or not
        if (this.active_user_eid.length == 0)
          return false

        // allow onboarding modal to be shown programmatically
        var params = _.get(this.$route, 'query', {})
        if (params['app.prompt.onboarding.shown'] === 'true')
          return true

        var cfg = _.get(this.getActiveUser(), 'config')
        if (this.show_intercom_button && _.get(cfg, 'app.prompt.onboarding.shown') !== true)
          return true

        return false
      }
    },
    methods: {
      ...mapGetters([
        'getActiveUser'
      ]),
      updateOnboardingConfig() {
        var cfg = _.get(this.getActiveUser(), 'config', {})
        if (_.isArray(cfg))
          cfg = {}
        cfg['app.prompt.onboarding.shown'] = true

        this.$store.dispatch('updateUser', { eid: this.active_user_eid, attrs: { config: cfg } })
      },
      onOnboardingClose() {
        this.show_onboarding_modal = false

        setTimeout(() => {
          if (this.$route.name != ROUTE_HOME_LEARN)
            this.$router.push({ name: ROUTE_HOME_LEARN })

          this.updateOnboardingConfig()
        }, 10)
      }
    }
  }
</script>

<style lang="less">
  // match .blue color to Material Design's 'Blue A600' color
  @blue: #1e88e5;
  @black-60: rgba(0,0,0,0.6);
  @bg-color: #eee;
  @bg-near-white: #f4f4f4;

  .css-nav-item {
    border-color: transparent;
    color: @black-60;

    &:hover {
      border-color: @black-60;
      color: #222;
    }

    &.router-link-active {
      border-color: @blue;
      color: @blue;
    }
  }

  .css-list-item {
    border-color: rgba(0,0,0,0.1);

    &:hover {
      background-color: @bg-near-white;
      border-color: rgba(0,0,0,0.1);
      border-bottom-color: rgba(0,0,0,0.15);
    }
  }

  .css-list-item:not(.css-trash-item):hover .css-list-title {
    color: @blue;
  }
</style>
