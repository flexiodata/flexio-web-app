<template>
  <div class="overflow-y-auto">
    <chart-process-stats title="Pipes Run (Last 30 days)"></chart-process-stats>
    <admin-task-summary></admin-task-summary>
  </div>
</template>

<script>
  import ChartProcessStats from './ChartProcessStats.vue'
  import AdminTaskSummary from './AdminTaskSummary.vue'

  export default {
    components: {
      ChartProcessStats,
      AdminTaskSummary
    }
  }
</script>
