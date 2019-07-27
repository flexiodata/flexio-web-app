<template>
  <main class="pa3 ph3-m pa5-ns bg-nearer-white overflow-auto">
    <div class="measure center mt4">
      <ResetPasswordForm class="br2 bg-white pa3 pa4-ns css-white-box" />
    </div>
  </main>
</template>

<script>
  import ResetPasswordForm from '@/components/ResetPasswordForm'

  export default {
    metaInfo: {
      title: 'Reset Password'
    },
    components: {
      ResetPasswordForm
    },
    mounted() {
      this.$store.track('Visited Reset Password Page')
    }
  }
</script>
