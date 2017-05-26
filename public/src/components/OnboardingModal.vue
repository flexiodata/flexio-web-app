<template>
  <ui-modal
    ref="dialog"
    title=" "
    dismiss-on="close-button"
    class="ui-modal-onboarding"
    @hide="updateUserConfig"
  >
    <div class="w-100 tc">
      <div class="f2 mb3" style="margin-top: -1rem">Welcome to Flex.io</div>
      <div class="mv3 f5 fw6 black-60">Click on one of the example templates to get started building your first pipe.</div>
      <div class="flex flex-row justify-around mv4">
        <div>Example #1</div>
        <div>Example #2</div>
        <div>Example #3</div>
      </div>
      <div class="mv3 fw6 black-60">
        You can also watch a quick 3-minute getting started video in the <a class="black-60" href="/docs/web-app/#overview" target="_blank">Help Docs</a>.
      </div>
    </div>
  </ui-modal>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import Btn from './Btn.vue'

  export default {
    components: {
      Btn
    },
    computed: {
      ...mapState([
        'active_user_eid'
      ])
    },
    methods: {
      ...mapGetters([
        'getActiveUser'
      ]),
      open() {
        this.$refs['dialog'].open()
      },
      close() {
        this.$refs['dialog'].close()
        this.updateUserConfig()
      },
      updateUserConfig() {
        var cfg = _.get(this.getActiveUser(), 'config', {})
        if (_.isArray(cfg))
          cfg = {}
        cfg['app.prompt.tour.shown'] = true

        this.$store.dispatch('updateUser', { eid: this.active_user_eid, attrs: { config: cfg } })

        analytics.track('Closed Onboarding Modal')

        setTimeout(() => {
          analytics.identify(this.active_user_eid, { closed_onboarding_modal: 1 })
        }, 500)
      }
    }
  }
</script>

<style lang="less">
  .ui-modal-onboarding {
    .ui-modal__container {
      width: 690px;
    }
  }
</style>
