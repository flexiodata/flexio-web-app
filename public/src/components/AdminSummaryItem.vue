<template>
  <div class="pb3 ma3 bb b--black-10">
    <h2 class="ma0 mb3">{{title}}</h2>
    <admin-summary-chart :type="type"></admin-summary-chart>
  </div>
</template>

<script>
  import AdminSummaryChart from './AdminSummaryChart.vue'

  export default {
    props: {
      'item': {
        type: Object,
        required: true
      }
    },
    components: {
      AdminSummaryChart
    },
    computed: {
      title() {
        return _.get(this.item, 'title', '')
      },
      type() {
        return _.get(this.item, 'type', '')
      }
    }
  }
</script>
