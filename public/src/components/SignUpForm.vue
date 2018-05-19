<template>
  <form @submit.prevent>
    <div class="tc mb3">
      <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGEAAAAcCAYAAABxutG8AAAACXBIWXMAAAWJAAAFiQFtaJ36AAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAARoSURBVHja7FpLbtswEFUMo13aN7BuYK+7sXqCKCeIcoI4QPeW9104J7B8gsoniLzpNsoJqtzAXrYo4JLFY8swMySlyAochMDAkEyKnzfz5iMFAd1GQlZCfgg5aHIn5FrIIHhvrbUz4t6lkMwxbickEbLB9VhILiTEf5GQh1M9lK/fp/IMlkKGQkq5ny+ftvtjzdczrqceAARYXA7AAowJjf9OFYAB9jPErQkACboCYVZzfKEtVG/hCbPDhLgXdglCTPQpcT82rERqx+MbpOgSlEop29FBGDF9InC/lCtcV0LSt+gkwf2RBkZ2bDoy/cGBiIZ82oGQ9+bZ+q849xgaN9E4t4AGyt89ETZT3Lz1mGdIRHetRm/CoY+wnxC/itokaxTCwh58QtQpwX3y+rOnJfiEv2qeVFsoFwJLClg41qf82MYCQEGAYBsjD/RA0NQZ03eM/cSOM/pL4eI5a5djPna7xqFEHiGw3Ni9lhhuGT+UWZLHjAAgswFQU/vnWuDiatJCMjHmDmHwExAGOJyEGTg3ZNpwzfMGDm4C0NSiF9i0CVjGzDchIp9ZSwCsGgYnUgELHYg+EqvIgp45UerBwxwFUbSTw1SH0KiQAEKOvdGopDQ0PIYi3Wo0RM2XEL6mqQUkFiottbUPGeXKFdWfNYhkUoOrfXzCD+JwlRmbucbKYpWPltLKDptThxASieit5yGzPgEOuGIOf2Zyvuh/DgaggopE9u8xD2yznRMLqGB9VLJ3xZQ9dBpZE30ULaXEfIUvAJ5KSAEQUU5X3NtAOUruWT1oY8502mEDutQFLWYmt9HCjOFSk1oqos+M2EPcolJRVhrbQlAkgKR1S0vpIV6+YDZegrd0WTdwRGZzFfgeCaUwnezewsut+wFQCxWUVOKQnT4SIFEBxKSLEDVkLOzgkAnj4M1EzRahLNsKR1+gUK4aVNR1nnCMtrBQZBf1rV2NvuQ63wII84AvNaensIHXqh01PZyKKEukDgdftEhJFUNRixrJ5zO/2wUIVJmiaJDwmW3gyccZLKUN50xFkJHMHYTj9Xm3QgY/XdBR/gJLuHQ43ZCYi8ofWnndiginYoB2RVbXDG3mrwVChMzYpuUrbG7FJIAJ4SATyI6Yb35EKpXW8M0szBkAUHWzTOYQXZWy58ziCyyuAF2MtYQrNPrF6DMiakcqKdxoIOUMJ/9LqvofPupfVSx///p54ypb4P97ht/Vm7gi+P/VSWzpG0oQunLMS2YxUeAua+v9NgFdnjbzgQ3uzQirlGvYCwDGBo3MxL1KAOFT3kgC+j3FEHP6VGoT9RlNVyGq/t62aalgA4uKiIglZWijIhLHpXZgPiUWzjdENXOEJ7SJmlLneYICos47hRKauw74cjhXluDKGgnoqrL4r8rl1wBE2CBjflbo6xsIFR4hmW86zgFxo1FFRFCUWkdmUEzC+CxbqLvFPKaGJ4J2LgT9JIZPuNUsQn1RWHA1KtDJBcrb3H4qtR+uxvRHgAEAAbWMsfVFqtEAAAAASUVORK5CYII=" alt="Flex.io">
    </div>
    <h1 class="b f3 tc ph0 mh0 mt0 mb3 black-80 w-100">Sign up for Flex.io</h1>
    <div>
      <div v-if="error_msg" class="mv3 ph3 pv2a lh-title fw6 br1 white bg-dark-red">
        {{error_msg}}
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
        <btn
          btn-lg
          btn-primary
          class="ttu b w-100"
          :disabled="is_submitting || has_errors"
          @click="trySignUp"
        >
          <span v-if="is_submitting">{{label_submitting}}</span>
          <span v-else>Sign up</span>
        </btn>
        <div class="mt1 f8 fw6 black-60">
          By signing up, you agree to Flex.io's
          <a class="link underline-hover blue" href="/terms" target="_blank" rel="noopener noreferrer">Terms</a> and
          <a class="link underline-hover blue" href="/privacy" target="_blank" rel="noopener noreferrer">Privacy Policy</a>.
        </div>
      </div>
    </div>
    <div class="mt3 pt2 f5 fw6 tc">
      Already have an account?
      <button type="button" class="link dib blue underline-hover db fw6" @click="$emit('sign-in-click')">Sign in</button>
    </div>
  </form>
</template>

<script>
  import _ from 'lodash'
  import axios from 'axios'
  import Btn from './Btn.vue'

  export default {
    components: {
      Btn
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
        input_cls: 'input-reset ba b--black-10 br2 focus-b--blue lh-title ph3 pv2a w-100'
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
      has_client_errors() {
        var errors = _.get(this.errors, 'errors', [])
        return _.size(errors) > 0
      },
      has_server_errors() {
        return this.has_email_error || this.has_username_error || this.has_password_error
      },
      has_errors() {
        return this.has_client_errors || this.has_server_errors
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
          if (this.has_errors)
          {
            this.is_submitting = false
            return
          }

          axios.post('/api/v2/signup', attrs).then(response => {
            var user_info =  _.get(response, 'data', {})
            this.is_submitting = false
            this.$emit('signed-up', user_info)
            this.trySignIn()
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
          this.is_submitting = false
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
