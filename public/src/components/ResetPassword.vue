<template>
  <main class="pa3 ph3-m pa5-ns black-60 overflow-auto">
    <div class="measure-wide center">
      <reset-password-form />
    </div>
  </main>
</template>

<script>
  import ResetPasswordForm from './ResetPasswordForm.vue'

  export default {
    components: {
      ResetPasswordForm
    }
  }
</script>
