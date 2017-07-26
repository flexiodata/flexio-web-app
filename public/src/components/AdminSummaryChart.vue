<template>
  <div class="f6 pa3">
    <line-chart
      :height="120"
      :config="{}"
      :options="{
        maintainAspectRatio: false,
        animation: {
          duration: 0
        }
      }"
    ></line-chart>
  </div>
</template>

<script>
  import LineChart from './LineChart.vue'

  export default {
    props: {
      'type': {
        type: String,
        required: true
      },
      'count': {
        type: Number,
        default: 5
      }
    },
    components: {
      LineChart
    },
    computed: {
      stats() {
        return _.get(this.$store, 'state.statistics.'+this.type, {})
      },
      top_stats() {
        var t = _.take(_.sortBy(this.stats, ['total_count']), this.count)
        console.log(t)
        return t
      }
    },
    mounted() {
      this.$store.dispatch('fetchStatistics', { type: this.type })
    }
  }
</script>
