<template>
  <div>
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
