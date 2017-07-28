<template>
  <div class="dib" v-if="store_stats.length == 0">
    <div class="flex flex-row justify-center items-center min-h3 pa3">
      <spinner size="medium"></spinner>
      <span class="ml2 f5">Loading...</span>
    </div>
  </div>
  <div class="pa3" v-else>
    <h2 class="ma0">Task usage</h2>
    <div class="mt2 mb3 pv2 bt b--black-10">
      <table class="w-100">
        <thead>
          <tr>
            <th class="pa2 tl">Task Type</th>
            <th class="pa2 tr">Avg. Time</th>
            <th class="pa2 tr w-10">Failed</th>
            <th class="pa2 tr w-10">Completed</th>
            <th class="pa2 tr w-10">Total</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(item, index) in store_stats"
            :class="index % 2 ? 'bg-near-white' : ''"
            item="item"
            index="index"
          >
            <td class="pa2 tl">{{item.task_type}}</td>
            <td class="pa2 tr">{{ getAvgTime(item.average_time) }}</td>
            <td class="pa2 tr w-10 dark-red">{{item.failed}}</td>
            <td class="pa2 tr w-10 dark-green">{{item.completed}}</td>
            <td class="pa2 tr w-10">{{item.total_count}}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
  import Spinner from 'vue-simple-spinner'

  export default {
    components: {
      Spinner
    },
    computed: {
      store_stats() {
        return _.get(this.$store, 'state.statistics.tasks', [])
      }
    },
    mounted() {
      this.$store.dispatch('fetchStatistics', { type: 'tasks' })
    },
    methods: {
      getAvgTime(v) {
        return _.isString(v) ? parseFloat(v).toFixed(4) : '--'
      }
    }
  }
</script>
