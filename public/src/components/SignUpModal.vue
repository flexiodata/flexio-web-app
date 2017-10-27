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
      <sign-up-modal-success />
    </div>
  </flexio-modal>
</template>

<script>
  import FlexioModal from './FlexioModal.vue'
  import SignUpForm from './SignUpForm.vue'
  import SignInForm from './SignInForm.vue'
  import ForgotPasswordForm from './ForgotPasswordForm.vue'
  import SignUpModalSuccess from './SignUpModalSuccess.vue'

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
      ForgotPasswordForm,
      SignUpModalSuccess
    },
    data() {
      return {
        view: this.initialView
      }
    },
    methods: {
      onSignedUp() {
        this.$emit('signed-up-signed-in')
        this.view = 'sign-up-success'
      }
    }
  }
</script>
