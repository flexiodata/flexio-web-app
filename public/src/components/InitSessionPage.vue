<template>
  <div class="bg-nearer-white">
    <div class="flex flex-column justify-center h-100">
      <Spinner size="large" message="Signing in..." />
    </div>
  </div>
</template>

<script>
  import Spinner from 'vue-simple-spinner'
  import MixinRedirect from './mixins/redirect'

  export default {
    mixins: [MixinRedirect],
    components: {
      Spinner
    },
    data() {
      return {
        query: this.$route.query
      }
    },
    mounted() {
      var ref = _.get(this.query, 'ref', '')
      var redirect = _.get(this.query, 'redirect', '/')

      if (ref.length > 0) {
        // initialize session
        axios.get('/api/v2/login', { params: ref }).then(response => {
          this.$_Redirect_redirect(redirect, 'replace')
        })
      } else {
        this.$_Redirect_redirect(redirect, 'replace')
      }
    }
  }
</script>
