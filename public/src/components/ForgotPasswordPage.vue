<template>
  <main class="pa3 ph3-m pa5-ns black-60 overflow-auto">
    <div class="measure-wide center">
      <forgot-password-form
        class="mh5-ns"
        @sign-up-click="onSignUpClick"
        @sign-in-click="onSignInClick"
      />
    </div>
  </main>
</template>

<script>
  import { ROUTE_SIGNIN, ROUTE_SIGNUP } from '../constants/route'
  import ForgotPasswordForm from './ForgotPasswordForm.vue'

  export default {
    components: {
      ForgotPasswordForm
    },
    computed: {
      signup_route() {
        return {
          name: ROUTE_SIGNUP,
          query: this.$route.query
        }
      },
      signin_route() {
        return {
          name: ROUTE_SIGNIN
        }
      }
    },
    mounted() {
      this.$store.track('Visited Forgot Password Page')
    },
    methods: {
      onSignUpClick() {
        this.$router.push(this.signup_route)
      },
      onSignInClick() {
        this.$router.push(this.signin_route)
      }
    }
  }
</script>
