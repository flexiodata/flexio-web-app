<template>
  <ui-modal
    ref="dialog"
    remove-header
    remove-close-button
    dismiss-on="close-button"
    class="ui-modal-onboarding"
  >
    <div class="w-100 tc">
      <div class="f2 mv3">Welcome to Flex.io</div>
      <div class="mv3 f5 fw6 black-60">Watch this quick 3 minute video to get started.</div>
      <div class="mv4">
        <iframe width="640" height="360" src="https://www.youtube.com/embed/iKvVYtn9hoY?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>
      </div>
      <btn btn-lg btn-primary class="b ttu" @click="close()">All set, take me to the app</btn>
      <div class="mv3 fw6 black-60">
        You can watch the video again later in the <a class="black-60" href="/docs/web-app/#overview" target="_blank">Help Docs</a>.
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

        var cfg = _.get(this.getActiveUser(), 'config', {})
        if (_.isArray(cfg))
          cfg = {}
        cfg['app.prompt.tour.shown'] = true

        this.$store.dispatch('updateUser', { eid: this.active_user_eid, attrs: { config: cfg } })
      }
    }
  }
</script>

<style lang="less">
  .ui-modal-onboarding {
    .ui-modal__container {
      width: 688px;
    }
  }
</style>
