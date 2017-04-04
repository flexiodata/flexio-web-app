<template>
  <div class="overflow-y-auto">
    <div class="blankslate mv3 mr4" v-if="tasks.length == 0">
      <div class="lh-copy mid-gray mb3 i">There are no steps in this pipe.</div>
      <div class="mt3">
        <btn
          btn-md
          btn-primary
          class="ttu b"
          @click="insertNewTask(-1)"
        >
          Add a step
        </btn>
      </div>
    </div>
    <div class="pb3" v-else>
      <pipe-builder-item
        v-for="(task, index) in tasks"
        :pipe-eid="pipeEid"
        :item="task"
        :index="index"
        :tasks="tasks"
        :active-process="activeProcess"
        :project-connections="projectConnections"
        @insert-task="insertNewTask"
      ></pipe-builder-item>
    </div>
  </div>
</template>

<script>
  import { TASK_TYPE_INPUT, TASK_TYPE_OUTPUT } from '../constants/task-type'
  import Btn from './Btn.vue'
  import PipeBuilderItem from './PipeBuilderItem.vue'

  export default {
    props: ['pipe-eid', 'tasks', 'active-process', 'project-connections'],
    components: {
      Btn,
      PipeBuilderItem
    },
    computed: {
      input_tasks()  { return _.filter(this.tasks, { type: TASK_TYPE_INPUT }) },
      output_tasks() { return _.filter(this.tasks, { type: TASK_TYPE_OUTPUT }) },
      has_input()    { return this.input_tasks.length > 0 },
      has_output()   { return this.output_tasks.length > 0 }
    },
    methods: {
      insertNewTask(idx) {
        var eid = this.pipeEid
        var attrs = {
          index: _.defaultTo(idx, -1),
          params: {}
        }
        this.$store.dispatch('createPipeTask', { eid, attrs })
      }
    }
  }
</script>

<style>
  .css-corner-title {
    top: 0.5rem;
    left: 0.625rem;
  }
</style>
