<template>
  <div class="flex flex-row items-stretch bt b--black-10">
    <div class="flex-fill flex flex-column">
      <div class="f4 pa4 tc">1. Choose Input</div>
      <div v-if="has_input">Has Input</div>
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
  import PipeTransferInputBlankslate from './PipeTransferInputBlankslate.vue'
  import PipeTransferOutputBlankslate from './PipeTransferOutputBlankslate.vue'

  export default {
    props: ['tasks', 'active-subprocess'],
    components: {
      Btn,
      PipeTransferInputBlankslate,
      PipeTransferOutputBlankslate
    },
    computed: {
      has_input() {
        return _.find(this.tasks, { type: TASK_TYPE_INPUT })
      },
      has_output() {
        return _.find(this.tasks, { type: TASK_TYPE_OUTPUT })
      }
    }
  }
</script>
