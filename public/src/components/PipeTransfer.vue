<template>
  <div class="flex-l flex-row-l items-stretch overflow-y-auto">

    <div class="flex-fill flex flex-column mr3-l">
      <div class="f4 pa2 bg-blue white tc css-pipe-transfer-header">
        <div class="flex flex-row items-center justify-center relative" v-if="has_input">
          <div v-if="show_input_chooser">Add Input</div>
          <div v-else>Input</div>
          <button
            type="button"
            class="pa0 ml2 white-50 hover-white link hint--bottom absolute right-0"
            aria-label="Add another connection"
            @click="show_input_chooser = true"
            v-if="allow_multiple_inputs && !show_input_chooser"
          >
            <i class="db material-icons f4">add_circle</i>
          </button>
          <button
            type="button"
            class="pa0 ml2 white-50 hover-white link hint--bottom absolute right-0"
            aria-label="Cancel add connection"
            @click="show_input_chooser = false"
            v-if="allow_multiple_inputs && show_input_chooser"
          >
            <i class="db material-icons f4">cancel</i>
          </button>
        </div>
        <div v-else>1. Choose Input</div>
      </div>
      <pipe-transfer-input-list
        class="flex-fill"
        :tasks="input_tasks"
        v-if="has_input && !show_input_chooser"
        @input-add-items="addInputItems"
        @input-delete="deleteInput"
      ></pipe-transfer-input-list>
      <pipe-transfer-input-chooser
        class="flex-fill overflow-y-auto"
        :project-eid="projectEid"
        @cancel="show_input_chooser = false"
        @choose-input="addInput"
        v-else
      ></pipe-transfer-input-chooser>
    </div>

    <div class="flex-fill flex flex-column mr3-l">
      <div class="f4 pa2 bg-blue white tc css-pipe-transfer-header">
        <div class="flex flex-row items-center justify-center relative" v-if="has_output">
          <div v-if="show_output_chooser">Add Output</div>
          <div v-else>Output</div>
          <button
            type="button"
            class="pa0 ml2 white-50 hover-white link hint--bottom absolute right-0"
            aria-label="Add another connection"
            @click="show_output_chooser = true"
            v-if="allow_multiple_outputs && !show_output_chooser"
          >
            <i class="db material-icons f4">add_circle</i>
          </button>
          <button
            type="button"
            class="pa0 ml2 white-50 hover-white link hint--bottom absolute right-0"
            aria-label="Cancel add connection"
            @click="show_output_chooser = false"
            v-if="allow_multiple_outputs && show_output_chooser"
          >
            <i class="db material-icons f4">cancel</i>
          </button>
        </div>
        <div v-else>2. Choose Output</div>
      </div>
      <pipe-transfer-output-list
        class="flex-fill"
        :tasks="output_tasks"
        v-if="has_output"
      ></pipe-transfer-output-list>
      <pipe-transfer-output-chooser
        class="flex-fill overflow-y-auto"
        :project-eid="projectEid"
        @cancel="show_output_chooser = false"
      ></pipe-transfer-output-chooser>
    </div>

    <div class="flex-none flex flex-column css-pipe-transfer-column-transform">
      <div class="f4 pa2 bg-blue white tc css-pipe-transfer-header">


        <div class="flex flex-row items-center justify-center relative">
          <div v-if="has_tasks">Transformations</div>
          <div v-else>3. Add Transformations</div>
          <button
            type="button"
            class="pa0 ml2 white-50 hover-white link hint--bottom-left absolute right-0"
            aria-label="Take me to the builder"
            @click="$emit('open-builder')"
            v-if="has_tasks"
          >
            <i class="db material-icons f4">edit</i>
          </button>
        </div>
      </div>
      <pipe-transfer-transform-list
        class="flex-fill pa3"
        :tasks="transform_tasks"
        v-if="has_tasks"
      ></pipe-transfer-transform-list>
      <div class="flex-fill" v-else>
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
    props: ['tasks', 'pipe-eid', 'project-eid', 'active-subprocess'],
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
      has_tasks()  { return this.transform_tasks.length > 0 }
    },
    methods: {
      addInputItems(input) {

      },
      deleteInput(input) {
        var eid = this.pipeEid
        var task_eid = _.get(input, 'eid', '')
        this.$store.dispatch('deletePipeTask', { eid, task_eid })
      },
      addInput(connection) {
        // insert input after any existing inputs
        var input_idx = _.findLastIndex(this.tasks, (t) => { return _.get(t, 'type') == TASK_TYPE_INPUT })

        // if there are no inputs, insert at the beginning of the pipe
        if (input_idx == -1)
          input_idx = 0
           else
          input_idx++

        var conn_identifier = _.get(connection, 'ename', '')
        conn_identifier = conn_identifier.length > 0 ? conn_identifier : _.get(connection, 'eid', '')

        var eid = this.pipeEid
        var attrs = {
          index: input_idx,
          metadata: {
            connection_type: _.get(connection, 'connection_type', '')
          },
          type: TASK_TYPE_INPUT,
          params: {
            items: []
          }
        }

        if (conn_identifier.length > 0)
          _.set(attrs, 'params.connection', conn_identifier)

        // add input task
        this.$store.dispatch('createPipeTask', { eid, attrs })
      }
    }
  }
</script>

<style lang="less">
  @import "../stylesheets/variables.less";

  /*
  .css-pipe-transfer-header {
    box-shadow: inset -1px -1px 0 rgba(0,0,0,0.2);
  }
  */

  @media @breakpoint-large {
    .css-pipe-transfer-column-transform {
      width: 32%;
      max-width: 320px;
    }
  }
</style>
