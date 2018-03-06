<template>
  <div :class="iconsOnly ? 'flex flex-row items-center' : ''">
    <task-summary-item
      v-for="(task, index) in tasks"
      :item="task"
      :index="index"
      :icon-only="iconsOnly"
    >
    </task-summary-item>
  </div>
</template>

<script>
  import TaskSummaryItem from './TaskSummaryItem.vue'

  export default {
    props: {
      'tasks': {
        type: Array,
        required: true
      },
      'icons-only': {
        type: Boolean,
        default: false
      }
    },
    components: {
      TaskSummaryItem
    }
  }
</script>
