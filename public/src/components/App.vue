<template>
  <div id="app" class="flex flex-column fixed absolute--fill overflow-hidden">
    <app-navbar class="flex-none" v-if="show_intercom_button"></app-navbar>
    <router-view class="flex-fill"></router-view>

    <!-- onboarding modal -->
    <onboarding-modal
      ref="modal-onboarding"
      @hide="show_onboarding_modal = false"
      v-if="config_show_onboarding && show_onboarding_modal"
    ></onboarding-modal>

    <button
      id="open-intercom-inbox"
      class="fixed bottom-0 right-0 pointer darken-10 bn br-100 white bg-blue"
      style="z-index: 2147482000; padding: 12px; margin: 20px"
      v-if="show_intercom_button"
    ><i class="material-icons md-24 relative" style="top: 1px">chat</i>
    </button>
  </div>
</template>

<script>
  import {
    ROUTE_EMBEDHOME,
    ROUTE_SIGNIN,
    ROUTE_SIGNUP,
    ROUTE_FORGOTPASSWORD,
    ROUTE_RESETPASSWORD
  } from '../constants/route'
  import { mapState, mapGetters } from 'vuex'
  import AppNavbar from './AppNavbar.vue'
  import OnboardingModal from './OnboardingModal.vue'

  export default {
    name: 'app',
    components: {
      AppNavbar,
      OnboardingModal
    },
    watch: {
      config_show_onboarding(val, old_val) {
        if (val === true)
          this.$nextTick(() => { this.$refs['modal-onboarding'].open() })
      }
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
      show_intercom_button() {
        switch (_.get(this.$route, 'name', ''))
        {
          case null:
          case ROUTE_EMBEDHOME:
          case ROUTE_SIGNIN:
          case ROUTE_SIGNUP:
          case ROUTE_FORGOTPASSWORD:
          case ROUTE_RESETPASSWORD:
            return false
        }

        return true
      },
      config_show_onboarding() {
        // we have to do 'config_show_onboarding' as a computed value since
        // we need to wait to check if we have an active user or not
        if (this.active_user_eid.length == 0)
          return false

        var params = _.get(this.$route, 'query', {})
        if (params['app.prompt.tour.shown'] === 'true')
          return true

        var cfg = _.get(this.getActiveUser(), 'config')
        if (this.show_intercom_button && _.get(cfg, 'app.prompt.tour.shown') !== true)
          return true

        return false
      }
    },
    methods: {
      ...mapGetters([
        'getActiveUser'
      ])
    }
  }
</script>

<style lang="less">
  // match .blue color to Material Design's 'Blue A600' color
  @blue: #1e88e5;
  @black-60: rgba(0,0,0,.6);
  @bg-color: #eee;

  .css-nav {
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
</style>
