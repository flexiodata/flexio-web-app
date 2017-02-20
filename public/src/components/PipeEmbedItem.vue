<template>
  <div>
    <a
      target="_blank"
      rel="noopener noreferrer"
      class="css-pipe-item db link ma0 pv2a ph3 ba b--sui-segment br2 cursor-default shadow-sui-segment trans-pm no-select no-underline"
      :href="pipe_src"
    >
      <div class="flex flex-row items-center">
        <div class="flex-none mr2">
          <connection-icon :type="input_type" class="dib v-mid br2 fx-square-3"></connection-icon>
          <i class="material-icons md-24 black-40 v-mid fa-rotate-270" style="margin: 0 -4px">arrow_drop_down</i>
          <connection-icon :type="output_type" class="dib v-mid br2 fx-square-3"></connection-icon>
        </div>
        <div class="flex-fill mh2 fw6 f6 f5-ns black-60 mv0 lh-title truncate">
          <span :title="item.name">{{item.name}}</span>
        </div>
      </div>
    </a>
    <div class="flex flex-row items-center justify-end mr1 mt1 f7 fw6">
      <a
        href="https://www.flex.io"
        class="css-footer link black-50 no-underline hint--top"
        aria-label="Visit www.flex.io"
        target="_blank"
        rel="noopener noreferrer"
      >
        <span style="letter-spacing: -0.05em">Powered by</span>
        <img src="../assets/logo-flexio-14x49.png" class="dib" alt="Flex.io">
      </a>
    </div>
  </div>
</template>

<script>
  import { ROUTE_PIPEHOME } from '../constants/route'
  import { TASK_TYPE_INPUT, TASK_TYPE_OUTPUT } from '../constants/task-type'
  import ConnectionIcon from './ConnectionIcon.vue'

  export default {
    props: ['item'],
    components: {
      ConnectionIcon
    },
    computed: {
      input_type() { return this.getTaskConnectionType(TASK_TYPE_INPUT) },
      output_type() { return this.getTaskConnectionType(TASK_TYPE_OUTPUT) },
      pipe_src() {
        var loc = window.location
        return loc.protocol+'//'+loc.host+'/app/pipe/'+this.item.eid
      }
    },
    methods: {
      getTaskConnectionType(task_type) {
        var task = _.find(this.item.task, { type: task_type })
        return _.get(task, 'metadata.connection_type')
      }
    }
  }
</script>

<style lang="less" scoped>
  .css-pipe-item {
    border-color: rgba(34, 36, 38, 0.15);

    &:hover {
      background-color: rgba(0,0,0,0.05);
      border-color: rgba(0,0,0,0.2);
    }
  }

  .css-footer:hover {
    box-shadow: 0 1px rgba(0,0,0,0.3);
  }
</style>
