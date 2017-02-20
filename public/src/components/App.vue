<template>
  <div id="app" class="flex flex-column fixed absolute--fill overflow-hidden">
    <app-navbar class="flex-none"></app-navbar>
    <router-view class="flex-fill"></router-view>
  </div>
</template>

<script>
  import AppNavbar from './AppNavbar.vue'

  export default {
    name: 'app',
    components: {
      AppNavbar
    }
  }
</script>
