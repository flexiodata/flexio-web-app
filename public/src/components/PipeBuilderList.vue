<template>
  <div class="overflow-y-auto pt1">
    <pipe-transfer-input-blankslate
      class="blankslate mh5 mv3"
      v-if="!has_input">
      <div class="f8 fw6 moon-gray ttu absolute css-corner-title">Input</div>
    </pipe-transfer-input-blankslate>

    <div class="blankslate mh5 mv3" v-if="tasks.length == 0">
      <div class="f8 fw6 moon-gray ttu absolute css-corner-title">Transform</div>
      <div class="lh-copy mid-gray f6 mb3">There are no transformation steps in this pipe.</div>
      <div class="mt3">
        <btn
          btn-md
          btn-primary
          class="ttu b"
        >
          Add a step
        </btn>
      </div>
    </div>
    <div class="pv4" v-else>
      <pipe-builder-item
        v-for="(task, index) in tasks"
        :pipe-eid="pipeEid"
        :item="task"
        :index="index"
        :active-stream-eid="active_stream_eid"
      ></pipe-builder-item>
    </div>

    <pipe-transfer-output-blankslate
      class="blankslate mh5 mv3"
      v-if="!has_output">
      <div class="f8 fw6 moon-gray ttu absolute css-corner-title">Output</div>
    </pipe-transfer-output-blankslate>
  </div>
</template>

<script>
  import { TASK_TYPE_INPUT, TASK_TYPE_OUTPUT } from '../constants/task-type'
  import Btn from './Btn.vue'
  import PipeTransferInputBlankslate from './PipeTransferInputBlankslate.vue'
  import PipeTransferOutputBlankslate from './PipeTransferOutputBlankslate.vue'
  import PipeBuilderItem from './PipeBuilderItem.vue'

  export default {
    props: ['pipe-eid', 'tasks', 'active-subprocess'],
    components: {
      Btn,
      PipeTransferInputBlankslate,
      PipeTransferOutputBlankslate,
      PipeBuilderItem
    },
    computed: {
      input_tasks()  { return _.filter(this.tasks, { type: TASK_TYPE_INPUT }) },
      output_tasks() { return _.filter(this.tasks, { type: TASK_TYPE_OUTPUT }) },
      has_input()    { return this.input_tasks.length > 0 },
      has_output()   { return this.output_tasks.length > 0 },

      active_stream_eid() {
        return ''
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
