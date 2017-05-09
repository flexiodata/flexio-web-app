<template>
  <article class="flex flex-row pt1 pb3">
    <div class="flex-none">
      <connection-icon
        :type="ctype"
        class="br1 mr2"
        style="width: 28px; height: 28px"
        v-if="show_connection_icon"
      ></connection-icon>
      <div
        class="pointer pa1 mr2 br1 white trans-wh tc relative"
        :class="[ bg_color ]"
        v-else
      >
        <i class="db material-icons f4">{{task_icon}}</i>
      </div>
    </div>
    <div class="flex-fill relative" :style="title_style">
      <div class="flex flex-row">
        <div class="f5 lh-title mr1">{{index+1}}.</div>
        <div class="flex-fill f5 lh-title">{{display_name}}</div>
      </div>
      <div class="f7 lh-title gray mt1" v-html="short_description" v-if="description.length > 0"></div>
      </div>
    </div>
  </article>
</template>

<script>
  import ConnectionIcon from './ConnectionIcon.vue'
  import TaskItemHelper from './mixins/task-item-helper'

  export default {
    props: ['item', 'index'],
    mixins: [TaskItemHelper],
    components: {
      ConnectionIcon
    },
    computed: {
      task() {
        return _.get(this, 'item', {})
      },
      description() {
        return _.get(this, 'task.description', '')
      },
      title_style() {
        return this.description.length > 0 ? 'top: -3px' : 'top: 3px'
      },
      short_description() {
        var s = this.description

        s = s.replace(/[\r\n]+/g, function(s) {
          return '<br>'
        })

        return s
      }
    }
  }
</script>
