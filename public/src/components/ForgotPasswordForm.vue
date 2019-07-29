<template>
  <form @submit.prevent>
    <div class="tc" style="margin-top: -76px">
      <img src="../assets/logo-square-80x80.png" alt="Flex.io" class="br-100 ba bw1 b--white" style="width: 84px; box-shadow: 0 0 3px rgba(0,0,0,0.4)">
    </div>
    <h1 class="fw6 tc mb4">Forgot Password</h1>
    <template v-if="is_sent">
      <p>An email has been sent to <span class="b">{{email}}</span> with further instructions.</p>
      <p>You may need to check your spam folder or unblock no-reply@flex.io.</p>
      <div class="pv2">
        <button
          :class="button_cls"
          @click="$emit('sign-in-click')"
        >
          Sign in
        </button>
      </div>
    </template>
    <template v-else>
      <p>Enter the email address you signed up with below. An email will be sent containing a link to reset your password.</p>
      <p>You may need to check your spam folder or unblock <span class="fw6 nowrap">no-reply@flex.io</span>.</p>
      <div class="pt2">
        <div class="mb3" v-if="error_msg">
          <div class="el-alert el-alert--error is-light">
            {{error_msg}}
          </div>
        </div>
        <div class="mb3">
          <input
            ref="input-email"
            type="email"
            placeholder="Email address"
            autocomplete="off"
            spellcheck="false"
            :class="input_cls"
            @keyup.enter="sendReset"
            v-model="email"
          >
        </div>
        <div class="mt3">
          <button
            type="button"
            :class="button_cls"
            :disabled="is_submitting"
            @click="sendReset"
          >
            <span v-if="is_submitting">Sending reset instructions...</span>
            <span v-else>Send reset instructions</span>
          </button>
        </div>
      </div>
    </template>
    <div
      class="mt3 pt2 f5 fw6 tc"
      v-show="!is_sent"
    >
      New to Flex.io?
      <button type="button" class="link dib blue underline-hover db fw6" @click="$emit('sign-up-click')">Sign up</button>
    </div>
  </form>
</template>

<script>
  import api from '@/api'

  export default {
    data() {
      return {
        email: '',
        is_submitting: false,
        is_sent: false,
        error_msg: '',
        input_cls: 'input-reset ba b--black-10 br2 focus-b--blue lh-title ph3 pv2a w-100',
        button_cls: 'border-box no-select ttu fw6 w-100 ph4 pv2a lh-title white bg-blue br2 darken-10'
      }
    },
    mounted() {
      this.$refs['input-email'].focus()
    },
    methods: {
      getAttrs() {
        // assemble non-empty values for submitting to the backend
        var attrs = _.assign({}, this.$data)
        attrs = _.pick(attrs, ['email'])
        return _.omitBy(attrs, _.isEmpty)
      },
      sendReset() {
        var attrs = this.getAttrs()

        this.is_submitting = true

        api.forgotPassword(attrs).then(response => {
          this.is_sent = true
          this.$emit('requested-password')
        }).catch(error => {
          this.is_submitting = false
          this.error_msg = _.get(error, 'response.data.error.message', '')
        })
      }
    }
  }
</script>
