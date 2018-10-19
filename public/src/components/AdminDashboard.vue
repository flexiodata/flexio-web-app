<template>
  <div class="bg-white">
    <!-- list -->
    <AppActivity class="h-100" :items="processes" />
  </div>
</template>

<script>
  import api from '../api'
  import AppActivity from './AppActivity.vue'

  export default {
    components: {
      AppActivity
    },
    data() {
      return {
        processes: []
      }
    },
    mounted() {
      api.fetchAdminProcesses({ attrs: { created_min: '20181018' } }).then(response => {
        this.processes = response.body
      })
    }
  }
</script>
