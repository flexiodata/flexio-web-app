<template>
  <div class="bg-nearer-white">
    <div class="flex flex-column justify-center h-100">
      <Spinner size="large" message="Signing in..." />
    </div>
  </div>
</template>

<script>
  import api from '@/api'
  import Spinner from 'vue-simple-spinner'
  import MixinRedirect from '@/components/mixins/redirect'

  export default {
    mixins: [MixinRedirect],
    components: {
      Spinner
    },
    mounted() {
      var token = _.get(this.$route.query, 'token', '')
      var redirect = _.get(this.$route.query, 'redirect', '/')

      if (token.length > 0) {
        // initialize session
        api.signIn({ params: { token } }).then(response => {
          this.fetchUserAndRedirect(redirect)
        }).catch(error => {
          this.fetchUserAndRedirect(redirect)
        })
      } else {
        this.fetchUserAndRedirect(redirect)
      }
    },
    methods: {
      fetchUserAndRedirect(redirect) {
        this.$store.dispatch('users/fetch', { eid: 'me' }).then(response => {
          this.$_Redirect_redirect(redirect, 'replace')
        }).catch(error => {
          this.$_Redirect_redirect(redirect, 'replace')
        })
      }
    }
  }
</script>
