<template>
  <main class="pa3 ph3-m pa5-ns black-60 overflow-auto">
    <form @submit.prevent class="measure-wide center">
      <div class="tc">
        <img src="../assets/logo-flexio-header.png" alt="Flex.io">
      </div>
      <legend class="f3 tc ph0 mh0 mv3 black-80 w-100">Reset Password</legend>
      <div v-if="is_sent" class="mh5-ns">
        <div class="mv3 lh-copy">
          <p>Your password has been successfully reset. To continue using Flex.io, you will need to sign in again.</p>
          <router-link to="/signin" class="link ph4 pv2a b lh-title white bg-blue b--blue darken-10 ttu tc db">Sign in</router-link>
        </div>
      </div>
      <div v-else class="mh5-ns">
        <div class="mv3 lh-copy">
          <p>Please enter a new password for your account.</p>
        </div>
        <div v-if="error_msg" class="mv3 ph3 pv2a lh-title fw6 br1 white bg-dark-red">
          {{error_msg}}
        </div>
        <div v-show="false">
          <input v-model="email" type="hidden">
          <input v-model="verify_code" type="hidden">
        </div>
        <div class="mv3">
          <input
            type="password"
            name="password"
            placeholder="Password"
            autocomplete=off
            spellcheck="false"
            :class="input_cls"
            v-model="password"
            v-focus
            v-validate
            data-vv-as="password"
            data-vv-name="password"
            data-vv-value-path="password"
            data-vv-rules="required"
          >
          <span class="f8 dark-red" v-show="has_password_error">{{password_error}}</span>
        </div>
        <div class="mv3">
          <input
            type="password"
            name="password2"
            placeholder="Retype Password"
            autocomplete=off
            spellcheck="false"
            :class="input_cls"
            @keyup.enter="tryChangePassword"
            v-model="password2"
            v-validate
            data-vv-as="password"
            data-vv-name="password2"
            data-vv-value-path="password2"
            data-vv-rules="required|confirmed:password"
          >
          <span class="f8 dark-red" v-show="has_confirm_password_error">{{confirm_password_error}}</span>
        </div>
        <div class="mv3">
          <btn btn-lg btn-primary :disabled="is_submitting" @click="tryChangePassword" class="b ttu w-100">
            <span v-if="is_submitting">Submitting...</span>
            <span v-else>Change password</span>
          </btn>
        </div>
      </div>
    </form>
  </main>
</template>

<script>
  import api from '../api'
  import Btn from './Btn.vue'

  export default {
    components: {
      Btn
    },
    beforeRouteEnter(to, from, next) {
      next(vm => {
        // access to component instance via `vm`
        vm.email = _.get(to, 'query.email', '')
        vm.verify_code = _.get(to, 'query.verify_code', '')

        // now that we have them, we can remove the query params from the url
        vm.$router.replace(to.path)
      })
    },
    watch: {
      password: function(val, old_val) {
        if (val.length > 0)
          this.validateForm('password')
      }
    },
    data() {
      return {
        email: '',
        verify_code: '',
        password: '',
        password2: '',
        is_submitting: false,
        is_sent: false,
        error_msg: '',
        ss_errors: {},
        input_cls: 'input-reset ba b--black-20 focus-b--transparent focus-outline focus-ow1 focus-o--blue lh-title ph3 pv2a w-100'
      }
    },
    computed: {
      password_error() { return _.get(this.ss_errors, 'password.message', '') },
      confirm_password_error() { return _.defaultTo(this.errors.first('password2'), '') },
      has_password_error() { return this.password_error.length > 0 },
      has_confirm_password_error() { return this.confirm_password_error.length > 0 }
    },
    mounted() {
      analytics.track('Visited Reset Password Page')
    },
    methods: {
      getAttrs() {
        // assemble non-empty values for submitting to the backend
        return _
          .chain(this.$data)
          .pick(['email', 'verify_code', 'password', 'password2'])
          .omitBy(_.isEmpty)
          .value()
      },
      validateForm: _.debounce(function(validate_key, callback) {
        var validate_attrs = [{
          key: 'password',
          value: this.password,
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
            callback.call(this)
        }, response => {
          // error callback
        })
      }, 300),
      tryChangePassword() {
        this.$validator.validateAll().then(success => {
          // client-side validation failed; bail out
          if (!success)
            return

          // this will show errors below each input
          this.validateForm(null, () => {
            if (this.has_password_error)
              return

            var attrs = this.getAttrs()

            this.is_submitting = true

            api.resetPassword({ attrs }).then(response => {
              // success callback
              this.is_submitting = false
              this.is_sent = true
            }, response => {
              // error callback
              this.is_submitting = false
              this.error_msg = _.get(response, 'data.error.message', '')
            })
          })
        })
      }
    }
  }
</script>
