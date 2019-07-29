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
          :disabled="is_submitting"
          @click="trySignIn"
        >
          <span v-if="is_submitting">Signing in...</span>
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
  import axios from 'axios'

  export default {
    data() {
      return {
        username: '',
        password: '',
        is_submitting: false,
        error_msg: '',
        verify_code: '',
        input_cls: 'input-reset ba b--black-10 br2 focus-b--blue lh-title ph3 pv2a w-100',
        button_cls: 'border-box no-select ttu fw6 w-100 ph4 pv2a lh-title white bg-blue br2 darken-10'
      }
    },
    mounted() {
      this.$refs['input-username'].focus()
    },
    methods: {
      getAttrs() {
        // assemble non-empty values for submitting to the backend
        var attrs = _.assign({}, this.$data)
        attrs = _.pick(attrs, ['username', 'password', 'verify_code'])
        return _.omitBy(attrs, _.isEmpty)
      },
      trySignIn() {
        var attrs = this.getAttrs()

        this.is_submitting = true

        axios.post('/api/v2/login', attrs).then(response => {
          var user_info =  _.get(response, 'data', {})
          this.$emit('signed-in', user_info)
          this.trackSignIn(user_info)
        }).catch(error => {
          this.is_submitting = false
          this.password = ''
          this.error_msg = _.get(error, 'response.data.error.message', '')
        })
      },
      getUserInfo(attrs, include_label) {
        var user_info = _.pick(attrs, ['first_name', 'last_name', 'email'])

        // add Segment-friendly keys
        _.assign(user_info, {
          firstName: _.get(attrs, 'first_name'),
          lastName: _.get(attrs, 'last_name'),
          username: _.get(attrs, 'username'),
          createdAt: _.get(attrs, 'created')
        })

        // add current pathname as 'label' (for Google Analytics)
        if (include_label === true) {
          _.assign(user_info, { label: window.location.pathname })
        }

        // remove null values
        user_info = _.omitBy(user_info, _.isNil)

        return user_info
      },
      trackSignIn(attrs) {
        var eid = _.get(attrs, 'eid', '')

        if (window.analytics && eid.length > 0) {
          // identify user
          window.analytics.identify(eid, this.getUserInfo(attrs))

          // track sign in
          setTimeout(() => {
            window.analytics.track('Signed In', this.getUserInfo(attrs, true))
          }, 100)
        }
      }
    }
  }
</script>
