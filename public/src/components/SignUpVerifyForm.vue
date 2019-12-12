<template>
  <form @submit.prevent>
    <div class="tc" style="margin-top: -76px">
      <img src="../assets/logo-square-80x80.png" alt="Flex.io" class="br-100 ba bw1 b--white" style="width: 84px; box-shadow: 0 0 3px rgba(0,0,0,0.4)">
    </div>
    <h1 class="fw6 tc mb4">Send verification email</h1>
    <p>We sent a verification email to the address you provided when you signed up.</p>
    <p>If you no longer have this email, you may enter your email address below and we'll send it to you again.</p>
    <div class="pt2">
      <div class="mb3">
        <input
          type="email"
          placeholder="Email address"
          auto-complete="off"
          spellcheck="false"
          class="input-reset ba b--black-10 br2 focus-b--blue lh-title ph3 pv2a w-100"
          @keyup.enter="resendVerification"
          v-model="email"
        >
      </div>
      <div class="mt3">
        <button
          type="button"
          class="border-box no-select ttu fw6 w-100 ph4 pv2a lh-title white br2 darken-10"
          :class="{
            'bg-blue': !is_sent,
            'bg-dark-green': is_sent,
            'o-40 no-pointer-events': is_sending || is_sent
          }"
          @click="resendVerification"
        >
          {{button_label}}
        </button>
      </div>
      <div class="mt3 pt2 f5 fw6 tc">
        Already have an account?
        <button type="button" class="link dib blue underline-hover db fw6" @click="$emit('sign-in-click')">Sign in</button>
      </div>
    </div>
  </form>
</template>

<script>
  import api from '@/api'

  export default {
    props: {
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
      }
    },
    data() {
      return {
        is_sending: false,
        is_sent: false,
        email: ''
      }
    },
    computed: {
      button_label() {
        if (this.is_sent) {
          return 'Verification email sent!'
        } else if (this.is_sending) {
          return 'Sending email...'
        } else {
          return 'Send verification email'
        }
      }
    },
    methods: {
      initSelf() {
        this.email = _.get(this.user, 'email', '')
      },
      resendVerification() {
        this.is_sending = true
        api.requestVerification({ email: this.email }).then(response => {
          this.is_sent = true
          setTimeout(() => { this.is_sent = false }, 6000)
        }).finally(() => {
          this.is_sending = false
        })
      }
    }
  }
</script>
