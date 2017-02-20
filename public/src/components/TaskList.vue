cv<template>
  <div class="flex flex-row">
    <div v-if="false" class="flex flex-row relative">
      <div class="flex flex-column relative pointer b--transparent mh2 fx-square-6 fx-square-7-ns tc justify-center  trans-wh"
      >
        <div class="absolute absolute--fill b--dashed b--black-20 black-20 ma1 darken-10">
          <i class="material-icons f1">input</i>
        </div>
      </div>
    </div>
    <task-item
      v-show="!hasInput"
      :is-ghost="true"
      :is-disabled="isInserting"
      @activate="onGhostInputItemActivate"
    >
      <i slot="icon" class="material-icons f3 f2-ns">input</i>
      <span slot="name">Add Input</span>
    </task-item>
    <task-item
      v-for="(task, index) in input_tasks"
      :item="task"
      :index="index"
      :active-item-eid="activeTaskEid"
      :last-task-item="last_task_item"
      :is-disabled="isInserting"
      @activate="onItemActivate"
      @delete="onItemDelete"
      @insert="onItemInsert"
      @cancel-insert="onItemCancelInsert">
    </task-item>
    <div class="mh2 mv1 bl b--black-20"></div>
    <task-item
      v-for="(task, index) in middle_tasks"
      :item="task"
      :index="index"
      :active-item-eid="activeTaskEid"
      :last-task-item="last_task_item"
      :is-disabled="isInserting"
      @activate="onItemActivate"
      @delete="onItemDelete"
      @insert="onItemInsert"
      @cancel-insert="onItemCancelInsert">
    </task-item>
    <task-item
      :is-task-add="true"
      :is-disabled="isInserting"
      :show-name="false"
      @activate="onAddTaskItemActivate"
    >
      <i slot="icon" class="material-icons md-48">add_circle</i>
    </task-item>
    <div class="mh2 mv1 bl b--black-20"></div>
    <task-item
      v-for="(task, index) in output_tasks"
      :item="task"
      :index="index"
      :active-item-eid="activeTaskEid"
      :last-task-item="last_task_item"
      :is-disabled="isInserting"
      @activate="onItemActivate"
      @delete="onItemDelete"
      @insert="onItemInsert"
      @cancel-insert="onItemCancelInsert">
    </task-item>
    <task-item
      v-show="!hasOutput"
      :is-ghost="true"
      :is-disabled="isInserting"
      @activate="onGhostOutputItemActivate"
    >
      <i slot="icon" class="material-icons f3 f2-ns">input</i>
      <span slot="name">Add Output</span>
    </task-item>
  </div>
</template>

<script>
  import { TASK_TYPE_INPUT, TASK_TYPE_OUTPUT } from '../constants/task-type'
  import TaskItem from './TaskItem.vue'

  export default {
    props: ['tasks', 'active-task-eid', 'is-inserting'],
    components: {
      TaskItem
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
      onAddTaskItemActivate(item) {
        if (!this.isInserting)
          this.$emit('add-task-item-activate', item)
      }
    }
  }
</script>
