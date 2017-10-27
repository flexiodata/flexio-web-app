<template>
  <flexio-modal
    title="Create Your Free Account"
    container-cls="relative"
    container-style="width: 32rem"
    :show-header="false"
    :show-footer="false"
    @cancel="cancelClick"
    @submit="submitClick"
  >
    <div class="pointer f3 lh-solid b child black-30 hover-black-60 mt2 mr3 absolute top-0 right-0" @click="cancelClick">&times;</div>
    <div class="pv3 ph2">
      <sign-up-form
        @sign-in-click="onSignInClick"
        v-if="view === 'signup'"
      />
      <sign-in-form
        @sign-up-click="onSignUpClick"
        @forgot-password-click="onForgotPasswordClick"
        v-else-if="view === 'signin'"
      />
      <forgot-password-form
        @sign-up-click="onSignUpClick"
        @sign-in-click="onSignInClick"
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
    data() {
      return {
        view: this.initialView
      }
    },
    methods: {
      cancelClick() {
        this.$emit('cancel', this)
      },
      submitClick() {
        this.$emit('submit', this)
      },
      onSignUpClick() {
        this.view = 'signup'
      },
      onSignInClick() {
        this.view = 'signin'
      },
      onForgotPasswordClick() {
        this.view = 'forgotpassword'
      }
    }
  }
</script>
