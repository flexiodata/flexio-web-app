<template>
  <div>
    <div class="pv3 ph2">
      <SignUpForm
        @sign-in-click="view = 'signin'"
        @signed-up="onSignedUp"
        @signed-in="onSignedUpAndIn"
        v-if="view === 'signup'"
      />
      <SignInForm
        @sign-up-click="view = 'signup'"
        @forgot-password-click="view = 'forgotpassword'"
        @signed-in="onSignedIn"
        v-else-if="view === 'signin'"
      />
      <ForgotPasswordForm
        @sign-up-click="view = 'signup'"
        @sign-in-click="view = 'signin'"
        @requested-password="$emit('requested-password')"
        v-else-if="view === 'forgotpassword'"
      />
    </div>
  </div>
</template>

<script>
  import SignUpForm from './SignUpForm.vue'
  import SignInForm from './SignInForm.vue'
  import ForgotPasswordForm from './ForgotPasswordForm.vue'

  export default {
    props: {
      'initial-view': {
        type: String,
        default: 'signup'
      }
    },
    components: {
      SignUpForm,
      SignInForm,
      ForgotPasswordForm
    },
    watch: {
      view: {
        handler: 'trackPage',
        immediate: true
      }
    },
    data() {
      return {
        base_url: 'https://www.flex.io',
        view: this.initialView // 'signin', 'signup' or 'forgotpassword'
      }
    },
    mounted() {
      switch (window.location.hostname) {
        default:
          this.base_url = 'https://www.flex.io'
          return
        case 'test.flex.io':
          this.base_url = 'https://test.flex.io'
          break
        case 'localhost':
          var base_url = window.location.protocol + '://localhost'
          var port = window.location.port
          if (port && port != '80' && port != '443') {
            base_url += ':' + port
          }
          this.base_url = base_url
          break
      }
    },
    methods: {
      trackPage() {
        if (this.view == 'signup') {
          if (window.analytics) {
            window.analytics.track('Visited Sign Up Page', { label: window.location.pathname })
          }
        } else if (this.view == 'signin') {
          if (window.analytics) {
            window.analytics.track('Visited Sign In Page', { label: window.location.pathname })
          }
        } else if (this.view == 'forgotpassword') {
          if (window.analytics) {
            window.analytics.track('Visited Forgot Password Page', { label: window.location.pathname })
          }
        }
      },
      getUrl(path) {
        return this.base_url + path
      },
      onSignedIn() {
        this.$emit('signed-in')
        setTimeout(() => { window.location = this.getUrl('/app') }, 500)
      },
      onSignedUp() {
        // do nothing
      },
      onSignedUpAndIn() {
        this.$emit('signed-up')
        setTimeout(() => { window.location = this.getUrl('/app/onboard') }, 500)
      }
    }
  }
</script>
