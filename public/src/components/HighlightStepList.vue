<template>
  <div class="flex flex-row">
    <div
      class="flex flex-row items-center"
      v-for="(step, index) in steps"
    >
      <img :src="getImgSrc(step)" class="db br2" :class="cls" :style="style" v-if="isConnection(step)">
      <div class="flex flex-row items-center justify-center br2 white" :class="[cls, task_cls, getTaskBgColor(step)]" :style="style" v-else>
        <i class="db material-icons child" :class="icon_cls">{{getTaskIcon(step)}}</i>
      </div>
      <div class="f2 mh1 moon-gray material-icons rotate-270" v-if="index < steps.length - 1">arrow_drop_down</div>
    </div>
  </div>
</template>

<script>
  import * as tasks from '../constants/task-info'
  import * as connections from '../constants/connection-info'

  export default {
    props: {
      'steps': {
        type: Array,
        default: []
      },
      'size': {
        type: String,
        default: 'medium'
      }
    },
    computed: {
      cls() {
        return this.size == 'large' ? 'h3 w3' : ''
      },
      task_cls() {
        return this.size == 'large' ? 'h3 w3' : ''
      },
      icon_cls() {
        return this.size == 'large' ? 'f1' : 'f2'
      },
      style() {
        return this.size == 'large' ? '' : 'height: 3rem; width: 3rem'
      }
    },
    methods: {
      getConnectionInfo(type) {
        return _.find(connections, { connection_type: type })
      },
      getTaskInfo(op) {
        return _.find(tasks, { op })
      },
      isConnection(type) {
        var cinfo = this.getConnectionInfo(type)
        return !_.isNil(cinfo)
      },
      getImgSrc(type) {
        var cinfo = this.getConnectionInfo(type)

        // return connection icon
        if (!_.isNil(cinfo))
          return _.get(cinfo, 'icon', '')
      },
      getTaskIcon(op) {
        var tinfo = this.getTaskInfo(op)
        return _.get(tinfo, 'icon', '')
      },
      getTaskBgColor(op) {
        var tinfo = this.getTaskInfo(op)
        return _.get(tinfo, 'bg_color', '')
      }
    }
  }
</script>
