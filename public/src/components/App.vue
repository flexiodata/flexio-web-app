<template>
  <div id="app" class="flex flex-column fixed absolute--fill overflow-hidden">
    <app-navbar class="flex-none"></app-navbar>
    <router-view class="flex-fill"></router-view>

    <!-- onboarding modal -->
    <onboarding-modal
      ref="modal-onboarding"
      @hide="show_onboarding_modal = false"
      v-if="show_onboarding_modal"
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
    ROUTE_ACCOUNT,
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
      show_onboarding_modal(val, old_val) {
        if (val === true)
          this.$nextTick(() => { this.$refs['modal-onboarding'].open() })
      }
    },
    computed: {
      ...mapState([
        'active_user_eid'
      ]),
      show_onboarding_modal() {
        if (this.active_user_eid.length == 0)
          return false

        var cfg = _.get(this.getActiveUser(), 'config')
        if (_.get(cfg, 'app.prompt.tour.shown') !== true)
          return true

        return false
      },
      show_intercom_button() {
        switch (this.$route.name)
        {
          case ROUTE_EMBEDHOME:
          case ROUTE_SIGNIN:
          case ROUTE_SIGNUP:
          case ROUTE_FORGOTPASSWORD:
          case ROUTE_RESETPASSWORD:
            return false
        }

        return true
      }
    },
    methods: {
      ...mapGetters([
        'getActiveUser'
      ])
    }
  }
</script>
