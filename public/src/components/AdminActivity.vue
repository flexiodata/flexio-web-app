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
    metaInfo: {
      title: '[Admin] Activity Overview'
    },
    components: {
      AppActivity
    },
    data() {
      var query = this.$route.query
      var yesterday = moment().subtract(1, 'days')
      var today = moment()

      return {
        processes: [],
        owned_by: _.get(query, 'owned_by', null),
        created_min: _.get(query, 'created_min', yesterday.format('YYYYMMDD')),
        created_max: _.get(query, 'created_max', today.format('YYYYMMDD'))
      }
    },
    mounted() {
      var attrs = {
        owned_by: this.owned_by,
        created_min: this.created_min,
        created_max: this.created_max
      }

      this.$store.dispatch('v2_action_fetchAdminProcesses', { attrs }).then(response => {
        var processes = response.data
        this.processes = processes
      }).catch(error => {
        // TODO: add error handling
      })
    }
  }
</script>
