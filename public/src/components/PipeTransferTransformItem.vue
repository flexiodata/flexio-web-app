<template>
  <article class="flex flex-row pt1 pb3">
    <div class="flex-none">
      <div
        class="pointer pa1 mr3 br1 white trans-wh tc relative"
        :class="[ bg_color ]"
      >
        <i class="db material-icons f4">{{task_icon}}</i>
      </div>
    </div>
    <div class="flex-fill relative" :style="title_style">
      <div class="flex flex-row">
        <div class="f5 lh-title mr1">{{index+1}}.</div>
        <div class="flex-fill f5 lh-title">{{display_name}}</div>
      </div>
      <div class="f7 lh-title gray mt1" v-if="description.length > 0">{{description}}</div>
      </div>
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
      description() { return _.get(this, 'task.description', '') },
      title_style() { return this.description.length > 0 ? 'top: -3px' : 'top: 3px' }
    }
  }
</script>
