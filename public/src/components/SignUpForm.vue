<template>
  <form @submit.prevent>
    <div class="tc" style="margin-top: -76px">
      <img src="../assets/logo-square-80x80.png" alt="Flex.io" class="br-100 ba bw1 b--white" style="width: 84px; box-shadow: 0 0 3px rgba(0,0,0,0.4)">
    </div>
    <h1 class="fw6 tc mb4">Sign up for Flex.io</h1>
    <div class="mb3" v-if="error_msg">
      <div class="el-alert el-alert--error is-light">
        {{error_msg}}
      </div>
    </div>
    <div v-if="email_provided" class="mv3">
      <input
        type="email"
        placeholder="Email"
        autocomplete=off
        spellcheck="false"
        class="bg-black-10"
        disabled
        :class="input_cls"
        v-model="email"
      >
    </div>
    <div class="mv3 flex flex-row">
      <div class="flex-fill mr3">
        <input
          ref="input-firstname"
          type="text"
          placeholder="First name"
          autocomplete=off
          spellcheck="false"
          :class="input_cls"
          v-model="first_name"
        >
      </div>
      <div class="flex-fill">
        <input
          type="text"
          placeholder="Last name"
          autocomplete=off
          spellcheck="false"
          :class="input_cls"
          v-model="last_name"
        >
      </div>
    </div>
    <div class="mv3">
      <input
        type="text"
        placeholder="Pick a username"
        autocomplete=off
        spellcheck="false"
        :class="input_cls"
        v-model="username"
      >
      <span class="f8 dark-red" v-show="has_username_error">{{username_error}}</span>
    </div>
    <div v-if="!email_provided" class="mv3">
      <input
        type="email"
        placeholder="Email"
        autocomplete=off
        spellcheck="false"
        :class="input_cls"
        v-model="email"
      >
      <span class="f8 dark-red" v-show="has_email_error">{{email_error}}</span>
    </div>
    <div class="mv3">
      <input
        type="password"
        placeholder="Password"
        autocomplete=off
        spellcheck="false"
        :class="input_cls"
        @keyup.enter="trySignUp"
        v-model="password"
      >
      <span class="f8 dark-red" v-show="has_password_error">{{password_error}}</span>
    </div>
    <div class="mv3">
      <button
        type="button"
        :class="button_cls"
        :disabled="is_submitting || has_errors"
        @click="trySignUp"
      >
        <span v-if="is_submitting">{{label_submitting}}</span>
        <span v-else>Sign up</span>
      </button>
      <div class="mt1 f8 fw6 black-60">
        By signing up, you agree to Flex.io's
        <a class="link underline-hover blue" href="/terms" target="_blank" rel="noopener noreferrer">Terms</a> and
        <a class="link underline-hover blue" href="/privacy" target="_blank" rel="noopener noreferrer">Privacy Policy</a>.
      </div>
    </div>
    <div class="mt3 pt2 f5 fw6 tc">
      Already have an account?
      <button type="button" class="link dib blue underline-hover db fw6" @click="$emit('sign-in-click')">Sign in</button>
    </div>
  </form>
</template>

<script>
  import axios from 'axios'

  export default {
    props: {
      signinOnSignup: {
        type: Boolean,
        default: true
      }
    },
    data() {
      return {
        first_name: '',
        last_name: '',
        username: '',
        email: '',
        password: '',
        label_submitting: 'Creating account...',
        email_provided: false,
        is_submitting: false,
        is_sent: false,
        error_msg: '',
        ss_errors: {},
        verify_code: '',
        input_cls: 'input-reset ba b--black-10 br2 focus-b--blue lh-title ph3 pv2a w-100',
        button_cls: 'border-box no-select ttu fw6 w-100 ph4 pv2a lh-title white bg-blue br2 darken-10'
      }
    },
    watch: {
      email: function(val, old_val) { this.checkSignup('email') },
      username: function(val, old_val) { this.checkSignup('username') },
      password: function(val, old_val) { this.checkSignup('password') }
    },
    computed: {
      email_error() {
        return _.get(this.ss_errors, 'email.message', '')
      },
      username_error() {
        return _.get(this.ss_errors, 'username.message', '')
      },
      password_error() {
        return _.get(this.ss_errors, 'password.message', '')
      },
      has_email_error() {
        return this.email_error.length > 0
      },
      has_username_error() {
        return this.username_error.length > 0
      },
      has_password_error() {
        return this.password_error.length > 0
      },
      has_errors() {
        return this.has_email_error || this.has_username_error || this.has_password_error
      }
    },
    mounted() {
      this.$refs['input-firstname'].focus()
    },
    methods: {
      getAttrs() {
        // assemble non-empty values for submitting to the backend
        return _.pick(this.$data, ['first_name', 'last_name', 'username', 'email', 'password', 'verify_code'])
      },
      getSignInAttrs() {
        // massage attributes to match login call's expected params
        var attrs = this.getAttrs()
        attrs = _.pick(attrs, ['email', 'password'])

        return _.mapKeys(attrs, (val, key) => {
          if (key == 'email')
            return 'username'

          return key
        })
      },
      checkSignup: _.debounce(function(validate_key, callback) {
        var attrs = this.getAttrs()

        var validate_attrs = [{
          key: 'email',
          value: _.get(attrs, 'email', ''),
          type: 'email'
        },{
          key: 'username',
          value: _.get(attrs, 'username', ''),
          type: 'username'
        },{
          key: 'password',
          value: _.get(attrs, 'password', ''),
          type: 'password'
        }]

        // if a validation key is provided; only run validation on that key
        if (!_.isNil(validate_key))
        {
          validate_attrs = _.filter(validate_attrs, (attr) => {
            return attr.key == validate_key || _.has(this.ss_errors, attr.key)
          })
        }

        axios.post('/api/v2/validate', validate_attrs).then(response => {
          this.ss_errors = _.keyBy(response.data, 'key')

          if (_.isFunction(callback))
            callback()
        })
      }, 500),
      trySignUp() {
        var attrs = this.getAttrs()

        this.is_submitting = true

        // check server-side errors
        this.checkSignup(null, () => {
          if (this.has_errors) {
            this.is_submitting = false
            return
          }

          axios.post('/api/v2/signup', attrs).then(response => {
            var user_info =  _.get(response, 'data', {})
            this.$emit('signed-up', user_info)
            if (this.signinOnSignup === true) {
              this.trySignIn()
            }
            this.trackSignUp(user_info)
          }).catch(error => {
            this.is_submitting = false
            this.password = ''
            this.error_msg = _.get(error, 'response.data.error.message', '')
          })
        })
      },
      trySignIn() {
        var attrs = this.getSignInAttrs()

        this.label_submitting = 'Signing in...'
        this.is_submitting = true

        axios.post('/api/v2/login', attrs).then(response => {
          var user_info =  _.get(response, 'data', {})
          this.$emit('signed-in', user_info)
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
      trackSignUp(attrs) {
        var eid = _.get(attrs, 'eid', '')

        if (window.analytics && eid.length > 0) {
          // identify user
          window.analytics.identify(eid, this.getUserInfo(attrs))

          // track sign in
          setTimeout(() => {
            window.analytics.track('Signed Up', this.getUserInfo(attrs, true))
          }, 100)
        }
      }
    }
  }
</script>
