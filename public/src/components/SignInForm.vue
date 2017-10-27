<template>
  <form @submit.prevent>
    <div class="tc">
      <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIgAAAAoCAMAAAASeEKOAAAAgVBMVEVMaXEiHh8iHh8iHh8iHh8iHh92vB4iHh8iHh8iHh9zth4iHR8iHh8iHh8iHh92vB52vB4iHh8iHh8iHh8iHh8iHh8iHh92vB4iHh92vB4iHh92vB52vB52vB52vB52vB4iHh8iHh92vB4iHh92vB52vB52vB52vB52vB52vB4iHh9CYC3gAAAAKXRSTlMA8Agx6dvppBYmB3TWSx7IMveymT/B4vKHJNAVnGOre1djjv3aQLlROAQZyksAAARtSURBVFjDxZjr1qogEIZRTFPT8pjHrMz68v4vcCMMCYZlq9ba86uU4nHmnYMgxM12rTC0Mm+N/qvpp0LriQVG6Dnsko9/xJTvysVr/bAfzbIHsszoDVf/HqM87++XeuHiddKLtvIQcukn92sO53wf7LZstdvLZqF1QT8UX7skNynIdZlDUpkjiJG9Yr75WiZ/DGTrLFnsAUARZdbAZOkcZPM1SLmlINUnkUl88hlHvYbR70DQbXDJNl+0NmIgMWANCv0dCPo7H5qF+WvRTQ1x0x+CfGDWszCVID6O49jWf1Ljuu4vd9Qg6WuQtRsWhqYZmyTDoHErImZlwu9iy6KXRtj8XBE7NMKmu+Z6Mfd787I9dx+D6O5qTG+D7W1v2NdshGXVR6yDuz3NmssDJK/YFWr76+0zEDuUK01BhR0H9IvmTVQfCX+0k+uIU5t32apSDeKoQPBmUnp7IxbyvgDcYyt9VYA41f3Jtju2EluJweppQu30DGKv+idLB6HooegCvuyI5kBUHCRslAQbkx2yJxCdt0QjiaKQrw8HQWIWnPY4OpY0qlmQ82Nz83IZY7Qtx7C+AoEAtBHNFjtqhQeHeykpyieIkz8LcuMyvdZ5Web1gZOcyd3wLciaCSRwJx2BdmYeHEu3Dd4w50Cg7dzNmqcQV+6+ezzHCxBYkUzF3XrCwta1euUEI4DUsKuQsdxHB3L7VKQQ6JSaOwEBhYhP6rWCGCCH++fMnYA4V4iDeBvg7oNedZgTNewPtp6A+NrUIZwNpiZxqirW8yA5e3pTbsQQrkZwtaYuaDGIAAsWjhk8ymT4ixjNg9wgCvJ9cEklgvhKEJBIQPoMN0OuqLzUE6GgFyCN+OzjkCDivQRx+zkLeOWCitqH6BUIFJHJQA8B+w2IGyhLyDIQcynIaRakBZAxb8LPQ7Nb7BGYrDfWk7FK++j96vegEaRWivW2WKx2MBt/sbyJTVkNArI05fm1UqSvGkQvoMrMcEDooCo+FZIRpLwoYgNavXfvQVCmaKr2Awu6txGH6tIqlHhQ60WoaLzabssFIHg6hxF5rngM+IjgIqxJClaA/MHTjy85j/mkRgtAdBBBetL5EQbxgsE2zMbhxGXVJLXBVW521OUxgG97qakDnO56lxzyBgRBg+/bxI0x9lzogsOZBXjLsIVSH1KZeOkwwegSCFcJQTk0zfnKx5N9hxaBoFP7aCaa9igapOfxwMB4mY45vF7By6M0oXV71ag4yvcdCPe6PMjjUce6nEDEPzFnkk8DbuYrjvcg6DSdbPuQ7OYxfa78SUkhisEtgOzk85FuO8Uw62lJCiSQVH4NxfJISQ+1ADYY88Qv+JTHghZg5BzkDjOcZIl22ImnAcZgKxHET+i15FGfdC8xAvqYbbDK6NJIo2vEyuGl7K9iZCdBYAxqyYkmTXEq21UmsOzNqpPef7FHTXy71mN6KRau+cfMCsPQilxMrzrsV57//LOjPRyX0vQlJM25lrYru6Y6EKuabiz3/wD8XkeE+HHPxgAAAABJRU5ErkJggg==" alt="Flex.io">
    </div>
    <legend class="f3 tc ph0 mh0 mv3 black-80 w-100">Sign in</legend>
    <div>
      <div v-if="error_msg" class="mv3 ph3 pv2a lh-title fw6 br1 white bg-dark-red">
        {{error_msg}}
      </div>
      <div class="mv3">
        <input type="text" v-model="username" :class="input_cls" class="bg-black-10" placeholder="Email or username" disabled v-if="username_provided">
        <input type="text" v-model="username" :class="input_cls" placeholder="Email or username" v-else v-focus>
      </div>
      <div class="mv3">
        <input v-model="password" @keyup.enter="trySignIn" :class="input_cls" placeholder="Password" type="password" autocomplete=off spellcheck="false" v-if="username_provided" v-focus>
        <input v-model="password" @keyup.enter="trySignIn" :class="input_cls" placeholder="Password" type="password" autocomplete=off spellcheck="false" v-else>
        <router-link to="/forgotpassword" class="f8 fw6 black-60 link underline-hover dib">Forgot your password?</router-link>
      </div>
      <div class="mv3">
        <btn btn-lg btn-primary :disabled="is_submitting" @click="trySignIn" class="b ttu w-100">
          <span v-if="is_submitting">Signing in...</span>
          <span v-else>Sign in</span>
        </btn>
      </div>
    </div>
    <div class="tc f5 fw6 mt3">
      New to Flex.io?
      <router-link :to="signup_link" class="link dib blue underline-hover db">Sign up</router-link>
    </div>
  </form>
</template>

<script>
  import api from '../api'
  import { ROUTE_SIGNUP } from '../constants/route'
  import Btn from './Btn.vue'
  import Redirect from './mixins/redirect'

  export default {
    mixins: [Redirect],
    components: {
      Btn
    },
    beforeRouteEnter(to, from, next) {
      next(vm => {
        // access to component instance via `vm`
        vm.verify_code = _.get(to, 'query.verify_code', '')
        vm.username = _.get(to, 'query.email', '')

        // if an email address has been provided to us, this indicates
        // the user has been invited to Flex.io; disable the email input
        // and show it at the top of the form -- all the user needs
        // to do is to enter their first/last name, username and
        // a password to create their account
        if (vm.username.length > 0)
          vm.username_provided = true
      })
    },
    data() {
      return {
        username: '',
        password: '',
        username_provided: false,
        is_submitting: false,
        error_msg: '',
        verify_code: '',
        input_cls: 'input-reset ba b--black-10 focus-b--transparent focus-outline focus-o--blue lh-title ph3 pv2a w-100'
      }
    },
    computed: {
      signup_link() {
        return {
          name: ROUTE_SIGNUP,
          query: this.$route.query
        }
      }
    },
    methods: {
      getAttrs() {
        // assemble non-empty values for submitting to the backend
        return _
          .chain(this.$data)
          .pick(['username', 'password', 'verify_code'])
          .omitBy(_.isEmpty)
          .value()
      },
      trySignIn() {
        var attrs = this.getAttrs()

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
            this.error_msg = _.get(response, 'data.error.message', '')
          }
        })
      }
    }
  }
</script>
