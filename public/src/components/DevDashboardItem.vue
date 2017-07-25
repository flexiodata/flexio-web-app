<template>
  <div class="f6 pa3">
    <div class="mb3">
      <span class="f4">{{pipe_name}}</span><span class="ml2 silver">({{pipe_eid}})</span>
    </div>
    <bar-chart
      :height="100"
      :config="item.chart"
      :options="{
        maintainAspectRatio: false,
        legend: {
          display: false
        },
        animation: {
          duration: 0
        }
      }"
    ></bar-chart>
  </div>
</template>

<script>
  import BarChart from './BarChart.vue'

  export default {
    props: {
      'item': {
        type: Object,
        required: true
      }
    },
    components: {
      BarChart
    },
    computed: {
      pipe_name() {
        return _.get(this.item, 'pipe.name', '')
      },
      pipe_eid() {
        return _.get(this.item, 'pipe.eid', '')
      },
      pipe_items() {
        return _.get(this.item, 'items', '')
      },
      pipe_seq() {
        return _.map(this.pipe_items, (s) => {
          return _.get(s, 'total_count', 0)
        })
      }
    }
  }
</script>
