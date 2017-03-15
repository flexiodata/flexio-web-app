<template>
  <div id="app" class="flex flex-column fixed absolute--fill overflow-hidden">
    <app-navbar class="flex-none"></app-navbar>
    <router-view class="flex-fill"></router-view>

    <!-- share modal -->
    <onboarding-modal
      ref="modal-onboarding"
      @hide="show_onboarding_modal = false"
      v-if="show_onboarding_modal"
    ></onboarding-modal>
  </div>
</template>

<script>
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
      }
    },
    methods: {
      ...mapGetters([
        'getActiveUser'
      ])
    }
  }
</script>
