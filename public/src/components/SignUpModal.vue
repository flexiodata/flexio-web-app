<template>
  <flexio-modal
    title="Create Your Free Account"
    container-cls="relative"
    container-style="width: 32rem"
    :show-header="false"
    :show-footer="false"
    @cancel="$emit('cancel')"
    @submit="$emit('submit')"
  >
    <div class="pointer f3 lh-solid b child black-30 hover-black-60 mt2 mr3 absolute top-0 right-0" @click="$emit('cancel')">&times;</div>
    <div class="pv3 ph2">
      <sign-up-form
        @sign-in-click="view = 'signin'"
        @signed-up="onSignedUp"
        @signed-in="onSignedUpAndIn"
        v-if="view === 'signup'"
      />
      <sign-in-form
        @sign-up-click="view = 'signup'"
        @forgot-password-click="view = 'forgotpassword'"
        @signed-in="onSignedIn"
        v-else-if="view === 'signin'"
      />
      <forgot-password-form
        @sign-up-click="view = 'signup'"
        @sign-in-click="view = 'signin'"
        @requested-password="$emit('requested-password')"
        v-else-if="view === 'forgotpassword'"
      />
    </div>
  </flexio-modal>
</template>

<script>
  import FlexioModal from './FlexioModal.vue'
  import SignUpForm from './SignUpForm.vue'
  import SignInForm from './SignInForm.vue'
  import ForgotPasswordForm from './ForgotPasswordForm.vue'

  export default {
    props: {
      'initial-view': {
        type: String,
        default: 'signup'
      }
    },
    components: {
      FlexioModal,
      SignUpForm,
      SignInForm,
      ForgotPasswordForm
    },
    watch: {
      view: {
        handler: 'trackPage',
        immediate: true
      }
    },
    data() {
      return {
        view: this.initialView // 'signin', 'signup' or 'forgotpassword'
      }
    },
    methods: {
      trackPage() {
        if (this.view == 'signup') {
          if (window.analytics) {
            window.analytics.track('Visited Sign Up Page', { label: window.location.pathname })
          }
        } else if (this.view == 'signin') {
          if (window.analytics) {
            window.analytics.track('Visited Sign In Page', { label: window.location.pathname })
          }
        } else if (this.view == 'forgotpassword') {
          if (window.analytics) {
            window.analytics.track('Visited Forgot Password Page', { label: window.location.pathname })
          }
        }
      },
      onSignedIn() {
        this.$emit('signed-in')
        setTimeout(function() { window.location = '/app' }, 500)
      },
      onSignedUp() {
        // do nothing
      },
      onSignedUpAndIn() {
        this.$emit('signed-up')
        setTimeout(function() { window.location = '/app/onboard' }, 500)
      }
    }
  }
</script>
