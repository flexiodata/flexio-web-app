<template>
  <div class="flex-l flex-row-l items-stretch overflow-y-auto">
    <div class="flex-fill flex flex-column">
      <div class="f4 pa3 tc bg-blue white" style="box-shadow: inset -1px -1px 0 rgba(0,0,0,0.15)" v-if="has_input">
        <span class="v-mid">Input</span>
        <div
          class="v-mid cursor-default moon-gray hover-blue link hint--bottom"
          aria-label="Add another connection"
          @click="$emit('choose-input-connection')"
          v-if="allow_multiple_inputs"
        >
          <i class="db material-icons f3">add_circle</i>
        </div>
      </div>
      <div class="f4 pa3 tc bg-blue white" style="box-shadow: inset -1px -1px 0 rgba(0,0,0,0.15)" v-else>
        <div class="v-mid">1. Choose Input</div>
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
        class="flex-fill overflow-y-auto bl-l br-l b--black-10"
        :project-eid="projectEid"
        @cancel="show_input_chooser = false"
        v-else
      ></pipe-transfer-input-chooser>
    </div>


    <div class="flex-fill flex flex-column">
      <div class="f4 pa3 tc bg-blue white" style="box-shadow: inset -1px -1px 0 rgba(0,0,0,0.15)" v-if="has_output">
        <span class="v-mid">Output</span>
        <div
          class="v-mid cursor-default moon-gray hover-blue link hint--bottom"
          aria-label="Add another connection"
          @click="$emit('choose-output-connection')"
          v-if="allow_multiple_outputs"
        >
          <i class="db material-icons f3">add_circle</i>
        </div>
      </div>
      <div class="f4 pa3 tc bg-blue white" style="box-shadow: inset -1px -1px 0 rgba(0,0,0,0.15)" v-else>
        <div class="v-mid">2. Choose Output</div>
        <btn
          btn-sm
          btn-outline
          class="ttu b light-silver b--black-20 hover-black dib v-mid ml2"
          @click="show_output_chooser = false"
          v-if="show_output_chooser"
        >Cancel</btn>
      </div>
      <pipe-transfer-output-chooser
        class="flex-fill overflow-y-auto br-l b--black-10"
        :project-eid="projectEid"
        @cancel="show_output_chooser = false"
      ></pipe-transfer-output-chooser>
    </div>


    <div class="flex-fill flex flex-column">
      <div class="f4 pa3 tc bg-blue white" style="box-shadow: inset -1px -1px 0 rgba(0,0,0,0.15)">
        <div class="v-mid">3. Add Transformations</div>
      </div>
      <div class="flex-fill br-l b--black-10">
        <div class="blankslate ma4">
          <div class="lh-copy mid-gray f6 mb4 tl-l i">There are no transformations steps in this pipe. Transformation steps can be added in the builder view.</div>
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
</template>

<script>
  import { TASK_TYPE_INPUT, TASK_TYPE_OUTPUT } from '../constants/task-type'
  import Btn from './Btn.vue'
  import PipeTransferInputList from './PipeTransferInputList.vue'
  import PipeTransferInputChooser from './PipeTransferInputChooser.vue'
  import PipeTransferInputBlankslate from './PipeTransferInputBlankslate.vue'
  import PipeTransferOutputChooser from './PipeTransferOutputChooser.vue'
  import PipeTransferOutputBlankslate from './PipeTransferOutputBlankslate.vue'

  export default {
    props: ['tasks', 'project-eid', 'active-subprocess'],
    components: {
      Btn,
      PipeTransferInputList,
      PipeTransferInputChooser,
      PipeTransferInputBlankslate,
      PipeTransferOutputChooser,
      PipeTransferOutputBlankslate
    },
    data() {
      return {
        show_input_chooser: false,
        show_output_chooser: false,
        allow_multiple_inputs: false,
        allow_multiple_outputs: false
      }
    },
    computed: {
      input_tasks()  { return _.filter(this.tasks, { type: TASK_TYPE_INPUT }) },
      output_tasks() { return _.filter(this.tasks, { type: TASK_TYPE_OUTPUT }) },
      has_input()    { return this.input_tasks.length > 0 },
      has_output()   { return this.output_tasks.length > 0 }
    }
  }
</script>
