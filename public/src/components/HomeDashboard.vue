<template>
  <div class="overflow-y-auto bg-nearer-white">
    <div class="ma4">
      <div class="f3 f2-ns mb2">Hey, {{active_user_firstname}}!</div>
      <div class="f4 f3-ns">Welcome to the Flex.io Dashboard.</div>
      <div class="mt3">
        <div class="dib nl2 nb2 nr2">
          <help-items
            help-message="I need help getting started with Flex.io..."
            :items="['quick-start', 'api-docs', 'templates', 'help']"
          ></help-items>
        </div>
      </div>
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
  import { mapState, mapGetters } from 'vuex'
  import ChartProcessStats from './ChartProcessStats.vue'
  import HelpItems from './HelpItems.vue'

  export default {
    components: {
      ChartProcessStats,
      HelpItems
    },
    computed: {
      ...mapState([
        'active_user_eid'
      ]),
      active_user() {
        var user = this.getActiveUser()
        return user ? user : {}
      },
      active_user_firstname() {
        return _.get(this.active_user, 'first_name', '')
      }
    },
    methods: {
      ...mapGetters([
        'getActiveUser'
      ])
    }
  }
</script>

<style lang="less">
  .css-dashboard-box {
    box-shadow: 0 4px 24px -4px rgba(0,0,0,0.2);
  }
</style>
