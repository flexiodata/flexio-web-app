<template>
  <main class="pa3 ph3-m pa5-ns black-60 overflow-auto">
    <div class="measure-wide center">
      <forgot-password-form />
    </div>
  </main>
</template>

<script>
  import ForgotPasswordForm from './ForgotPasswordForm.vue'

  export default {
    components: {
      ForgotPasswordForm
    }
  }
</script>
