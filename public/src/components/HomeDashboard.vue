<template>
  <div class="overflow-y-auto bg-nearer-white">
    <div class="bg-white ma4 css-dashboard-box">
      <chart-process-stats title="Pipes Run (Last 30 days)"></chart-process-stats>
    </div>
    <div class="bg-white ma4 mt0 css-dashboard-box">
      <chart-process-stats title="Pipes Run (Last 30 days)"></chart-process-stats>
    </div>
  </div>
</template>

<script>
  import ChartProcessStats from './ChartProcessStats.vue'

  export default {
    components: {
      ChartProcessStats
    }
  }
</script>

<style lang="less">
  .css-dashboard-box {
    box-shadow: 0 4px 24px -4px rgba(0,0,0,0.125);
  }
</style>
