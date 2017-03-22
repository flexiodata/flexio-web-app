<template>
  <div class="flex flex-row items-stretch bt b--black-10">
    <div class="flex-fill">
      <div class="h-100">
        <div class="f4 pa3 tc">1. Choose Input</div>
        <div v-if="has_input">Has Input</div>
        <div v-else>
          Show empty input instructions/options...
        </div>
      </div>
    </div>
    <div class="flex-fill">
      <div class="h-100 bl b--black-10">
        <div class="f4 pa3 tc">2. Choose Output</div>
        <div v-if="has_output">Has Output</div>
        <div v-else>
          Show empty output instructions/options...
        </div>
      </div>
    </div>
    <div class="flex-fill">
      <div class="h-100 bl b--black-10">
        <div class="f4 pa3 tc">3. Choose Transformations</div>
      </div>
    </div>
  </div>
</template>

<script>
  import { TASK_TYPE_INPUT, TASK_TYPE_OUTPUT } from '../constants/task-type'

  export default {
    props: ['tasks', 'active-subprocess'],
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
