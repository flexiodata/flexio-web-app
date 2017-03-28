<template>
  <div class="flex-l flex-row-l items-stretch overflow-y-auto">
    <div class="flex-fill flex flex-column">
      <div class="f4 pa3 tc bg-blue white" style="box-shadow: inset -1px -1px 0 rgba(0,0,0,0.15)">
        <div class="flex flex-row items-center justify-center" v-if="has_input">
          <div>Input</div>
          <button
            type="button"
            class="pa0 ml2 white-50 hover-white link hint--bottom"
            aria-label="Add another connection"
            @click="$emit('choose-input-connection')"
            v-if="allow_multiple_inputs"
          >
            <i class="db material-icons f4">add_circle</i>
          </button>
        </div>
        <div v-else>1. Choose Input</div>
      </div>
      <pipe-transfer-input-list
        class="flex-fill bl-l br-l b--black-10"
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
      <div class="f4 pa3 tc bg-blue white" style="box-shadow: inset -1px -1px 0 rgba(0,0,0,0.15)">
        <div v-if="has_output">
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
        <div v-else>2. Choose Output</div>
      </div>
      <pipe-transfer-output-chooser
        class="flex-fill overflow-y-auto br-l b--black-10"
        :project-eid="projectEid"
        @cancel="show_output_chooser = false"
      ></pipe-transfer-output-chooser>
    </div>


    <div class="flex-none flex flex-column" style="width: 30%; max-width: 350px">
      <div class="f4 pa3 tc bg-blue white" style="box-shadow: inset -1px -1px 0 rgba(0,0,0,0.15)">
        <div v-if="has_tasks">Transformations</div>
        <div v-else>3. Add Transformations</div>
      </div>
      <pipe-transfer-transform-list
        class="flex-fill br-l b--black-10 pa3"
        :tasks="transform_tasks"
        v-if="has_tasks"
      ></pipe-transfer-transform-list>
      <div class="flex-fill br-l b--black-10" v-else>
        <div class="ma3">
          <div class="lh-copy mid-gray f6 mb3 tl tc-m i">There are no transformation steps in this pipe. Transformation steps can be added in the builder view.</div>
          <div class="tc">
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
  import PipeTransferOutputChooser from './PipeTransferOutputChooser.vue'
  import PipeTransferTransformList from './PipeTransferTransformList.vue'

  export default {
    props: ['tasks', 'project-eid', 'active-subprocess'],
    components: {
      Btn,
      PipeTransferInputList,
      PipeTransferInputChooser,
      PipeTransferOutputChooser,
      PipeTransferTransformList
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
      input_tasks() { return _.filter(this.tasks, { type: TASK_TYPE_INPUT }) },
      output_tasks() { return _.filter(this.tasks, { type: TASK_TYPE_OUTPUT }) },

      transform_tasks() {
        return _.reject(this.tasks, (t) => {
          var task_type = _.get(t, 'type')
          return task_type == TASK_TYPE_INPUT || task_type == TASK_TYPE_OUTPUT
        })
      },

      has_input()  { return this.input_tasks.length > 0 },
      has_output() { return this.output_tasks.length > 0 },
      has_tasks()  { return this.transform_tasks.length > 0 },
    }
  }
</script>
