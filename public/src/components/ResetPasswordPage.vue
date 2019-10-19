<template>
  <main class="pv4 pv5-ns ph3 ph5-ns bg-nearer-white overflow-auto">
    <div class="measure center mt4">
      <ResetPasswordForm class="br2 bg-white pv4 ph3 ph4-ns css-white-box" />
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
    }
  }
</script>
