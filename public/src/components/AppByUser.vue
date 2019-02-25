<template>
  <div class="flex flex-column flex-row-l items-stretch">
    <router-view class="flex-fill relative"></router-view>
  </div>
</template>

<script>
  export default {
    mounted() {
      console.log(this.$route)
    }
  }
</script>
