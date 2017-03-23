<template>
  <div class="flex flex-row items-stretch bt b--black-10">
    <div class="flex-fill flex flex-column">
      <div class="f4 pa4 tc" v-if="has_input">Inputs</div>
      <div class="f4 pa4 tc" v-else>1. Choose Input</div>
      <pipe-transfer-input-list
        :tasks="input_tasks"
        v-if="has_input"
      ></pipe-transfer-input-list>
      <pipe-transfer-input-blankslate class="blankslate mh4" v-else></pipe-transfer-input-blankslate>
    </div>
    <div class="flex-fill flex flex-column bl b--black-10">
      <div class="f4 pa4 tc">2. Choose Output</div>
      <div v-if="has_output">Has Output</div>
      <pipe-transfer-output-blankslate class="blankslate mh4" v-else></pipe-transfer-output-blankslate>
    </div>
    <div class="flex-fill">
      <div class="h-100 bl b--black-10">
        <div class="f4 pa4 tc">3. Choose Transformations</div>
        <div class="mh4">TODO; How do we want to expose these here?</div>
      </div>
    </div>
  </div>
</template>

<script>
  import { TASK_TYPE_INPUT, TASK_TYPE_OUTPUT } from '../constants/task-type'
  import Btn from './Btn.vue'
  import PipeTransferInputList from './PipeTransferInputList.vue'
  import PipeTransferInputBlankslate from './PipeTransferInputBlankslate.vue'
  import PipeTransferOutputBlankslate from './PipeTransferOutputBlankslate.vue'

  export default {
    props: ['tasks', 'active-subprocess'],
    components: {
      Btn,
      PipeTransferInputList,
      PipeTransferInputBlankslate,
      PipeTransferOutputBlankslate
    },
    computed: {
      input_tasks()  { return _.filter(this.tasks, { type: TASK_TYPE_INPUT }) },
      output_tasks() { return _.filter(this.tasks, { type: TASK_TYPE_OUTPUT }) },
      has_input()    { return this.input_tasks.length > 0 },
      has_output()   { return this.output_tasks.length > 0 },
    }
  }
</script>
