<template>
  <form @submit.prevent>
    <div class="tc">
      <img src="../assets/logo-flexio-header.png" alt="Flex.io">
    </div>
    <legend class="f3 tc ph0 mh0 mv3 black-80 w-100">Forgot Password</legend>
    <div v-if="is_sent">
      <div class="mv3 lh-copy">
        <p>An email has been sent to <span class="b">{{email}}</span> with further instructions.</p>
        <p>You may need to check your spam folder or unblock no-reply@flex.io.</p>
        <router-link to="/signin" class="link ph4 pv2a b lh-title white bg-blue b--blue darken-10 ttu tc db">Sign in</router-link>
      </div>
    </div>
    <div v-else>
      <div class="mv3 lh-copy">
        <p>Enter the email address you signed up with below. An email will be sent containing a link to reset your password.</p>
        <p>You may need to check your spam folder or unblock no-reply@flex.io.</p>
      </div>
      <div v-if="error_msg" class="mv3 ph3 pv2a lh-title fw6 br1 white bg-dark-red">
        {{error_msg}}
      </div>
      <div class="mv3">
        <input v-model="email" v-focus @keyup.enter="sendReset" :class="input_cls" placeholder="Email address" type="email" autocomplete=off spellcheck="false">
      </div>
      <div class="mt3">
        <btn btn-lg btn-primary :disabled="is_submitting" @click="sendReset" class="b ttu w-100">
          <span v-if="is_submitting">Sending reset instructions...</span>
          <span v-else>Send reset instructions</span>
        </btn>
      </div>
    </div>
  </form>
</template>

<script>
  import api from '../api'
  import Btn from './Btn.vue'

  export default {
    components: {
      Btn
    },
    data() {
      return {
        email: '',
        is_submitting: false,
        is_sent: false,
        error_msg: '',
        input_cls: 'input-reset ba b--black-10 focus-b--transparent focus-outline focus-o--blue lh-title ph3 pv2a w-100'
      }
    },
    mounted() {
      analytics.track('Visited Forgot Password Page')
    },
    methods: {
      getAttrs() {
        // assemble non-empty values for submitting to the backend
        return _
          .chain(this.$data)
          .pick(['email'])
          .omitBy(_.isEmpty)
          .value()
      },
      sendReset() {
        var me = this
        var attrs = this.getAttrs()

        this.is_submitting = true

        api.requestPasswordReset({ attrs }).then(response => {
          // success callback
          me.is_submitting = false
          me.is_sent = true
        }, response => {
          // error callback
          me.is_submitting = false
          this.error_msg = _.get(response, 'data.error.message', '')
        })
      }
    }
  }
</script>
