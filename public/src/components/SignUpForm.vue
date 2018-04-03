<template>
  <form @submit.prevent>
    <div class="tc">
      <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIgAAAAoCAMAAAASeEKOAAAAgVBMVEVMaXEiHh8iHh8iHh8iHh8iHh92vB4iHh8iHh8iHh9zth4iHR8iHh8iHh8iHh92vB52vB4iHh8iHh8iHh8iHh8iHh8iHh92vB4iHh92vB4iHh92vB52vB52vB52vB52vB4iHh8iHh92vB4iHh92vB52vB52vB52vB52vB52vB4iHh9CYC3gAAAAKXRSTlMA8Agx6dvppBYmB3TWSx7IMveymT/B4vKHJNAVnGOre1djjv3aQLlROAQZyksAAARtSURBVFjDxZjr1qogEIZRTFPT8pjHrMz68v4vcCMMCYZlq9ba86uU4nHmnYMgxM12rTC0Mm+N/qvpp0LriQVG6Dnsko9/xJTvysVr/bAfzbIHsszoDVf/HqM87++XeuHiddKLtvIQcukn92sO53wf7LZstdvLZqF1QT8UX7skNynIdZlDUpkjiJG9Yr75WiZ/DGTrLFnsAUARZdbAZOkcZPM1SLmlINUnkUl88hlHvYbR70DQbXDJNl+0NmIgMWANCv0dCPo7H5qF+WvRTQ1x0x+CfGDWszCVID6O49jWf1Ljuu4vd9Qg6WuQtRsWhqYZmyTDoHErImZlwu9iy6KXRtj8XBE7NMKmu+Z6Mfd787I9dx+D6O5qTG+D7W1v2NdshGXVR6yDuz3NmssDJK/YFWr76+0zEDuUK01BhR0H9IvmTVQfCX+0k+uIU5t32apSDeKoQPBmUnp7IxbyvgDcYyt9VYA41f3Jtju2EluJweppQu30DGKv+idLB6HooegCvuyI5kBUHCRslAQbkx2yJxCdt0QjiaKQrw8HQWIWnPY4OpY0qlmQ82Nz83IZY7Qtx7C+AoEAtBHNFjtqhQeHeykpyieIkz8LcuMyvdZ5Web1gZOcyd3wLciaCSRwJx2BdmYeHEu3Dd4w50Cg7dzNmqcQV+6+ezzHCxBYkUzF3XrCwta1euUEI4DUsKuQsdxHB3L7VKQQ6JSaOwEBhYhP6rWCGCCH++fMnYA4V4iDeBvg7oNedZgTNewPtp6A+NrUIZwNpiZxqirW8yA5e3pTbsQQrkZwtaYuaDGIAAsWjhk8ymT4ixjNg9wgCvJ9cEklgvhKEJBIQPoMN0OuqLzUE6GgFyCN+OzjkCDivQRx+zkLeOWCitqH6BUIFJHJQA8B+w2IGyhLyDIQcynIaRakBZAxb8LPQ7Nb7BGYrDfWk7FK++j96vegEaRWivW2WKx2MBt/sbyJTVkNArI05fm1UqSvGkQvoMrMcEDooCo+FZIRpLwoYgNavXfvQVCmaKr2Awu6txGH6tIqlHhQ60WoaLzabssFIHg6hxF5rngM+IjgIqxJClaA/MHTjy85j/mkRgtAdBBBetL5EQbxgsE2zMbhxGXVJLXBVW521OUxgG97qakDnO56lxzyBgRBg+/bxI0x9lzogsOZBXjLsIVSH1KZeOkwwegSCFcJQTk0zfnKx5N9hxaBoFP7aCaa9igapOfxwMB4mY45vF7By6M0oXV71ag4yvcdCPe6PMjjUce6nEDEPzFnkk8DbuYrjvcg6DSdbPuQ7OYxfa78SUkhisEtgOzk85FuO8Uw62lJCiSQVH4NxfJISQ+1ADYY88Qv+JTHghZg5BzkDjOcZIl22ImnAcZgKxHET+i15FGfdC8xAvqYbbDK6NJIo2vEyuGl7K9iZCdBYAxqyYkmTXEq21UmsOzNqpPef7FHTXy71mN6KRau+cfMCsPQilxMrzrsV57//LOjPRyX0vQlJM25lrYru6Y6EKuabiz3/wD8XkeE+HHPxgAAAABJRU5ErkJggg==" alt="Flex.io">
    </div>
    <legend class="f3 tc ph0 mh0 mv3 black-80 w-100">Sign up for Flex.io</legend>
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
          v-model="user_name"
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
        <span class="f8 fw6 black-60">
          By signing up, you agree to Flex.io's
          <a class="link underline-hover blue" href="/terms" target="_blank" rel="noopener noreferrer">Terms</a> and
          <a class="link underline-hover blue" href="/privacy" target="_blank" rel="noopener noreferrer">Privacy Policy</a>.
        </span>
      </div>
    </div>
    <div class="tc f5 fw6 mt4">
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
        user_name: '',
        email: '',
        password: '',
        label_submitting: 'Creating account...',
        email_provided: false,
        is_submitting: false,
        is_sent: false,
        error_msg: '',
        ss_errors: {},
        verify_code: '',
        input_cls: 'input-reset ba b--black-10 focus-b--transparent focus-outline focus-o--blue lh-title ph3 pv2a w-100'
      }
    },
    watch: {
      email: function(val, old_val) { this.checkSignup('email') },
      user_name: function(val, old_val) { this.checkSignup('user_name') },
      password: function(val, old_val) { this.checkSignup('password') }
    },
    computed: {
      email_error() {
        return _.get(this.ss_errors, 'email.message', '')
      },
      username_error() {
        return _.get(this.ss_errors, 'user_name.message', '')
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
        return _.pick(this.$data, ['first_name', 'last_name', 'user_name', 'email', 'password', 'verify_code'])
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

        axios.post('/api/v1/validate', validate_attrs).then(response => {
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

          axios.post('/api/v1/users', attrs).then(response => {
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

        axios.post('/api/v1/login', attrs).then(response => {
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
          username: _.get(attrs, 'user_name'),
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
