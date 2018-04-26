<template>
  <div>
    <div class="tc mb3">
      <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGEAAAAcCAYAAABxutG8AAAACXBIWXMAAAWJAAAFiQFtaJ36AAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAARoSURBVHja7FpLbtswEFUMo13aN7BuYK+7sXqCKCeIcoI4QPeW9104J7B8gsoniLzpNsoJqtzAXrYo4JLFY8swMySlyAochMDAkEyKnzfz5iMFAd1GQlZCfgg5aHIn5FrIIHhvrbUz4t6lkMwxbickEbLB9VhILiTEf5GQh1M9lK/fp/IMlkKGQkq5ny+ftvtjzdczrqceAARYXA7AAowJjf9OFYAB9jPErQkACboCYVZzfKEtVG/hCbPDhLgXdglCTPQpcT82rERqx+MbpOgSlEop29FBGDF9InC/lCtcV0LSt+gkwf2RBkZ2bDoy/cGBiIZ82oGQ9+bZ+q849xgaN9E4t4AGyt89ETZT3Lz1mGdIRHetRm/CoY+wnxC/itokaxTCwh58QtQpwX3y+rOnJfiEv2qeVFsoFwJLClg41qf82MYCQEGAYBsjD/RA0NQZ03eM/cSOM/pL4eI5a5djPna7xqFEHiGw3Ni9lhhuGT+UWZLHjAAgswFQU/vnWuDiatJCMjHmDmHwExAGOJyEGTg3ZNpwzfMGDm4C0NSiF9i0CVjGzDchIp9ZSwCsGgYnUgELHYg+EqvIgp45UerBwxwFUbSTw1SH0KiQAEKOvdGopDQ0PIYi3Wo0RM2XEL6mqQUkFiottbUPGeXKFdWfNYhkUoOrfXzCD+JwlRmbucbKYpWPltLKDptThxASieit5yGzPgEOuGIOf2Zyvuh/DgaggopE9u8xD2yznRMLqGB9VLJ3xZQ9dBpZE30ULaXEfIUvAJ5KSAEQUU5X3NtAOUruWT1oY8502mEDutQFLWYmt9HCjOFSk1oqos+M2EPcolJRVhrbQlAkgKR1S0vpIV6+YDZegrd0WTdwRGZzFfgeCaUwnezewsut+wFQCxWUVOKQnT4SIFEBxKSLEDVkLOzgkAnj4M1EzRahLNsKR1+gUK4aVNR1nnCMtrBQZBf1rV2NvuQ63wII84AvNaensIHXqh01PZyKKEukDgdftEhJFUNRixrJ5zO/2wUIVJmiaJDwmW3gyccZLKUN50xFkJHMHYTj9Xm3QgY/XdBR/gJLuHQ43ZCYi8ofWnndiginYoB2RVbXDG3mrwVChMzYpuUrbG7FJIAJ4SATyI6Yb35EKpXW8M0szBkAUHWzTOYQXZWy58ziCyyuAF2MtYQrNPrF6DMiakcqKdxoIOUMJ/9LqvofPupfVSx///p54ypb4P97ht/Vm7gi+P/VSWzpG0oQunLMS2YxUeAua+v9NgFdnjbzgQ3uzQirlGvYCwDGBo3MxL1KAOFT3kgC+j3FEHP6VGoT9RlNVyGq/t62aalgA4uKiIglZWijIhLHpXZgPiUWzjdENXOEJ7SJmlLneYICos47hRKauw74cjhXluDKGgnoqrL4r8rl1wBE2CBjflbo6xsIFR4hmW86zgFxo1FFRFCUWkdmUEzC+CxbqLvFPKaGJ4J2LgT9JIZPuNUsQn1RWHA1KtDJBcrb3H4qtR+uxvRHgAEAAbWMsfVFqtEAAAAASUVORK5CYII=" alt="Flex.io">
    </div>
    <h1 class="f3 tc ph0 mh0 mt0 mb3 black-80 w-100">Thanks for signing up!</h1>
    <div class="mt4">
      <p class="lh-copy">Here is your API key. You can create new keys at any time from the <a href="/app/dashboard" class="blue">dashboard in the web app</a>.</p>
      <div class="flex flex-row mv3">
        <div class="flex-fill pv2 ph3 f3 tc b black code ba b--black-10 mv2">{{api_key}}</div>
      </div>
      <div class="mt3">
        <p class="lh-copy">You can use this key with any of the code snippets on the Flex.io website. Check out the <a href="/docs/quick-start" class="blue">quick start guide</a> to jump right in.</p>
        <p class="lh-copy">We're thrilled to have you on board and can't wait to see what you build with Flex.io!</p>
        <div class="flex flex-row items-center mt4">
          <div class="flex-fill"></div>
          <button type="button" class="border-box no-select ttu ph3 pv2a br1 silver bg-white ba b--black-10 darken-10 no-underline db tc mr2" @click="$emit('close-click')">Close</button>
          <a href="/app/dashboard" class="border-box no-select ttu b ph4 pv2a br1 white bg-blue ba b--blue darken-10 no-underline db tc" target="_blank" rel="noopener noreferrer">Open my dashboard</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import _ from 'lodash'
  import axios from 'axios'
  import Btn from './Btn.vue'

  export default {
    props: {
      eid: {
        type: String,
        required: true
      }
    },
    components: {
      Btn
    },
    data() {
      return {
        error_msg: '',
        label_submitting: 'Fetching API key...',
        is_submitting: false,
        api_key: 'API key'
      }
    },
    mounted() {
      this.fetchApiKey()
    },
    methods: {
      fetchApiKey() {
        this.is_submitting = true

        axios.get('/api/v2/' + this.eid + '/auth/keys').then(response => {
          this.is_submitting = false
          this.api_key = _.get(response, 'data[0].access_code', '')
        }).catch(error => {
          this.is_submitting = false
          this.error_msg = _.get(error, 'response.data.error.message', '')
        })
      }
    }
  }
</script>
