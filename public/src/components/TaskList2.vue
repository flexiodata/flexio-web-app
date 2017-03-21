<template>
  <div class="overflow-y-auto">
    <task-item2
      v-for="(task, index) in middle_tasks"
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
    props: ['tasks', 'active-task-eid', 'is-inserting'],
    components: {
      TaskItem2
    },
    computed: {
      hasInput() {
        return _.find(this.tasks, { type: TASK_TYPE_INPUT })
      },
      hasOutput() {
        return _.find(this.tasks, { type: TASK_TYPE_OUTPUT })
      },
      middle_tasks() {
        return _
          .chain(this.tasks)
          .reject({ type: TASK_TYPE_INPUT })
          .reject({ type: TASK_TYPE_OUTPUT })
          .value()
      },
      input_tasks() {
        return _.filter(this.tasks, { type: TASK_TYPE_INPUT })
      },
      output_tasks() {
        return _.filter(this.tasks, { type: TASK_TYPE_OUTPUT })
      },
      last_task_item() {
        return _
          .chain(this.middle_tasks)
          .without({ type: TASK_TYPE_INPUT })
          .without({ type: TASK_TYPE_OUTPUT })
          .last()
          .value()
      }
    },
    methods: {
      onItemActivate(item) {
        this.$emit('item-activate', item)
      },
      onItemDelete(item) {
        this.$emit('item-delete', item)
      },
      onItemInsert(item) {
        this.$emit('item-insert', item)
      },
      onItemCancelInsert(item) {
        this.$emit('item-cancel-insert', item)
      },
      onGhostInputItemActivate(item) {
        if (!this.isInserting)
          this.$emit('ghost-input-item-activate', item)
      },
      onGhostOutputItemActivate(item) {
        if (!this.isInserting)
          this.$emit('ghost-output-item-activate', item)
      },
      onAddTaskItem2Activate(item) {
        if (!this.isInserting)
          this.$emit('add-task-item2-activate', item)
      }
    }
  }
</script>
