<template>
  <main class="pa3 ph3-m pa5-ns black-60 overflow-auto">
    <div class="measure-wide center">
      <sign-in-form
        class="mh5-ns"
        @sign-up-click="onSignUpClick"
        @forgot-password-click="onForgotPasswordClick"
      />
    </div>
  </main>
</template>

<script>
  import { ROUTE_SIGNUP, ROUTE_FORGOTPASSWORD } from '../constants/route'
  import SignInForm from './SignInForm.vue'
  import Redirect from './mixins/redirect'

  export default {
    mixins: [Redirect],
    components: {
      SignInForm
    },
    computed: {
      signup_route() {
        return {
          name: ROUTE_SIGNUP,
          query: this.$route.query
        }
      },
      forgotpassword_route() {
        return {
          name: ROUTE_FORGOTPASSWORD
        }
      }
    },
    mounted() {
      analytics.track('Visited Sign In Page')
    },
    methods: {
      onSignUpClick() {
        this.$router.push(this.signup_route)
      },
      onForgotPasswordClick() {
        this.$router.push(this.forgotpassword_route)
      }
    }
  }
</script>
