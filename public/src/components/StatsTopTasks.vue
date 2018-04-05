<template>
  <div v-if="is_fetching">
    <div class="flex flex-row justify-center items-center min-h4 pa3">
      <spinner size="medium"></spinner>
      <span class="ml2 f5">Loading...</span>
    </div>
  </div>
  <div class="pa3" v-else>
    <div class="f3 ma0 pb2 mb3" v-if="title.length > 0">{{title}}</div>
    <div>
      <table class="w-100">
        <thead>
          <tr>
            <th class="fw6 pa2 tl">Task Type</th>
            <th class="fw6 pa2 tr">Avg. Time</th>
            <th class="fw6 pa2 tr w-10">Failed</th>
            <th class="fw6 pa2 tr w-10">Completed</th>
            <th class="fw6 pa2 tr w-10">Total</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(item, index) in store_stats"
            :class="index % 2 ? 'bg-nearer-white' : ''"
            item="item"
            index="index"
          >
            <td class="bt b--light-gray pa2 tl">{{item.task_op}}</td>
            <td class="bt b--light-gray pa2 tr">{{ getAvgTime(item.average_time) }}</td>
            <td class="bt b--light-gray pa2 tr w-10 dark-red">{{item.failed}}</td>
            <td class="bt b--light-gray pa2 tr w-10 dark-green">{{item.completed}}</td>
            <td class="bt b--light-gray pa2 tr w-10">{{item.total_count}}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
  import Spinner from 'vue-simple-spinner'

  export default {
    props: {
      'title': {
        type: String,
        default: 'Task Usage'
      }
    },
    components: {
      Spinner
    },
    computed: {
      is_fetching() {
        return _.get(this.$store, 'state.statistics_fetching.tasks', false)
      },
      is_fetched() {
        return _.get(this.$store, 'state.statistics_fetched.tasks', false)
      },
      store_stats() {
        return _.get(this.$store, 'state.statistics.tasks', [])
      }
    },
    created() {
      this.tryFetchStats()
    },
    methods: {
      tryFetchStats() {
        if (!this.is_fetched)
          this.$store.dispatch('fetchAdminInfo', { type: 'tasks' })
      },
      getAvgTime(v) {
        return _.isString(v) ? parseFloat(v).toFixed(4) : '--'
      }
    }
  }
</script>
