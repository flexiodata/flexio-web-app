<template>
  <div class="bg-nearer-white">
    <div class="flex flex-column justify-center h-100">
      <Spinner size="large" message="Initializing..." />
    </div>
  </div>
</template>

<script>
  import { ROUTE_PIPE_PAGE } from '../constants/route'
  import Spinner from 'vue-simple-spinner'
  import MixinConfig from './mixins/config'

  // 'pdf-to-json'
  import pipe_pipe_to_json from '../data/pipe/pdf-to-json.yml'

  export default {
    mixins: [MixinConfig],
    components: {
      Spinner
    },
    mounted() {
      this.fetchPipes()
    },
    methods: {
      getOnboardPipe() {
        return _.get(this.$route, 'query.pipe', '')
      },
      hasOnboardPipe() {
        return this.getOnboardPipe().length > 0
      },
      openPipe(eid) {
        this.$router.push({ name: ROUTE_PIPE_PAGE, params: { eid } })
      },
      createPipe(attrs) {
        if (!_.isObject(attrs)) {
          attrs = { name: 'Untitled Pipe' }
        }

        // make sure we don't show the onboarding tour if the user comes
        // to the app through a means that creates a pipe
        var cfg_path = 'app.prompt.onboarding.pipeDocument.build.shown'
        this.$_Config_set(cfg_path, true).then(() => {
          this.$store.dispatch('v2_action_createPipe', { attrs }).then(response => {
            var pipe = response.data
            var analytics_payload = _.pick(pipe, ['eid', 'name', 'alias', 'created'])
            this.$store.track('Created Pipe', analytics_payload)
            this.openPipe(pipe.eid)
          }).catch(error => {
            this.$store.track('Created Pipe (Error)')
          })
        })
      },
      fetchPipes() {
        this.$store.dispatch('v2_action_fetchPipes', {}).then(response => {
          if (this.hasOnboardPipe()) {
            var onboard_pipe = this.getOnboardPipe()

            switch (onboard_pipe) {
              case 'pdf-to-json':
                this.createPipe(pipe_pipe_to_json)
                break
            }
          } else {
            var pipes = response.data
            var pipe = _.find(pipes, { alias: 'example-email-results-of-python-function' })

            var cfg_path = 'app.prompt.onboarding.pipeDocument.build.shown'
            this.$_Config_reset(cfg_path)

            this.openPipe(pipe.eid)
          }
        }).catch(error => {
          // TODO: add error handling?
        })
      }
    }
  }
</script>
