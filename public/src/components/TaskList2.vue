<template>
  <div class="overflow-y-auto">
    <task-item2
      v-for="(task, index) in tasks"
      :item="task"
      :index="index"
    >
    </task-item2>
  </div>
</template>

<script>
  import { TASK_TYPE_INPUT, TASK_TYPE_OUTPUT } from '../constants/task-type'
  import TaskItem2 from './TaskItem2.vue'

  export default {
    props: ['tasks'],
    components: {
      TaskItem2
    }
  }
</script>
