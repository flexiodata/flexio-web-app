<template>
  <main class="pv4 pv5-ns ph3 ph5-ns bg-nearer-white overflow-auto">
    <div class="measure center mt4">
      <ForgotPasswordForm
        class="br2 bg-white pv4 ph3 ph4-ns css-white-box"
        @sign-up-click="onSignUpClick"
        @sign-in-click="onSignInClick"
      />
    </div>
  </main>
</template>

<script>
  import { ROUTE_SIGNIN_PAGE, ROUTE_SIGNUP_PAGE } from '@/constants/route'
  import ForgotPasswordForm from '@/components/ForgotPasswordForm'

  export default {
    metaInfo: {
      title: 'Forgot Password'
    },
    components: {
      ForgotPasswordForm
    },
    computed: {
      signup_route() {
        return {
          name: ROUTE_SIGNUP_PAGE,
          query: this.$route.query
        }
      },
      signin_route() {
        return {
          name: ROUTE_SIGNIN_PAGE
        }
      }
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
