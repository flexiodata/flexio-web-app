<template>
  <main class="pa3 ph3-m pa5-ns black-60 overflow-auto">
    <div class="measure-wide center">
      <sign-in-form />
    </div>
  </main>
</template>

<script>
  import SignInForm from './SignInForm.vue'

  export default {
    components: {
      SignInForm
    }
  }
</script>
