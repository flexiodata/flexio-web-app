<template>
  <div class="flex flex-column items-stretch">
    <h1 v-if="false" class="flex-none f3 f2-l fw6 ph2 pv2 pv3-m pv4-l ma0 truncate white bg-blue tc">Projects</h1>
    <h1 class="flex-none f3 f2-l fw6 ph2 pv2 pv3-m pv4-l ma0 truncate mid-gray tc">Projects</h1>
    <div class="flex-fill flex flex-column overflow-auto relative">
      <project-manager class="flex-fill flex flex-column mw7 center w-100"></project-manager>
    </div>
  </div>
</template>

<script>
  import ProjectManager from './ProjectManager.vue'

  export default {
    components: {
      ProjectManager
    }
  }
</script>
