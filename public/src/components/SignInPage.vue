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
  import { ROUTE_SIGNUP_PAGE, ROUTE_FORGOTPASSWORD_PAGE } from '../constants/route'
  import SignInForm from './SignInForm.vue'
  import MixinRedirect from './mixins/redirect'

  export default {
    metaInfo: {
      title: 'Sign In to the Flex.io Serverless Functions Platform',
      meta: [
        {
          vmid: 'description',
          name: 'description',
          content: 'Sign in to Flex.io and stitch together serverless functions with out-of-the-box helper tasks that take the pain out of OAuth, notifications, scheduling, local storage, library dependencies and other "glue" code.'
        }
      ]
    },
    mixins: [MixinRedirect],
    components: {
      SignInForm
    },
    computed: {
      ...mapState([
        'active_user_eid'
      ]),
      signup_route() {
        return {
          name: ROUTE_SIGNUP_PAGE,
          query: this.$route.query
        }
      },
      forgotpassword_route() {
        return {
          name: ROUTE_FORGOTPASSWORD_PAGE
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
