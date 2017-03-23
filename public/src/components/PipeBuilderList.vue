<template>
  <div>
    <pipe-transfer-input-blankslate
      class="blankslate mh5 mb3"
      v-if="tasks.length == 0">
      <div class="f8 fw6 moon-gray ttu absolute css-corner-title">Input</div>
    </pipe-transfer-input-blankslate>

    <div class="blankslate mh5 mb3" v-if="tasks.length == 0">
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
    <div class="overflow-y-auto pv4" v-else>
      <pipe-builder-item
        v-for="(task, index) in tasks"
        :item="task"
        :index="index"
        :active-stream-eid="active_stream_eid"
      ></pipe-builder-item>
    </div>

    <pipe-transfer-output-blankslate
      class="blankslate mh5 mb3"
      v-if="tasks.length == 0">
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
    props: ['tasks', 'active-subprocess'],
    components: {
      Btn,
      PipeTransferInputBlankslate,
      PipeTransferOutputBlankslate,
      PipeBuilderItem
    },
    computed: {
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
