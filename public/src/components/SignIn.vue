<template>
  <main class="pa3 ph3-m pa5-ns black-60 overflow-auto">
    <div class="measure-wide center">
      <sign-in-form class="mh5-ns" />
    </div>
  </main>
</template>

<script>
  import SignInForm from './SignInForm.vue'

  export default {
    components: {
      SignInForm
    },
    mounted() {
      analytics.track('Visited Sign In Page')
    }
  }
</script>
