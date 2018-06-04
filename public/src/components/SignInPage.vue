<template>
  <main class="pa3 ph3-m pa5-ns bg-nearer-white black-60 overflow-auto">
    <div class="measure center">
      <sign-in-form
        class="br2 bg-white pa3 pa4-ns css-white-box"
        @sign-up-click="onSignUpClick"
        @forgot-password-click="onForgotPasswordClick"
        @signed-in="onSignedIn"
      />
    </div>
  </main>
</template>

<script>
  import { mapState } from 'vuex'
  import { ROUTE_SIGNUP, ROUTE_FORGOTPASSWORD } from '../constants/route'
  import SignInForm from './SignInForm.vue'
  import Redirect from './mixins/redirect'

  export default {
    mixins: [Redirect],
    components: {
      SignInForm
    },
    computed: {
      ...mapState([
        'active_user_eid'
      ]),
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
      this.$store.track('Visited Sign In Page')

      if (this.active_user_eid.length > 0)
        this.$_Redirect_redirect()
    },
    methods: {
      onSignUpClick() {
        this.$router.push(this.signup_route)
      },
      onForgotPasswordClick() {
        this.$router.push(this.forgotpassword_route)
      },
      onSignedIn() {
        this.$_Redirect_redirect()
      }
    }
  }
</script>
