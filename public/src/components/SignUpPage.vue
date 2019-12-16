<template>
  <main class="pv4 pv5-ns ph3 ph5-ns bg-nearer-white overflow-auto">
    <div class="measure center mt4">
      <ResendVerificationForm
        class="br2 bg-white pv4 ph3 ph4-ns css-white-box"
        :just-signed-up="just_signed_up"
        :user="user"
        @sign-in-click="onSignInClick"
        v-if="is_resend_verify"
      />
      <SignUpVerifyForm
        class="br2 bg-white pv4 ph3 ph4-ns css-white-box"
        :just-signed-up="just_signed_up"
        :user="user"
        v-else-if="is_verify"
      />
      <SignUpForm
        class="br2 bg-white pv4 ph3 ph4-ns css-white-box"
        @sign-in-click="onSignInClick"
        @signed-in="onSignedUpAndSignedIn"
        v-else
      />
    </div>
  </main>
</template>

<script>
  import { ROUTE_SIGNIN_PAGE } from '@/constants/route'
  import { OBJECT_STATUS_AVAILABLE } from '@/constants/object-status'
  import SignUpForm from '@/components/SignUpForm'
  import SignUpVerifyForm from '@/components/SignUpVerifyForm'
  import ResendVerificationForm from '@/components/ResendVerificationForm'
  import MixinRedirect from '@/components/mixins/redirect'

  export default {
    metaInfo: {
      title: 'Sign Up for for on-demand API imports into Excel and Google Sheets'
    },
    mixins: [MixinRedirect],
    components: {
      SignUpForm,
      SignUpVerifyForm,
      ResendVerificationForm
    },
    data() {
      return {
        just_signed_up: false,
        user: {
          email: ''
        }
      }
    },
    computed: {
      is_verify() {
        return _.get(this.$route, 'params.action') == 'verify'
      },
      is_resend_verify() {
        return _.get(this.$route, 'params.action') == 'resendverification'
      },
      signin_route() {
        return {
          name: ROUTE_SIGNIN_PAGE,
          query: this.$route.query
        }
      }
    },
    methods: {
      onSignInClick() {
        this.$router.push(this.signin_route)
      },
      onSignedUpAndSignedIn(user) {
        this.just_signed_up = true
        this.user = user

        if (_.get(user, 'eid_status') == OBJECT_STATUS_AVAILABLE) {
          // user is verified; move 'em along...
          this.$_Redirect_redirect()
        } else {
          // user is not verified; take them to the account verification page
          var new_route = _.assign({}, this.$route, { params: { action: 'verify' } })
          this.$router.replace(new_route)
        }
      }
    }
  }
</script>
