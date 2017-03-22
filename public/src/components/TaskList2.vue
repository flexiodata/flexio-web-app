<template>
  <div class="overflow-y-auto pv4">
    <task-item2
      v-for="(task, index) in tasks"
      :item="task"
      :index="index"
      :active-stream-eid="active_stream_eid"
    >
    </task-item2>
  </div>
</template>

<script>
  import { TASK_TYPE_INPUT, TASK_TYPE_OUTPUT } from '../constants/task-type'
  import TaskItem2 from './TaskItem2.vue'

  export default {
    props: ['tasks', 'active-subprocess'],
    components: {
      TaskItem2
    },
    computed: {
      active_stream_eid() {
        return ''
      }
    }
  }
</script>
