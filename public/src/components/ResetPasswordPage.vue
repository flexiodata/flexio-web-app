<template>
  <main class="pa3 ph3-m pa5-ns bg-nearer-white black-60 overflow-auto">
    <div class="measure center">
      <reset-password-form class="br2 bg-white pa3 pa4-ns css-dashboard-box" />
    </div>
  </main>
</template>

<script>
  import ResetPasswordForm from './ResetPasswordForm.vue'

  export default {
    components: {
      ResetPasswordForm
    },
    mounted() {
      this.$store.track('Visited Reset Password Page')
    }
  }
</script>
