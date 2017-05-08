<template>
  <main class="pa3 ph3-m pa5-ns black-60 overflow-auto">
    <form @submit.prevent class="measure-wide center">
      <div class="tc">
        <img src="../assets/logo-flexio-header.png" alt="Flex.io">
      </div>
      <legend class="f3 tc ph0 mh0 mv3 black-80 w-100">Sign up for Flex.io</legend>
      <div v-if="!is_valid_invite_code" class="mh5-ns">
        <div class="mt3 mb4 lh-copy">
          <p>Thanks so much for your interest in Flex.io! We're still in private beta and a valid invitation code is required in order to sign up. We'd still love to hear from you! Drop us a line at <a href="mailto:hello@flex.io?subject=Flex.io%20Beta%20Invite%20Request" target="_blank" rel="noopener noreferrer">hello@flex.io</a> and we'll see if we can help you out with that!</p>
        </div>
      </div>
      <div v-else class="mh5-ns">
        <div v-if="error_msg" class="mv3 ph3 pv2a lh-title fw6 br1 white bg-dark-red">
          {{error_msg}}
        </div>
        <div v-if="email_provided" class="mv3">
          <input v-model="email" :class="input_cls" class="bg-black-10" placeholder="Email" type="email" autocomplete=off spellcheck="false" disabled>
        </div>
        <div class="mv3">
          <input v-model="first_name" v-focus :class="input_cls" placeholder="First name" type="text" autocomplete=off spellcheck="false">
        </div>
        <div class="mv3">
          <input v-model="last_name" :class="input_cls" placeholder="Last name" type="text" autocomplete=off spellcheck="false">
        </div>
        <div class="mv3">
          <input v-model="user_name" :class="input_cls" placeholder="Pick a username" type="text" autocomplete=off spellcheck="false">
          <span class="f8 dark-red" v-show="has_username_error">{{username_error}}</span>
        </div>
        <div v-if="!email_provided" class="mv3">
          <input v-model="email" :class="input_cls" placeholder="Email" type="email" autocomplete=off spellcheck="false">
          <span class="f8 dark-red" v-show="has_email_error">{{email_error}}</span>
        </div>
        <div class="mv3">
          <input v-model="password" @keyup.enter="trySignUp" :class="input_cls" placeholder="Password" type="password" autocomplete=off spellcheck="false">
          <span class="f8 dark-red" v-show="has_password_error">{{password_error}}</span>
        </div>
        <div class="mv3">
          <btn btn-lg btn-primary :disabled="is_submitting" @click="trySignUp" class="b ttu w-100">
            <span v-if="is_submitting">{{label_submitting}}</span>
            <span v-else>Sign up</span>
          </btn>
          <span class="f8 fw6">
            By signing up, you agree to Flex.io's
            <a class="link underline-hover blue" href="/terms" target="_blank" rel="noopener noreferrer">Terms</a> and
            <a class="link underline-hover blue" href="/privacy" target="_blank" rel="noopener noreferrer">Privacy Policy</a>.
          </span>
        </div>
      </div>
      <div class="tc f5 fw6 mt4 mb3">
        Already have an account? <router-link to="/signin" class="link dib blue underline-hover db">Sign in</router-link>
      </div>
    </form>
  </main>
</template>

<script>
  import api from '../api'
  import Btn from './Btn.vue'
  import Redirect from './mixins/redirect'

  const INVITE_CODES = [
    'e0miwu7qkv89h3rlrnst25e3jdxbbrpn'
  ]

  export default {
    mixins: [Redirect],
    components: {
      Btn
    },
    beforeRouteEnter(to, from, next) {
      next(vm => {
        // access to component instance via `vm`
        vm.verify_code = _.get(to, 'query.verify_code', '')
        vm.invite_code = _.get(to, 'query.invite_code', '')
        vm.email = _.get(to, 'query.email', '')

        // if an email address has been provided to us, this indicates
        // the user has been invited to Flex.io; disable the email input
        // and show it at the top of the form -- all the user needs
        // to do is to enter their first/last name, username and
        // a password to create their account
        if (vm.email.length > 0)
          vm.email_provided = true

        // TODO: remove? is this really necessary?
        // now that we have them, we can remove the query params from the url
        //vm.$router.replace(to.path)
      })
    },
    data() {
      return {
        first_name: '',
        last_name: '',
        user_name: '',
        email: '',
        password: '',
        label_submitting: 'Creating account...',
        email_provided: false,
        is_submitting: false,
        is_sent: false,
        error_msg: '',
        ss_errors: {},
        invite_code: '',
        verify_code: '',
        input_cls: 'input-reset ba b--black-20 focus-b--transparent focus-outline focus-ow1 focus-o--blue lh-title ph3 pv2a w-100'
      }
    },
    watch: {
      email: function(val, old_val) { this.checkSignup('email') },
      user_name: function(val, old_val) { this.checkSignup('user_name') },
      password: function(val, old_val) { this.checkSignup('password') }
    },
    computed: {
      is_valid_invite_code() { return _.includes(INVITE_CODES, this.invite_code) },
      email_error() { return _.get(this.ss_errors, 'email.message', '') },
      username_error() { return _.get(this.ss_errors, 'user_name.message', '') },
      password_error() { return _.get(this.ss_errors, 'password.message', '') },
      has_email_error() { return this.email_error.length > 0 },
      has_username_error() { return this.username_error.length > 0 },
      has_password_error() { return this.password_error.length > 0 },
    },
    methods: {
      getAttrs() {
        // assemble non-empty values for submitting to the backend
        return _
          .chain(this.$data)
          .pick(['first_name', 'last_name', 'user_name', 'email', 'password', 'verify_code'])
          .value()
      },
      getSignInAttrs() {
        // massage attributes to match login call's expected params
        return _
          .chain(this.getAttrs())
          .pick(['email', 'password'])
          .mapKeys(function(val, key) {
            if (key == 'email')
              return 'username'

            return key
          })
          .value()
      },
      checkSignup: _.debounce(function(validate_key, callback) {
        var attrs = this.getAttrs()

        var validate_attrs = [{
          key: 'email',
          value: _.get(attrs, 'email', ''),
          type: 'email'
        },{
          key: 'user_name',
          value: _.get(attrs, 'user_name', ''),
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

        api.validate({ attrs: validate_attrs }).then(response => {
          this.ss_errors = _.keyBy(response.body, 'key')

          if (_.isFunction(callback))
            callback()
        }, response => {
          // error callback
        })
      }, 300),
      trySignUp() {
        var attrs = this.getAttrs()

        this.is_submitting = true

        // this will show errors below each input
        this.checkSignup(null, () => {
          if (this.has_email_error ||
              this.has_username_error ||
              this.has_password_error)
          {
            return
          }

          this.$store.dispatch('signUp', { attrs }).then(response => {
            // success callback
            this.is_submitting = false
            this.trySignIn()
          }, response => {
            // error callback
            this.is_submitting = false
            this.password = ''
            this.showErrors(_.get(response, 'data.errors'))
          })
        })
      },
      trySignIn() {
        var attrs = this.getSignInAttrs()

        this.label_submitting = 'Signing in...'
        this.is_submitting = true

        this.$store.dispatch('signIn', { attrs }).then(response => {
          if (response.ok)
          {
            this.is_submitting = false
            this.redirect()
          }
           else
          {
            this.is_submitting = false
            this.password = ''
            this.showErrors(_.get(response, 'data.errors'))
          }
        })
      },
      showErrors: function(errors) {
        if (_.isArray(errors) && errors.length > 0)
          this.error_msg = errors[0].message
      }
    }
  }
</script>
