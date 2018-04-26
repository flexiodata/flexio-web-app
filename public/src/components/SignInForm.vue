<template>
  <form @submit.prevent>
    <div class="tc mb3">
      <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGEAAAAcCAYAAABxutG8AAAACXBIWXMAAAWJAAAFiQFtaJ36AAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAARoSURBVHja7FpLbtswEFUMo13aN7BuYK+7sXqCKCeIcoI4QPeW9104J7B8gsoniLzpNsoJqtzAXrYo4JLFY8swMySlyAochMDAkEyKnzfz5iMFAd1GQlZCfgg5aHIn5FrIIHhvrbUz4t6lkMwxbickEbLB9VhILiTEf5GQh1M9lK/fp/IMlkKGQkq5ny+ftvtjzdczrqceAARYXA7AAowJjf9OFYAB9jPErQkACboCYVZzfKEtVG/hCbPDhLgXdglCTPQpcT82rERqx+MbpOgSlEop29FBGDF9InC/lCtcV0LSt+gkwf2RBkZ2bDoy/cGBiIZ82oGQ9+bZ+q849xgaN9E4t4AGyt89ETZT3Lz1mGdIRHetRm/CoY+wnxC/itokaxTCwh58QtQpwX3y+rOnJfiEv2qeVFsoFwJLClg41qf82MYCQEGAYBsjD/RA0NQZ03eM/cSOM/pL4eI5a5djPna7xqFEHiGw3Ni9lhhuGT+UWZLHjAAgswFQU/vnWuDiatJCMjHmDmHwExAGOJyEGTg3ZNpwzfMGDm4C0NSiF9i0CVjGzDchIp9ZSwCsGgYnUgELHYg+EqvIgp45UerBwxwFUbSTw1SH0KiQAEKOvdGopDQ0PIYi3Wo0RM2XEL6mqQUkFiottbUPGeXKFdWfNYhkUoOrfXzCD+JwlRmbucbKYpWPltLKDptThxASieit5yGzPgEOuGIOf2Zyvuh/DgaggopE9u8xD2yznRMLqGB9VLJ3xZQ9dBpZE30ULaXEfIUvAJ5KSAEQUU5X3NtAOUruWT1oY8502mEDutQFLWYmt9HCjOFSk1oqos+M2EPcolJRVhrbQlAkgKR1S0vpIV6+YDZegrd0WTdwRGZzFfgeCaUwnezewsut+wFQCxWUVOKQnT4SIFEBxKSLEDVkLOzgkAnj4M1EzRahLNsKR1+gUK4aVNR1nnCMtrBQZBf1rV2NvuQ63wII84AvNaensIHXqh01PZyKKEukDgdftEhJFUNRixrJ5zO/2wUIVJmiaJDwmW3gyccZLKUN50xFkJHMHYTj9Xm3QgY/XdBR/gJLuHQ43ZCYi8ofWnndiginYoB2RVbXDG3mrwVChMzYpuUrbG7FJIAJ4SATyI6Yb35EKpXW8M0szBkAUHWzTOYQXZWy58ziCyyuAF2MtYQrNPrF6DMiakcqKdxoIOUMJ/9LqvofPupfVSx///p54ypb4P97ht/Vm7gi+P/VSWzpG0oQunLMS2YxUeAua+v9NgFdnjbzgQ3uzQirlGvYCwDGBo3MxL1KAOFT3kgC+j3FEHP6VGoT9RlNVyGq/t62aalgA4uKiIglZWijIhLHpXZgPiUWzjdENXOEJ7SJmlLneYICos47hRKauw74cjhXluDKGgnoqrL4r8rl1wBE2CBjflbo6xsIFR4hmW86zgFxo1FFRFCUWkdmUEzC+CxbqLvFPKaGJ4J2LgT9JIZPuNUsQn1RWHA1KtDJBcrb3H4qtR+uxvRHgAEAAbWMsfVFqtEAAAAASUVORK5CYII=" alt="Flex.io">
    </div>
    <h1 class="b f3 tc ph0 mh0 mt0 mb3 black-80 w-100">Sign in</h1>
    <div>
      <div v-if="error_msg" class="mv3 ph3 pv2a lh-title fw6 br1 white bg-dark-red">
        {{error_msg}}
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
        <btn btn-lg btn-primary :disabled="is_submitting" @click="trySignIn" class="ttu b w-100">
          <span v-if="is_submitting">Signing in...</span>
          <span v-else>Sign in</span>
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
        username: '',
        password: '',
        is_submitting: false,
        error_msg: '',
        verify_code: '',
        input_cls: 'input-reset ba b--black-10 focus-b--transparent focus-outline focus-o--blue lh-title ph3 pv2a w-100'
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
          this.is_submitting = false
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
