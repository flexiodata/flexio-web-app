<template>
  <form @submit.prevent>
    <div class="tc mb3">
      <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGEAAAAcCAYAAABxutG8AAAACXBIWXMAAAWJAAAFiQFtaJ36AAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAARoSURBVHja7FpLbtswEFUMo13aN7BuYK+7sXqCKCeIcoI4QPeW9104J7B8gsoniLzpNsoJqtzAXrYo4JLFY8swMySlyAochMDAkEyKnzfz5iMFAd1GQlZCfgg5aHIn5FrIIHhvrbUz4t6lkMwxbickEbLB9VhILiTEf5GQh1M9lK/fp/IMlkKGQkq5ny+ftvtjzdczrqceAARYXA7AAowJjf9OFYAB9jPErQkACboCYVZzfKEtVG/hCbPDhLgXdglCTPQpcT82rERqx+MbpOgSlEop29FBGDF9InC/lCtcV0LSt+gkwf2RBkZ2bDoy/cGBiIZ82oGQ9+bZ+q849xgaN9E4t4AGyt89ETZT3Lz1mGdIRHetRm/CoY+wnxC/itokaxTCwh58QtQpwX3y+rOnJfiEv2qeVFsoFwJLClg41qf82MYCQEGAYBsjD/RA0NQZ03eM/cSOM/pL4eI5a5djPna7xqFEHiGw3Ni9lhhuGT+UWZLHjAAgswFQU/vnWuDiatJCMjHmDmHwExAGOJyEGTg3ZNpwzfMGDm4C0NSiF9i0CVjGzDchIp9ZSwCsGgYnUgELHYg+EqvIgp45UerBwxwFUbSTw1SH0KiQAEKOvdGopDQ0PIYi3Wo0RM2XEL6mqQUkFiottbUPGeXKFdWfNYhkUoOrfXzCD+JwlRmbucbKYpWPltLKDptThxASieit5yGzPgEOuGIOf2Zyvuh/DgaggopE9u8xD2yznRMLqGB9VLJ3xZQ9dBpZE30ULaXEfIUvAJ5KSAEQUU5X3NtAOUruWT1oY8502mEDutQFLWYmt9HCjOFSk1oqos+M2EPcolJRVhrbQlAkgKR1S0vpIV6+YDZegrd0WTdwRGZzFfgeCaUwnezewsut+wFQCxWUVOKQnT4SIFEBxKSLEDVkLOzgkAnj4M1EzRahLNsKR1+gUK4aVNR1nnCMtrBQZBf1rV2NvuQ63wII84AvNaensIHXqh01PZyKKEukDgdftEhJFUNRixrJ5zO/2wUIVJmiaJDwmW3gyccZLKUN50xFkJHMHYTj9Xm3QgY/XdBR/gJLuHQ43ZCYi8ofWnndiginYoB2RVbXDG3mrwVChMzYpuUrbG7FJIAJ4SATyI6Yb35EKpXW8M0szBkAUHWzTOYQXZWy58ziCyyuAF2MtYQrNPrF6DMiakcqKdxoIOUMJ/9LqvofPupfVSx///p54ypb4P97ht/Vm7gi+P/VSWzpG0oQunLMS2YxUeAua+v9NgFdnjbzgQ3uzQirlGvYCwDGBo3MxL1KAOFT3kgC+j3FEHP6VGoT9RlNVyGq/t62aalgA4uKiIglZWijIhLHpXZgPiUWzjdENXOEJ7SJmlLneYICos47hRKauw74cjhXluDKGgnoqrL4r8rl1wBE2CBjflbo6xsIFR4hmW86zgFxo1FFRFCUWkdmUEzC+CxbqLvFPKaGJ4J2LgT9JIZPuNUsQn1RWHA1KtDJBcrb3H4qtR+uxvRHgAEAAbWMsfVFqtEAAAAASUVORK5CYII=" alt="Flex.io">
    </div>
    <h1 class="b f3 tc ph0 mh0 mt0 mb3 black-80 w-100">Forgot Password</h1>
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
        <p>You may need to check your spam folder or unblock <span class="fw6 nowrap">no-reply@flex.io</span>.</p>
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
        <button
          type="button"
          :class="button_cls"
          :disabled="is_submitting"
          @click="sendReset"
        >
          <span v-if="is_submitting">Sending reset instructions...</span>
          <span v-else>Send reset instructions</span>
        </button>
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

  export default {
    data() {
      return {
        email: '',
        is_submitting: false,
        is_sent: false,
        error_msg: '',
        input_cls: 'input-reset ba b--black-10 br2 focus-b--blue lh-title ph3 pv2a w-100',
        button_cls: 'border-box no-select ttu b w-100 ph4 pv2a lh-title white bg-blue br2 darken-10'
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
