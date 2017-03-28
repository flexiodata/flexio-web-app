<template>
  <article class="flex flex-row pb3">
    <div class="flex-none">
      <div
        class="pa1 mr2 br1 white trans-wh tc relative"
        :class="[ bg_color ]"
      >
        <i class="db material-icons f5">{{task_icon}}</i>
      </div>
    </div>
    <div class="flex-fill">
      <div class="mid-gray f5 cursor-default lh-title" style="margin-top: 2px">{{index+1}}. {{display_name}}</div>
      <div class="mid-gray f7 cursor-default mt1" v-if="description.length > 0">{{description}}</div>
    </div>
  </article>
</template>

<script>
  import taskItemHelper from './mixins/task-item-helper'

  export default {
    props: ['item', 'index'],
    mixins: [taskItemHelper],
    computed: {
      task() { return this.item },
      description() { return _.get(this, 'task.description', '') }
    }
  }
</script>
