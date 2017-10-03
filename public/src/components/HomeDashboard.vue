<template>
  <div class="overflow-y-auto bg-nearer-white">
    <div class="ma4">
      <help-items
        :items="['quick-start', 'api-docs', 'templates', 'help']"
        help-message="I need help getting started with Flex.io..."
      ></help-items>
    </div>
    <div class="ma4 mt0 bg-white css-dashboard-box">
      <chart-process-stats title="Pipes Run (Last 30 days)"></chart-process-stats>
    </div>
    <div class="ma4 mt0 bg-white css-dashboard-box">
      <chart-process-stats title="Pipes Run (Last 30 days)"></chart-process-stats>
    </div>
  </div>
</template>

<script>
  import ChartProcessStats from './ChartProcessStats.vue'
  import HelpItems from './HelpItems.vue'

  export default {
    components: {
      ChartProcessStats,
      HelpItems
    }
  }
</script>

<style lang="less">
  .css-dashboard-box {
    box-shadow: 0 4px 24px -4px rgba(0,0,0,0.2);
  }
</style>
