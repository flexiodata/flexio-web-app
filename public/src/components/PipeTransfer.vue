<template>
  <div class="flex-l flex-row-l items-stretch">
    <div class="flex-fill flex flex-column mr4-l">
      <div class="dib f4 pa4 tc" v-if="has_input">
        <span class="v-mid">Inputs</span>
        <div
          class="v-mid cursor-default moon-gray hover-blue link hint--bottom"
          aria-label="Add another connection"
          @click="$emit('choose-input-connection')"
        >
          <i class="db material-icons f3">add_circle</i>
        </div>
      </div>
      <div class="f4 pa4 tc" v-else>
        <div class="dib v-mid">1. Choose Input</div>
        <btn
          btn-sm
          btn-outline
          class="ttu b light-silver b--black-20 hover-black dib v-mid ml2"
          @click="show_input_chooser = false"
          v-if="show_input_chooser"
        >Cancel</btn>
      </div>
      <pipe-transfer-input-list
        class="flex-fill"
        :tasks="input_tasks"
        v-if="has_input"
      ></pipe-transfer-input-list>
      <pipe-transfer-input-chooser
        class="flex-fill overflow-y-auto"
        :project-eid="projectEid"
        @cancel="show_input_chooser = false"
        v-else-if="show_input_chooser"
      ></pipe-transfer-input-chooser>
      <pipe-transfer-input-blankslate
        class="blankslate"
        @choose-connection="show_input_chooser = true"
        v-else
      ></pipe-transfer-input-blankslate>
      <!--@choose-connection="$emit('choose-input-connection')"-->
    </div>

    <div class="flex-fill flex flex-column mr4-l">
      <div class="f4 pa4 tc" v-if="has_output">Outputs</div>
      <div class="f4 pa4 tc" v-else>2. Choose Output</div>
      <div v-if="has_output">Output configuration goes here...</div>
      <pipe-transfer-output-blankslate class="blankslate" v-else></pipe-transfer-output-blankslate>
    </div>

    <div class="flex-fill">
      <div class="h-100">
        <div class="f4 pa4 tc">3. Add Transformations</div>
        <div class="blankslate">
          <div class="lh-copy mid-gray f6 mb3">Transformation steps can be added in the builder view.</div>
          <div class="mt3">
            <btn
              btn-md
              btn-primary
              class="ttu b"
              @click="$emit('open-builder')"
            >
              Take me to the builder
            </btn>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { TASK_TYPE_INPUT, TASK_TYPE_OUTPUT } from '../constants/task-type'
  import Btn from './Btn.vue'
  import PipeTransferInputList from './PipeTransferInputList.vue'
  import PipeTransferInputChooser from './PipeTransferInputChooser.vue'
  import PipeTransferInputBlankslate from './PipeTransferInputBlankslate.vue'
  import PipeTransferOutputBlankslate from './PipeTransferOutputBlankslate.vue'
  import sameHeights from './mixins/same-heights'

  export default {
    props: ['tasks', 'project-eid', 'active-subprocess'],
    mixins: [sameHeights],
    components: {
      Btn,
      PipeTransferInputList,
      PipeTransferInputChooser,
      PipeTransferInputBlankslate,
      PipeTransferOutputBlankslate
    },
    data() {
      return {
        show_input_chooser: false
      }
    },
    computed: {
      input_tasks()  { return _.filter(this.tasks, { type: TASK_TYPE_INPUT }) },
      output_tasks() { return _.filter(this.tasks, { type: TASK_TYPE_OUTPUT }) },
      has_input()    { return this.input_tasks.length > 0 },
      has_output()   { return this.output_tasks.length > 0 }
    },
    mounted() {
      this.sameHeights('.blankslate')
    }
  }
</script>
