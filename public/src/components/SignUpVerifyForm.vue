<template>
  <form @submit.prevent>
    <div class="tc" style="margin-top: -76px">
      <img src="../assets/logo-square-80x80.png" alt="Flex.io" class="br-100 ba bw1 b--white" style="width: 84px; box-shadow: 0 0 3px rgba(0,0,0,0.4)">
    </div>
    <div v-if="is_verifying && is_from_link">
      <div class="tc mt4 mb3">
        <i class="el-icon-loading" style="font-size: 3rem"></i>
      </div>
      <h2 class="fw6 tc mb4" v-if="is_verifying">Verifying your account...</h2>
    </div>
    <div v-else-if="is_verified">
      <div class="tc mt4 mb3">
        <i class="el-icon-success dark-green" style="font-size: 3rem"></i>
      </div>
      <h2 class="fw6 tc mb4">Account verified!</h2>
      <div class="tc">
        <p>Thank you for verifying your account!</p>
        <p>You will be redirected to the sign in page in just a moment...</p>
      </div>
    </div>
    <div v-else>
      <h1 class="fw6 tc mb4">Please verify your email</h1>
      <p v-if="is_email_provided">You're almost there! We sent an email to <strong class="nowrap">{{email}}</strong>.</p>
      <p v-else>You're almost there! We sent an email to you with a verification code.</p>
      <p v-if="is_email_provided">Please click on the link in that email or enter the verification code from the email below to complete your sign up.</p>
      <p v-else>Please click on the link in that email or enter your email address and the verification code from the email below to complete your sign up.</p>
      <p>If you don't see the email in your inbox, you may need to check your spam folder.</p>
      <div class="pt2">
        <el-alert
          type="error"
          show-icon
          :title="error_msg"
          @close="error_msg = ''"
          v-if="error_msg"
        />
        <div class="mb3" :class="error_msg.length > 0 ? 'mt3' : ''" v-if="!is_email_provided">
          <input
            type="email"
            placeholder="Email"
            auto-complete="off"
            spellcheck="false"
            class="input-reset ba b--black-10 br2 focus-b--blue lh-title ph3 pv2a w-100"
            v-model="email"
          >
        </div>
        <div class="mb3" :class="error_msg.length > 0 ? 'mt3' : ''">
          <input
            type="text"
            placeholder="Enter verification code"
            auto-complete="off"
            spellcheck="false"
            class="input-reset ba b--black-10 br2 focus-b--blue lh-title ph3 pv2a w-100"
            @keyup.enter="verifyUser"
            v-model="verify_code"
          >
        </div>
        <div class="mt3">
          <button
            type="button"
            class="border-box no-select ttu fw6 w-100 ph4 pv2a lh-title white br2 darken-10"
            :class="{
              'bg-blue': !is_verified,
              'bg-dark-green': is_verified,
              'o-40 no-pointer-events': is_verifying || is_verified
            }"
            @click="verifyUser"
          >
            {{verify_button_label}}
          </button>
        </div>
      </div>
      <div class="mt3 f7" v-if="justSignedUp">
        Can't find the email?
        <button
          type="button"
          style="padding: 0 2px"
          :class="{
            'blue': !is_sent,
            'dark-green': is_sent,
            'no-pointer-events': is_sending || is_sent
          }"
          @click="resendVerification"
        >
          {{resend_button_label}}
        </button>
      </div>
    </div>
  </form>
</template>

<script>
  import api from '@/api'
  import { ROUTE_APP_ONBOARDING } from '@/constants/route'

  export default {
    props: {
      justSignedUp: {
        type: Boolean,
        default: false
      },
      user: {
        type: Object,
        default: () => {
          email: ''
        }
      }
    },
    watch: {
      user: {
        handler: 'initSelf',
        immediate: true,
        deep: true
      },
    },
    data() {
      return {
        is_verifying: false,
        is_verified: false,
        is_sending: false,
        is_sent: false,
        is_from_link: false,
        error_msg: '',
        email: '',
        verify_code: ''
      }
    },
    computed: {
      is_email_provided() {
        var query_email = _.get(this.$route, 'query.email', '')
        var prop_email = _.get(this.user, 'email', '')
        return query_email.length > 0 || prop_email.length > 0
      },
      verify_button_label() {
        if (this.is_verified) {
          return 'Account verified!'
        } else if (this.is_verifying) {
          return 'Verifying account...'
        } else {
          return 'Verify my account'
        }
      },
      resend_button_label() {
        if (this.is_sent) {
          return 'Verification email sent!'
        } else if (this.is_sending) {
          return 'Sending email...'
        } else {
          return 'Resend verification email'
        }
      },
    },
    mounted() {
      var query_params = this.$route.query
      _.assign(this.$data, query_params)

      if (_.has(query_params, 'email') && _.has(query_params, 'verify_code')) {
        this.is_from_link = true
        this.verifyUser()
      }
    },
    methods: {
      initSelf() {
        this.email = _.get(this.user, 'email', '')
      },
      verifyUser() {
        this.is_verifying = true

        var verify_params = {
          email: this.email,
          verify_code: this.verify_code
        }

        this.$store.dispatch('users/signOut', { silent: true }).then(response => {
          api.verifyAccount(verify_params).then(response => {
            this.$store.track('Verified Account')
            this.is_verified = true
            var query = _.omit(verify_params, ['ref', 'verify_code'])
            setTimeout(() => {
              this.$router.replace({
                name: ROUTE_APP_ONBOARDING,
                query
              })
            }, 3000)
          }).catch(error => {
            this.error_msg = _.get(error, 'response.data.error.message', '')
          }).finally(() => {
            this.is_verifying = false
            this.is_from_link = false
          })
        }).catch(error => {
          this.error_msg = _.get(error, 'response.data.error.message', '')
        }).finally(() => {
          this.is_verifying = false
          this.is_from_link = false
        })
      },
      resendVerification() {
        this.is_sending = true
        api.requestVerification({ email: this.email }).then(response => {
          this.is_sent = true
          setTimeout(() => { this.is_sent = false }, 6000)
        }).finally(() => {
          this.is_sending = false
        })
      },
    }
  }
</script>
