<template>
  <div class="bg-nearer-white">
    <div class="flex flex-column justify-center h-100">
      <Spinner size="large" message="Signing in..." />
    </div>
  </div>
</template>

<script>
  import axios from 'axios'
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
      var token = _.get(this.query, 'token', '')
      var redirect = _.get(this.query, 'redirect', '/')

      if (token.length > 0) {
        // initialize session
        axios.get('/api/v2/login', { params: token }).then(response => {
          this.fetchUserAndRedirect(redirect)
        })
      } else {
        this.fetchUserAndRedirect(redirect)
      }
    },
    methods: {
      fetchUserAndRedirect(redirect) {
        this.$store.dispatch('v2_action_fetchCurrentUser').then(response => {
          this.$_Redirect_redirect(redirect, 'replace')
        }).catch(error => {
          this.$_Redirect_redirect(redirect, 'replace')
        })
      }
    }
  }
</script>
