<template>
  <div class="overflow-y-auto bg-nearer-white">
    <div class="ma4 bg-white css-dashboard-box">
      <stats-pipes-run title="Pipes Run (Last 30 days)" :is-admin="true"></stats-pipes-run>
    </div>
    <div class="ma4 mt0 bg-white css-dashboard-box">
      <stats-top-pipes></stats-top-pipes>
    </div>
    <div class="ma4 mt0 bg-white css-dashboard-box">
      <stats-top-tasks></stats-top-tasks>
    </div>
  </div>
</template>

<script>
  import StatsPipesRun from './StatsPipesRun.vue'
  import StatsTopPipes from './StatsTopPipes.vue'
  import StatsTopTasks from './StatsTopTasks.vue'

  export default {
    components: {
      StatsPipesRun,
      StatsTopPipes,
      StatsTopTasks
    }
  }
</script>

<style lang="less">
  .css-dashboard-box {
    box-shadow: 0 4px 24px -4px rgba(0,0,0,0.2);
  }
</style>
