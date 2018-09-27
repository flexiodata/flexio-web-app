<template>
  <div class="bg-nearer-white">
    <div class="flex flex-column justify-center h-100">
      <Spinner size="large" message="Initializing..." />
    </div>
  </div>
</template>

<script>
  import { ROUTE_PIPES } from '../constants/route'
  import Spinner from 'vue-simple-spinner'
  import MixinConfig from './mixins/config'

  export default {
    mixins: [MixinConfig],
    components: {
      Spinner
    },
    mounted() {
      this.$store.dispatch('fetchPipes').then(response => {
        if (response.ok) {
          var pipes = response.data
          var pipe = _.find(pipes, { alias: 'example-email-results-of-python-function' })
          var eid = pipe.eid

          var cfg_path = 'app.prompt.onboarding.pipeDocument.build.shown'
          this.$_Config_reset(cfg_path)

          this.$router.push({ name: ROUTE_PIPES, params: { eid } })
        } else {
          // TODO: add error handling
        }
      })
    }
  }
</script>
