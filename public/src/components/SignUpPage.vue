<template>
  <main class="pa3 ph3-m pa5-ns bg-nearer-white overflow-auto">
    <div class="measure center mt3">
      <div
        class="br2 bg-white pa3 pa4-ns css-white-box"
        v-if="is_verify"
      >
        <div class="tc" style="margin-top: -68px">
          <img src="../assets/icon/icon-flexio-128.png" alt="Flex.io" class="br-100 ba bw1 b--white" style="width: 68px; box-shadow: 0 2px 4px rgba(0,0,0,0.2)">
        </div>
        <h1 class="tc mb4">Thanks for signing up!</h1>
        <p>To finish signing up, you just need to confirm that we got your email address right.</p>
        <p>We've sent a verification email to the address you provided. Clicking the confirmation link in that email lets us know the email address is both valid and yours.</p>
        <div class="pv2">
          <button
            type="button"
            class="border-box no-select ttu fw6 w-100 ph4 pv2a lh-title white br2 darken-10"
            :class="is_sent ? 'bg-dark-green o-40 no-pointer-events' : 'bg-blue'"
            @click="resendVerification"
          >
            {{is_sent ? 'Verification email sent!' : 'Resend verification email'}}
          </button>
        </div>
      </div>
      <SignUpForm
        class="br2 bg-white pa3 pa4-ns css-white-box"
        :signin-on-signup="false"
        @sign-in-click="onSignInClick"
        @signed-up="onSignedUp"
        @signed-in="onSignedIn"
        v-else
      />
    </div>
  </main>
</template>

<script>
  import axios from 'axios'
  import { ROUTE_SIGNIN_PAGE } from '../constants/route'
  import SignUpForm from '@comp/SignUpForm'
  import MixinRedirect from '@comp/mixins/redirect'

  export default {
    metaInfo: {
      title: 'Sign Up for Flex.io Serverless Functions Today'
    },
    mixins: [MixinRedirect],
    components: {
      SignUpForm
    },
    data() {
      return {
        is_sent: false,
        user: {}
      }
    },
    computed: {
      is_verify() {
        return _.get(this.$route, 'params.action', '') === 'verify'
      },
      signin_route() {
        return {
          name: ROUTE_SIGNIN_PAGE,
          query: this.$route.query
        }
      }
    },
    mounted() {
      // don't allow signup verification page to be reloaded
      if (this.is_verify && _.get(this.user, 'email', '').length == 0) {
        var new_route = _.assign({}, this.$route, { params: { action: undefined } })
        this.$router.replace(new_route)
      }

      this.$store.track('Visited Sign Up Page')
    },
    methods: {
      onSignInClick() {
        this.$router.push(this.signin_route)
      },
      onSignedUp(user) {
        this.user = user
        var new_route = _.assign({}, this.$route, { params: { action: 'verify' } })
        this.$router.replace(new_route)
      },
      onSignedIn() {
        var redirect = _.get(this.$route, 'query.redirect', '')

        if (redirect.length > 0) {
          this.$_Redirect_redirect()
        } else {
          this.$_Redirect_redirect()
        }
      },
      resendVerification() {
        axios.post('/api/v2/requestverification', { email: this.user.email }).then(response => {
          this.is_sent = true
          setTimeout(() => { this.is_sent = false }, 6000)
        })
      }
    }
  }
</script>
