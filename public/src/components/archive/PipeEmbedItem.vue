<template>
  <div>
    <a
      target="_blank"
      rel="noopener noreferrer"
      class="css-list-item db link ma0 pv2a ph3 ba br2 cursor-default shadow-sui-segment trans-pm no-select no-underline"
      :href="pipe_src"
    >
      <div class="flex flex-row items-center">
        <div class="flex-none mr2">
          <service-icon :type="input_type" class="dib v-mid br2 square-3"></service-icon>
          <i class="material-icons md-24 black-40 v-mid rotate-270" style="margin: 0 -4px">arrow_drop_down</i>
          <service-icon :type="output_type" class="dib v-mid br2 square-3"></service-icon>
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
  import { TASK_OP_INPUT, TASK_OP_OUTPUT } from '../constants/task-op'
  import ServiceIcon from './ServiceIcon.vue'

  export default {
    props: ['item'],
    components: {
      ServiceIcon
    },
    computed: {
      input_type() { return this.getTaskConnectionType(TASK_OP_INPUT) },
      output_type() { return this.getTaskConnectionType(TASK_OP_OUTPUT) },
      pipe_src() {
        var loc = window.location
        return loc.protocol+'//'+loc.host+'/app/pipes/'+this.item.eid
      }
    },
    methods: {
      getTaskConnectionType(task_op) {
        var task = _.find(this.item.task, { op: task_op })
        return _.get(task, 'metadata.connection_type')
      }
    }
  }
</script>
