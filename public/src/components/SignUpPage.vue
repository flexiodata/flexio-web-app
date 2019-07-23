<template>
  <main class="pa3 ph3-m pa5-ns bg-nearer-white black-60 overflow-auto">
    <div class="measure center">
      <div
        class="br2 bg-white pa3 pa4-ns css-white-box"
        v-if="is_verify"
      >
        <div class="tc mb3">
          <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGEAAAAcCAYAAABxutG8AAAACXBIWXMAAAWJAAAFiQFtaJ36AAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAARoSURBVHja7FpLbtswEFUMo13aN7BuYK+7sXqCKCeIcoI4QPeW9104J7B8gsoniLzpNsoJqtzAXrYo4JLFY8swMySlyAochMDAkEyKnzfz5iMFAd1GQlZCfgg5aHIn5FrIIHhvrbUz4t6lkMwxbickEbLB9VhILiTEf5GQh1M9lK/fp/IMlkKGQkq5ny+ftvtjzdczrqceAARYXA7AAowJjf9OFYAB9jPErQkACboCYVZzfKEtVG/hCbPDhLgXdglCTPQpcT82rERqx+MbpOgSlEop29FBGDF9InC/lCtcV0LSt+gkwf2RBkZ2bDoy/cGBiIZ82oGQ9+bZ+q849xgaN9E4t4AGyt89ETZT3Lz1mGdIRHetRm/CoY+wnxC/itokaxTCwh58QtQpwX3y+rOnJfiEv2qeVFsoFwJLClg41qf82MYCQEGAYBsjD/RA0NQZ03eM/cSOM/pL4eI5a5djPna7xqFEHiGw3Ni9lhhuGT+UWZLHjAAgswFQU/vnWuDiatJCMjHmDmHwExAGOJyEGTg3ZNpwzfMGDm4C0NSiF9i0CVjGzDchIp9ZSwCsGgYnUgELHYg+EqvIgp45UerBwxwFUbSTw1SH0KiQAEKOvdGopDQ0PIYi3Wo0RM2XEL6mqQUkFiottbUPGeXKFdWfNYhkUoOrfXzCD+JwlRmbucbKYpWPltLKDptThxASieit5yGzPgEOuGIOf2Zyvuh/DgaggopE9u8xD2yznRMLqGB9VLJ3xZQ9dBpZE30ULaXEfIUvAJ5KSAEQUU5X3NtAOUruWT1oY8502mEDutQFLWYmt9HCjOFSk1oqos+M2EPcolJRVhrbQlAkgKR1S0vpIV6+YDZegrd0WTdwRGZzFfgeCaUwnezewsut+wFQCxWUVOKQnT4SIFEBxKSLEDVkLOzgkAnj4M1EzRahLNsKR1+gUK4aVNR1nnCMtrBQZBf1rV2NvuQ63wII84AvNaensIHXqh01PZyKKEukDgdftEhJFUNRixrJ5zO/2wUIVJmiaJDwmW3gyccZLKUN50xFkJHMHYTj9Xm3QgY/XdBR/gJLuHQ43ZCYi8ofWnndiginYoB2RVbXDG3mrwVChMzYpuUrbG7FJIAJ4SATyI6Yb35EKpXW8M0szBkAUHWzTOYQXZWy58ziCyyuAF2MtYQrNPrF6DMiakcqKdxoIOUMJ/9LqvofPupfVSx///p54ypb4P97ht/Vm7gi+P/VSWzpG0oQunLMS2YxUeAua+v9NgFdnjbzgQ3uzQirlGvYCwDGBo3MxL1KAOFT3kgC+j3FEHP6VGoT9RlNVyGq/t62aalgA4uKiIglZWijIhLHpXZgPiUWzjdENXOEJ7SJmlLneYICos47hRKauw74cjhXluDKGgnoqrL4r8rl1wBE2CBjflbo6xsIFR4hmW86zgFxo1FFRFCUWkdmUEzC+CxbqLvFPKaGJ4J2LgT9JIZPuNUsQn1RWHA1KtDJBcrb3H4qtR+uxvRHgAEAAbWMsfVFqtEAAAAASUVORK5CYII=" alt="Flex.io">
        </div>
        <h1 class="b f3 tc ph0 mh0 mt0 mb3 black-80 w-100">Thanks for signing up!</h1>
        <p>To finish signing up, you just need to confirm that we got your email address right.</p>
        <p>We've sent a verification email to the address you provided. Clicking the confirmation link in that email lets us know the email address is both valid and yours.</p>
        <button
          type="button"
          class="border-box no-select ttu fw6 w-100 ph4 pv2a lh-title white br2 darken-10"
          :class="is_sent ? 'bg-dark-green o-40 no-pointer-events' : 'bg-blue'"
          @click="resendVerification"
        >
          {{is_sent ? 'Verification email sent!' : 'Resend verification email'}}
        </button>
      </div>
      <SignUpForm
        class="br2 bg-white pa3 pa4-ns css-white-box"
        :signin-on-signup="false"
        @sign-in-click="onSignInClick"
        @signed-up="onSignedUp"
        @signed-in="onSignedIn"
        v-else
      />
    </div>
  </main>
</template>

<script>
  import axios from 'axios'
  import { ROUTE_SIGNIN_PAGE } from '../constants/route'
  import SignUpForm from '@comp/SignUpForm'
  import MixinRedirect from '@comp/mixins/redirect'

  export default {
    metaInfo: {
      title: 'Sign Up for Flex.io Serverless Functions Today'
    },
    mixins: [MixinRedirect],
    components: {
      SignUpForm
    },
    data() {
      return {
        is_sent: false,
        user: {}
      }
    },
    computed: {
      is_verify() {
        return _.get(this.$route, 'params.action', '') === 'verify'
      },
      signin_route() {
        return {
          name: ROUTE_SIGNIN_PAGE,
          query: this.$route.query
        }
      }
    },
    mounted() {
      // don't allow signup verification page to be reloaded
      if (this.is_verify && _.get(this.user, 'email', '').length == 0) {
        var new_route = _.assign({}, this.$route, { params: { action: undefined } })
        this.$router.replace(new_route)
      }

      this.$store.track('Visited Sign Up Page')
    },
    methods: {
      onSignInClick() {
        this.$router.push(this.signin_route)
      },
      onSignedUp(user) {
        this.user = user
        var new_route = _.assign({}, this.$route, { params: { action: 'verify' } })
        this.$router.replace(new_route)
      },
      onSignedIn() {
        var redirect = _.get(this.$route, 'query.redirect', '')

        if (redirect.length > 0) {
          this.$_Redirect_redirect()
        } else {
          this.$_Redirect_redirect()
        }
      },
      resendVerification() {
        axios.post('/api/v2/requestverification', { email: this.user.email }).then(response => {
          this.is_sent = true
          setTimeout(() => { this.is_sent = false }, 6000)
        })
      }
    }
  }
</script>
