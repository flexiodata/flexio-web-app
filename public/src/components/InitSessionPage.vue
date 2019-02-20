<template>
  <div>
    <div>InitSessionPage</div>
    <pre class="f7">{{query}}</pre>
  </div>
</template>

<script>
  import MixinRedirect from './mixins/redirect'

  export default {
    mixins: [MixinRedirect],
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
        axios.post('/api/v2/sessioninit', { ref }).then(response => {
          this.$_Redirect_redirect(redirect, 'replace')
        })
      } else {
        this.$_Redirect_redirect(redirect, 'replace')
      }
    }
  }
</script>
