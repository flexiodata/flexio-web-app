<template>
  <div :class="iconsOnly ? 'flex flex-row items-center' : ''">
    <pipe-task-item
      v-for="(task, index) in tasks"
      :item="task"
      :index="index"
      :icon-only="iconsOnly"
    >
    </pipe-task-item>
  </div>
</template>

<script>
  import PipeTaskItem from './PipeTaskItem.vue'

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
      PipeTaskItem
    }
  }
</script>
