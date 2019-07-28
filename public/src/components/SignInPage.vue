<template>
  <main class="pa3 ph3-m pa5-ns bg-nearer-white overflow-auto">
    <div class="measure center mt4">
      <div class="br2 bg-white pa3 pa4-ns css-white-box">
        <SignInForm
          @sign-up-click="onSignUpClick"
          @forgot-password-click="onForgotPasswordClick"
          @signed-in="onSignedIn"
        />
      </div>
    </div>
  </main>
</template>

<script>
  import { OBJECT_STATUS_PENDING } from '@/constants/object-status'
  import { mapState, mapGetters } from 'vuex'
  import { ROUTE_SIGNUP_PAGE, ROUTE_FORGOTPASSWORD_PAGE } from '@/constants/route'
  import SignInForm from '@/components/SignInForm'
  import MixinRedirect from '@/components/mixins/redirect'

  export default {
    metaInfo: {
      title: 'Sign in to the Flex.io Serverless Functions Platform',
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
      ...mapState({
        active_user_eid: state => state.users.active_user_eid,
      }),
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

      if (this.active_user_eid.length > 0) {
        this.$_Redirect_redirect()
      }
    },
    methods: {
      ...mapGetters('users', {
        'getActiveUser': 'getActiveUser'
      }),
      onSignUpClick() {
        this.$router.push(this.signup_route)
      },
      onForgotPasswordClick() {
        this.$router.push(this.forgotpassword_route)
      },
      onSignedIn() {
        this.$store.dispatch('users/fetch', { eid: 'me' }).then(response => {
          if (this.getActiveUser().eid_status == OBJECT_STATUS_PENDING) {
            this.$router.push({ path: '/signup/verify' })
          } else {
            this.$_Redirect_redirect()
          }
        })
      }
    }
  }
</script>

<style lang="stylus">
  .dim-10:hover
    opacity: 0.9
  .dim-10:active
    opacity: 0.8
</style>
