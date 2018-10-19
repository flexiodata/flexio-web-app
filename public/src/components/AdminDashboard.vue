<template>
  <div class="bg-white">
    <!-- list -->
    <AppActivity class="h-100" :items="processes" :show-user="true" />
  </div>
</template>

<script>
  import moment from 'moment'
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
      var last_week = moment().subtract(1, 'days')
      var created_min = last_week.format('YYYYMMDD')

      this.$store.dispatch('fetchAdminProcesses', { created_min }).then(response => {
        this.processes = response.body
      })
    }
  }
</script>
