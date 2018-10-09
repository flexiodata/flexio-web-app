<template>
  <!-- fetching -->
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100">
      <Spinner size="large" message="Loading activity..." />
    </div>
  </div>

  <!-- fetched -->
  <div class="flex flex-column overflow-y-auto" v-else-if="is_fetched">
    <!-- control bar -->
    <div class="center w-100 pa3 pa4-l pb3-l bb bb-0-l b--black-10" style="max-width: 1152px">
      <div class="flex flex-row">
        <div class="flex-fill flex flex-row items-center">
          <div class="f2 dn db-ns mr3">Activity</div>
        </div>
      </div>
    </div>

    <!-- list -->
    <ProcessList
      class="center w-100 pl4-l pr4-l pb4-l"
      style="max-width: 1152px"
    />
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import ProcessList from './ProcessList.vue'

  export default {
    components: {
      Spinner,
      ProcessList
    },
    data() {
      return {
        filter: ''
      }
    },
    computed: {
      // mix this into the outer object with the object spread operator
      ...mapState({
        'is_fetching': 'processes_fetching',
        'is_fetched': 'processes_fetched'
      })
    },
    mounted() {
      this.$store.track('Visited Activity Page')
      this.$store.dispatch('fetchProcesses')
    },
    methods: {

    }
  }
</script>
