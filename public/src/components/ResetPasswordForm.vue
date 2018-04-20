<template>
  <form @submit.prevent>
    <div class="tc">
      <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIgAAAAoCAMAAAASeEKOAAAAgVBMVEVMaXEiHh8iHh8iHh8iHh8iHh92vB4iHh8iHh8iHh9zth4iHR8iHh8iHh8iHh92vB52vB4iHh8iHh8iHh8iHh8iHh8iHh92vB4iHh92vB4iHh92vB52vB52vB52vB52vB4iHh8iHh92vB4iHh92vB52vB52vB52vB52vB52vB4iHh9CYC3gAAAAKXRSTlMA8Agx6dvppBYmB3TWSx7IMveymT/B4vKHJNAVnGOre1djjv3aQLlROAQZyksAAARtSURBVFjDxZjr1qogEIZRTFPT8pjHrMz68v4vcCMMCYZlq9ba86uU4nHmnYMgxM12rTC0Mm+N/qvpp0LriQVG6Dnsko9/xJTvysVr/bAfzbIHsszoDVf/HqM87++XeuHiddKLtvIQcukn92sO53wf7LZstdvLZqF1QT8UX7skNynIdZlDUpkjiJG9Yr75WiZ/DGTrLFnsAUARZdbAZOkcZPM1SLmlINUnkUl88hlHvYbR70DQbXDJNl+0NmIgMWANCv0dCPo7H5qF+WvRTQ1x0x+CfGDWszCVID6O49jWf1Ljuu4vd9Qg6WuQtRsWhqYZmyTDoHErImZlwu9iy6KXRtj8XBE7NMKmu+Z6Mfd787I9dx+D6O5qTG+D7W1v2NdshGXVR6yDuz3NmssDJK/YFWr76+0zEDuUK01BhR0H9IvmTVQfCX+0k+uIU5t32apSDeKoQPBmUnp7IxbyvgDcYyt9VYA41f3Jtju2EluJweppQu30DGKv+idLB6HooegCvuyI5kBUHCRslAQbkx2yJxCdt0QjiaKQrw8HQWIWnPY4OpY0qlmQ82Nz83IZY7Qtx7C+AoEAtBHNFjtqhQeHeykpyieIkz8LcuMyvdZ5Web1gZOcyd3wLciaCSRwJx2BdmYeHEu3Dd4w50Cg7dzNmqcQV+6+ezzHCxBYkUzF3XrCwta1euUEI4DUsKuQsdxHB3L7VKQQ6JSaOwEBhYhP6rWCGCCH++fMnYA4V4iDeBvg7oNedZgTNewPtp6A+NrUIZwNpiZxqirW8yA5e3pTbsQQrkZwtaYuaDGIAAsWjhk8ymT4ixjNg9wgCvJ9cEklgvhKEJBIQPoMN0OuqLzUE6GgFyCN+OzjkCDivQRx+zkLeOWCitqH6BUIFJHJQA8B+w2IGyhLyDIQcynIaRakBZAxb8LPQ7Nb7BGYrDfWk7FK++j96vegEaRWivW2WKx2MBt/sbyJTVkNArI05fm1UqSvGkQvoMrMcEDooCo+FZIRpLwoYgNavXfvQVCmaKr2Awu6txGH6tIqlHhQ60WoaLzabssFIHg6hxF5rngM+IjgIqxJClaA/MHTjy85j/mkRgtAdBBBetL5EQbxgsE2zMbhxGXVJLXBVW521OUxgG97qakDnO56lxzyBgRBg+/bxI0x9lzogsOZBXjLsIVSH1KZeOkwwegSCFcJQTk0zfnKx5N9hxaBoFP7aCaa9igapOfxwMB4mY45vF7By6M0oXV71ag4yvcdCPe6PMjjUce6nEDEPzFnkk8DbuYrjvcg6DSdbPuQ7OYxfa78SUkhisEtgOzk85FuO8Uw62lJCiSQVH4NxfJISQ+1ADYY88Qv+JTHghZg5BzkDjOcZIl22ImnAcZgKxHET+i15FGfdC8xAvqYbbDK6NJIo2vEyuGl7K9iZCdBYAxqyYkmTXEq21UmsOzNqpPef7FHTXy71mN6KRau+cfMCsPQilxMrzrsV57//LOjPRyX0vQlJM25lrYru6Y6EKuabiz3/wD8XkeE+HHPxgAAAABJRU5ErkJggg==" alt="Flex.io">
    </div>
    <legend class="f3 tc ph0 mh0 mv3 black-80 w-100" v-show="false">Reset Password</legend>
    <div v-if="is_sent">
      <div class="mv3 lh-copy">
        <p>Your password has been successfully reset. To continue using Flex.io, you will need to sign in again.</p>
        <router-link to="/signin" class="link ph4 pv2a b lh-title white bg-blue b--blue darken-10 ttu tc db">Sign in</router-link>
      </div>
    </div>
    <div v-else>
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
          ref="input-password"
          type="password"
          name="password"
          placeholder="Password"
          autocomplete=off
          spellcheck="false"
          :class="input_cls"
          v-model="password"
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
      <div class="mt3">
        <btn btn-lg btn-primary :disabled="is_submitting" @click="tryChangePassword" class="ttu b w-100">
          <span v-if="is_submitting">Submitting...</span>
          <span v-else>Change password</span>
        </btn>
      </div>
    </div>
  </form>
</template>

<script>
  import _ from 'lodash'
  import api from '../api'
  import Btn from './Btn.vue'

  export default {
    components: {
      Btn
    },
    watch: {
      password: function(val, old_val) {
        if (val.length > 0)
          this.validateForm('password')
      }
    },
    data() {
      return {
        email: _.get(this.$route, 'query.email', ''),
        verify_code: _.get(this.$route, 'query.verify_code', ''),
        password: '',
        password2: '',
        is_submitting: false,
        is_sent: false,
        error_msg: '',
        ss_errors: {},
        input_cls: 'input-reset ba b--black-10 focus-b--transparent focus-outline focus-o--blue lh-title ph3 pv2a w-100'
      }
    },
    computed: {
      password_error() { return _.get(this.ss_errors, 'password.message', '') },
      confirm_password_error() { return _.defaultTo(this.errors.first('password2'), '') },
      has_password_error() { return this.password_error.length > 0 },
      has_confirm_password_error() { return this.confirm_password_error.length > 0 }
    },
    mounted() {
      this.$refs['input-password'].focus()
    },
    methods: {
      getAttrs() {
        // assemble non-empty values for submitting to the backend
        var attrs = _.assign({}, this.$data)
        attrs = _.pick(attrs, ['email', 'verify_code', 'password'])
        return _.omitBy(attrs, _.isEmpty)
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
