<template>
  <form @submit.prevent>
    <div class="tc" style="margin-top: -76px">
      <img src="../assets/logo-square-80x80.png" alt="Flex.io" class="br-100 ba bw1 b--white" style="width: 84px; box-shadow: 0 0 3px rgba(0,0,0,0.4)">
    </div>
    <h1 class="fw6 tc mb4">Sign in to Flex.io</h1>
    <template>
      <div class="mb3" v-if="error_msg">
        <div class="el-alert el-alert--error is-light">
          {{error_msg}}
        </div>
      </div>
      <div class="mv3">
        <input
          ref="input-username"
          type="text"
          placeholder="Email or username"
          autocomplete=off
          spellcheck="false"
          :class="input_cls"
          v-model="username"
        >
      </div>
      <div class="mv3">
        <input
          ref="input-password"
          type="password"
          placeholder="Password"
          autocomplete=off
          spellcheck="false"
          :class="input_cls"
          @keyup.enter="trySignIn"
          v-model="password"
        >
        <button type="button" class="f8 fw6 black-60 link underline-hover dib pa0" @click="$emit('forgot-password-click')">Forgot your password?</button>
      </div>
      <div class="mv3">
        <button
          type="button"
          :class="button_cls"
          :disabled="is_signing_in || is_submitting"
          @click="trySignIn"
        >
          <span v-if="is_signing_in || is_submitting">Signing in...</span>
          <span v-else>Sign in</span>
        </button>
      </div>
    </template>
    <div class="mt3 pt2 f5 fw6 tc">
      New to Flex.io?
      <button type="button" class="link dib blue underline-hover db fw6" @click="$emit('sign-up-click')">Sign up</button>
    </div>
  </form>
</template>

<script>
  import { mapState } from 'vuex'

  export default {
    data() {
      return {
        username: '',
        password: '',
        error_msg: '',
        is_submitting: false,
        input_cls: 'input-reset ba b--black-10 br2 focus-b--blue lh-title ph3 pv2a w-100',
        button_cls: 'border-box no-select ttu fw6 w-100 ph4 pv2a lh-title white bg-blue br2 darken-10'
      }
    },
    mounted() {
      this.username = _.get(this.$route, 'query.email', '')
      this.$nextTick(() => {
        this.username.length > 0 ? this.$refs['input-password'].focus() : this.$refs['input-username'].focus()
      })
    },
    computed: {
      ...mapState({
        is_signing_in: state => state.users.is_signing_in
      })
    },
    methods: {
      trySignIn() {
        this.is_submitting = true

        var username = this.username
        var password = this.password

        this.$store.dispatch('users/signIn', { username, password }).then(response => {
          this.$emit('signed-in', response.data)
        }).catch(error => {
          this.is_submitting = false
          this.password = ''
          this.error_msg = _.get(error, 'response.data.error.message', '')
        })
      }
    }
  }
</script>
