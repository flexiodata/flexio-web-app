<template>
  <main class="pa3 ph3-m pa5-ns bg-nearer-white overflow-auto">
    <div class="measure center mt4">
      <SignUpVerifyForm
        class="br2 bg-white pa3 pa4-ns css-white-box"
        :just-signed-up="just_signed_up"
        :user="user"
        @sign-in-click="onSignInClick"
        v-if="is_verify"
      />
      <SignUpForm
        class="br2 bg-white pa3 pa4-ns css-white-box"
        :signin-on-signup="false"
        @sign-in-click="onSignInClick"
        @signed-up="onSignedUp"
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
  import MixinRedirect from '@/components/mixins/redirect'

  export default {
    metaInfo: {
      title: 'Sign Up for Flex.io Serverless Functions Today'
    },
    mixins: [MixinRedirect],
    components: {
      SignUpForm,
      SignUpVerifyForm
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
      signin_route() {
        return {
          name: ROUTE_SIGNIN_PAGE,
          query: this.$route.query
        }
      }
    },
    mounted() {
      this.$store.track('Visited Sign Up Page')
    },
    methods: {
      onSignInClick() {
        this.$router.push(this.signin_route)
      },
      onSignedUp(user) {
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
