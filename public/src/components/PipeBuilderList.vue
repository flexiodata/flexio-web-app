<template>
  <div class="overflow-y-auto pv4">
    <pipe-builder-item
      v-for="(task, index) in tasks"
      :item="task"
      :index="index"
      :active-stream-eid="active_stream_eid"
    >
    </pipe-builder-item>
  </div>
</template>

<script>
  import { TASK_TYPE_INPUT, TASK_TYPE_OUTPUT } from '../constants/task-type'
  import PipeBuilderItem from './PipeBuilderItem.vue'

  export default {
    props: ['tasks', 'active-subprocess'],
    components: {
      PipeBuilderItem
    },
    computed: {
      active_stream_eid() {
        return ''
      }
    }
  }
</script>
