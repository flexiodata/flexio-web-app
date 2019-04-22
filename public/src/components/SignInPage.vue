<template>
  <main class="pa3 ph3-m pa5-ns bg-nearer-white black-60 overflow-auto">
    <div class="measure center">
      <div class="br2 bg-white mt4 pa4 css-white-box" v-if="false">
        <div class="tc mb4" style="margin-top: -68px">
          <img src="../assets/icon/icon-flexio-128.png" alt="Flex.io" class="br-100 ba bw1 b--white" style="width: 68px; box-shadow: 0 2px 4px rgba(0,0,0,0.2)">
        </div>
        <h1 class="tc mb4 pb2">Welcome to Flex.io</h1>
        <div>
          <p style="margin: 0 0 2rem 0">Sign in with one of the following:</p>
          <div class="flex flex-column">
            <el-button class="dim-10" style="margin: 0 0 1rem 0; color: #fff; background-color: #4285f4; border-color: #4285f4">
              <div class="flex flex-row justify-center items-center">
                <svg fill="white" height="24" width="24" viewBox="0 0 488 512"><path d="M488 261.8C488 403.3 391.1 504 248 504 110.8 504 0 393.2 0 256S110.8 8 248 8c66.8 0 123 24.5 166.3 64.9l-67.5 64.9C258.5 52.6 94.3 116.6 94.3 256c0 86.5 69.1 156.6 153.7 156.6 98.2 0 135-70.4 140.8-106.9H248v-85.3h236.1c2.3 12.7 3.9 24.9 3.9 41.4z"/></svg>
                <div class="ml2">Google</div>
              </div>
            </el-button>
            <el-button class="dim-10" style="margin: 0 0 1rem 0; color: #fff; background-color: #24282d; border-color: #24282d">
              <div class="flex flex-row justify-center items-center">
                <svg fill="white" height="24" width="24" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0 0 16 8c0-4.42-3.58-8-8-8z"></path></svg>
                <div class="ml2">GitHub</div>
              </div>
            </el-button>
            <el-button style="margin: 0 0 1rem 0">
              <div class="flex flex-row justify-center items-center">
                <svg fill="currentColor" height="24" width="24" viewBox="0 0 24 24"><path fill="none" d="M0 0h24v24H0z"/><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H4V8l8 5 8-5v10zm-8-7L4 6h16l-8 5z"/></svg>
                <div class="ml2">Email</div>
              </div>
            </el-button>
          </div>
        </div>
      </div>
      <div class="br2 bg-white pa3 pa4-ns css-white-box">
        <sign-in-form
          @sign-up-click="onSignUpClick"
          @forgot-password-click="onForgotPasswordClick"
          @signed-in="onSignedIn"
        />
      </div>
    </div>
  </main>
</template>

<script>
  import { mapState } from 'vuex'
  import { ROUTE_SIGNUP_PAGE, ROUTE_FORGOTPASSWORD_PAGE } from '../constants/route'
  import SignInForm from '@comp/SignInForm'
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

<style lang="stylus">
  .dim-10:hover
    opacity: 0.9
  .dim-10:active
    opacity: 0.8
</style>
