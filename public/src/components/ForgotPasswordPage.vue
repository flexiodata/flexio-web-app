<template>
  <main class="pa3 ph3-m pa5-ns bg-nearer-white overflow-auto">
    <div class="measure center mt3">
      <ForgotPasswordForm
        class="br2 bg-white pa3 pa4-ns css-white-box"
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
