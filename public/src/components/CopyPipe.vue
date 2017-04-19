<template>
  <div class="ma4">
    Pipe slug: {{slug}}
  </div>
</template>

<script>
  export default {
    computed: {
      slug() {
        return this.$route.params.slug
      }
    }
  }
</script>
