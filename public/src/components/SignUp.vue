<template>
  <main class="pa3 ph3-m pa5-ns black-60 overflow-auto">
    <div class="measure-wide center">
      <sign-up-form class="mh5-ns" />
    </div>
  </main>
</template>

<script>
  import SignUpForm from './SignUpForm.vue'

  export default {
    components: {
      SignUpForm
    }
  }
</script>
