<template>
  <form @submit.prevent>
    <div class="tc">
      <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIgAAAAoCAMAAAASeEKOAAAAgVBMVEVMaXEiHh8iHh8iHh8iHh8iHh92vB4iHh8iHh8iHh9zth4iHR8iHh8iHh8iHh92vB52vB4iHh8iHh8iHh8iHh8iHh8iHh92vB4iHh92vB4iHh92vB52vB52vB52vB52vB4iHh8iHh92vB4iHh92vB52vB52vB52vB52vB52vB4iHh9CYC3gAAAAKXRSTlMA8Agx6dvppBYmB3TWSx7IMveymT/B4vKHJNAVnGOre1djjv3aQLlROAQZyksAAARtSURBVFjDxZjr1qogEIZRTFPT8pjHrMz68v4vcCMMCYZlq9ba86uU4nHmnYMgxM12rTC0Mm+N/qvpp0LriQVG6Dnsko9/xJTvysVr/bAfzbIHsszoDVf/HqM87++XeuHiddKLtvIQcukn92sO53wf7LZstdvLZqF1QT8UX7skNynIdZlDUpkjiJG9Yr75WiZ/DGTrLFnsAUARZdbAZOkcZPM1SLmlINUnkUl88hlHvYbR70DQbXDJNl+0NmIgMWANCv0dCPo7H5qF+WvRTQ1x0x+CfGDWszCVID6O49jWf1Ljuu4vd9Qg6WuQtRsWhqYZmyTDoHErImZlwu9iy6KXRtj8XBE7NMKmu+Z6Mfd787I9dx+D6O5qTG+D7W1v2NdshGXVR6yDuz3NmssDJK/YFWr76+0zEDuUK01BhR0H9IvmTVQfCX+0k+uIU5t32apSDeKoQPBmUnp7IxbyvgDcYyt9VYA41f3Jtju2EluJweppQu30DGKv+idLB6HooegCvuyI5kBUHCRslAQbkx2yJxCdt0QjiaKQrw8HQWIWnPY4OpY0qlmQ82Nz83IZY7Qtx7C+AoEAtBHNFjtqhQeHeykpyieIkz8LcuMyvdZ5Web1gZOcyd3wLciaCSRwJx2BdmYeHEu3Dd4w50Cg7dzNmqcQV+6+ezzHCxBYkUzF3XrCwta1euUEI4DUsKuQsdxHB3L7VKQQ6JSaOwEBhYhP6rWCGCCH++fMnYA4V4iDeBvg7oNedZgTNewPtp6A+NrUIZwNpiZxqirW8yA5e3pTbsQQrkZwtaYuaDGIAAsWjhk8ymT4ixjNg9wgCvJ9cEklgvhKEJBIQPoMN0OuqLzUE6GgFyCN+OzjkCDivQRx+zkLeOWCitqH6BUIFJHJQA8B+w2IGyhLyDIQcynIaRakBZAxb8LPQ7Nb7BGYrDfWk7FK++j96vegEaRWivW2WKx2MBt/sbyJTVkNArI05fm1UqSvGkQvoMrMcEDooCo+FZIRpLwoYgNavXfvQVCmaKr2Awu6txGH6tIqlHhQ60WoaLzabssFIHg6hxF5rngM+IjgIqxJClaA/MHTjy85j/mkRgtAdBBBetL5EQbxgsE2zMbhxGXVJLXBVW521OUxgG97qakDnO56lxzyBgRBg+/bxI0x9lzogsOZBXjLsIVSH1KZeOkwwegSCFcJQTk0zfnKx5N9hxaBoFP7aCaa9igapOfxwMB4mY45vF7By6M0oXV71ag4yvcdCPe6PMjjUce6nEDEPzFnkk8DbuYrjvcg6DSdbPuQ7OYxfa78SUkhisEtgOzk85FuO8Uw62lJCiSQVH4NxfJISQ+1ADYY88Qv+JTHghZg5BzkDjOcZIl22ImnAcZgKxHET+i15FGfdC8xAvqYbbDK6NJIo2vEyuGl7K9iZCdBYAxqyYkmTXEq21UmsOzNqpPef7FHTXy71mN6KRau+cfMCsPQilxMrzrsV57//LOjPRyX0vQlJM25lrYru6Y6EKuabiz3/wD8XkeE+HHPxgAAAABJRU5ErkJggg==" alt="Flex.io">
    </div>
    <legend class="f3 tc ph0 mh0 mv3 black-80 w-100" v-show="false">Forgot Password</legend>
    <div v-if="is_sent">
      <div class="mv3 lh-copy">
        <p>An email has been sent to <span class="b">{{email}}</span> with further instructions.</p>
        <p>You may need to check your spam folder or unblock no-reply@flex.io.</p>
        <button class="link ph4 pv2a b lh-title white bg-blue b--blue darken-10 ttu tc w-100" @click="$emit('sign-in-click')">Sign in</button>
      </div>
    </div>
    <div v-else>
      <div class="mv3 lh-copy">
        <p>Enter the email address you signed up with below. An email will be sent containing a link to reset your password.</p>
        <p>You may need to check your spam folder or unblock <span class="fw6">no-reply@flex.io</span>.</p>
      </div>
      <div v-if="error_msg" class="mv3 ph3 pv2a lh-title fw6 br1 white bg-dark-red">
        {{error_msg}}
      </div>
      <div class="mv3">
        <input
          ref="input-email"
          type="email"
          placeholder="Email address"
          autocomplete=off
          spellcheck="false"
          :class="input_cls"
          @keyup.enter="sendReset"
          v-model="email"
        >
      </div>
      <div class="mt3">
        <btn btn-lg btn-primary :disabled="is_submitting" @click="sendReset" class="ttu b w-100">
          <span v-if="is_submitting">Sending reset instructions...</span>
          <span v-else>Send reset instructions</span>
        </btn>
      </div>
    </div>
    <div class="mt3 pt2 f5 fw6 tc">
      New to Flex.io?
      <button type="button" class="link dib blue underline-hover db fw6" @click="$emit('sign-up-click')">Sign up</button>
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
        email: '',
        is_submitting: false,
        is_sent: false,
        error_msg: '',
        input_cls: 'input-reset ba b--black-10 focus-b--transparent focus-outline focus-o--blue lh-title ph3 pv2a w-100'
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

        axios.post('/api/v2/forgotpassword', attrs).then(response => {
          this.is_submitting = false
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
