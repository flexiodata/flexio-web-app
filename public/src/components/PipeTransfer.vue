<template>
  <div class="flex-l flex-row-l items-stretch overflow-y-auto">

    <div class="flex-fill flex flex-column bg-white mr3-l br2-l ba-l b--white-box">
      <div class="f5 pa2 pa3-l relative ttu tc tl-l fw6 css-pipe-transfer-column-header">
        <div v-if="has_input">
          <div v-if="show_input_chooser">Add Input</div>
          <div v-else>Input</div>
          <button
            type="button"
            class="ma2 mv3-l black-30 hover-black-60 link hint--bottom-left absolute top-0 right-0"
            aria-label="Add another input"
            @click="show_input_chooser = true"
            v-if="allow_multiple_inputs && !show_input_chooser"
          >
            <i class="db material-icons f4">add_circle</i>
          </button>
          <button
            type="button"
            class="ma2 mv3-l black-30 hover-black-60 link hint--bottom-left absolute top-0 right-0"
            aria-label="Cancel add input"
            @click="show_input_chooser = false"
            v-if="allow_multiple_inputs && show_input_chooser"
          >
            <i class="db material-icons f4">cancel</i>
          </button>
        </div>
        <div v-else>Choose Input</div>
      </div>
      <pipe-transfer-input-list
        class="flex-fill bg-white ph3-l pb3-l br2 br--bottom overflow-y-auto-l"
        :tasks="input_tasks"
        @input-delete="deleteInput"
        v-if="has_input && !show_input_chooser"
      ></pipe-transfer-input-list>
      <pipe-transfer-input-chooser
        class="flex-fill bg-white ph3-l pb3-l br2 br--bottom overflow-y-auto-l"
        @cancel="show_input_chooser = false"
        @choose-input="addInput"
        v-else
      ></pipe-transfer-input-chooser>
    </div>

    <div class="flex-fill flex flex-column bg-white mr3-l br2-l ba-l b--white-box">
      <div class="f5 pa2 pa3-l relative ttu tc tl-l fw6 css-pipe-transfer-column-header">
        <div v-if="has_output">
          <div v-if="show_output_chooser">Add Output</div>
          <div v-else>Output</div>
          <button
            type="button"
            class="ma2 mv3-l black-30 hover-black-60 link hint--bottom-left absolute top-0 right-0"
            aria-label="Add another output"
            @click="show_output_chooser = true"
            v-if="allow_multiple_outputs && !show_output_chooser"
          >
            <i class="db material-icons f4">add_circle</i>
          </button>
          <button
            type="button"
            class="ma2 mv3-l black-30 hover-black-60 link hint--bottom-left absolute top-0 right-0"
            aria-label="Cancel add output"
            @click="show_output_chooser = false"
            v-if="allow_multiple_outputs && show_output_chooser"
          >
            <i class="db material-icons f4">cancel</i>
          </button>
        </div>
        <div v-else>Choose Output</div>
      </div>
      <pipe-transfer-output-list
        class="flex-fill bg-white ph3-l pb3-l br2 br--bottom overflow-y-auto-l"
        :tasks="output_tasks"
        @output-delete="deleteOutput"
        v-if="has_output && !show_output_chooser"
      ></pipe-transfer-output-list>
      <pipe-transfer-output-chooser
        class="flex-fill bg-white ph3-l pb3-l br2 br--bottom overflow-y-auto-l"
        @cancel="show_output_chooser = false"
        @choose-output="addOutput"
        v-else
      ></pipe-transfer-output-chooser>
    </div>

    <div class="flex flex-column mr4-l css-pipe-transfer-column-summary">
      <div class="f5 pa2 pl0-l pt0-l pb2-l ml3-l relative ttu tc tl-l fw6 bb b--black-10 css-pipe-transfer-column-header">
        <div>Pipe Steps</div>
      </div>
      <div class="flex-fill pt3 pb3 pr3 ml3 overflow-y-auto-l" v-if="has_tasks">
        <task-summary-list
          class="overflow-y-auto-l"
          :tasks="tasks"
        ></task-summary-list>
        <div>
          <btn
            btn-md
            btn-primary
            class="ttu b"
            @click="$emit('open-builder')"
          >
            <i class="material-icons md-18 v-mid" style="margin-left: -4px" v-if="false">edit</i>
            <span class="v-mid">Edit steps</span>
          </btn>
        </div>
      </div>
      <div class="flex-fill pt3 pb3 pr3 ml3 overflow-y-auto tl tc-m" v-else>
        <div class="lh-copy mid-gray f6 mb3 i">There are no steps in this pipe.</div>
        <btn
          btn-md
          btn-primary
          class="ttu b w-100 w-auto-ns"
          @click="$emit('open-builder')"
        >
          Add steps
        </btn>
      </div>
    </div>

  </div>
</template>

<script>
  import {
    TASK_TYPE_INPUT,
    TASK_TYPE_OUTPUT,
    TASK_TYPE_EMAIL_SEND
  } from '../constants/task-type'
  import {
    CONNECTION_TYPE_STDIN,
    CONNECTION_TYPE_STDOUT,
    CONNECTION_TYPE_DROPBOX,
    CONNECTION_TYPE_GOOGLEDRIVE,
    CONNECTION_TYPE_SFTP,
    CONNECTION_TYPE_EMAIL
  } from '../constants/connection-type'
  import Btn from './Btn.vue'
  import PipeTransferInputList from './PipeTransferInputList.vue'
  import PipeTransferInputChooser from './PipeTransferInputChooser.vue'
  import PipeTransferOutputList from './PipeTransferOutputList.vue'
  import PipeTransferOutputChooser from './PipeTransferOutputChooser.vue'
  import TaskSummaryList from './TaskSummaryList.vue'

  export default {
    props: {
      'tasks': {
        type: Array,
        required: true
      },
      'pipe-eid': {
        type: String
      }
    },
    components: {
      Btn,
      PipeTransferInputList,
      PipeTransferInputChooser,
      PipeTransferOutputList,
      PipeTransferOutputChooser,
      TaskSummaryList
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
      input_tasks() {
        return _.filter(this.tasks, { type: TASK_TYPE_INPUT })
      },
      output_tasks() {
        // this is somewhat of a kludge, but it works -- ideally, we'd have a full-fledged
        // email output connection and/or figure out a way to make outputs and inputs
        // a little less strict
        return _.filter(this.tasks, (task) => {
          return task.type == TASK_TYPE_OUTPUT || task.type == TASK_TYPE_EMAIL_SEND
        })
      },
      transform_tasks() {
        return _.reject(this.tasks, (t) => {
          var task_type = _.get(t, 'type')
          return task_type == TASK_TYPE_INPUT || task_type == TASK_TYPE_OUTPUT
        })
      },
      has_input() {
        return this.input_tasks.length > 0
      },
      has_output() {
        return this.output_tasks.length > 0
      },
      has_tasks() {
        return this.tasks.length > 0
      }
    },
    methods: {
      deleteInput(input) {
        var eid = this.pipeEid
        var task_eid = _.get(input, 'eid', '')
        this.$store.dispatch('deletePipeTask', { eid, task_eid })
      },
      deleteOutput(output) {
        var eid = this.pipeEid
        var task_eid = _.get(output, 'eid', '')
        this.$store.dispatch('deletePipeTask', { eid, task_eid })
      },
      addInput(connection) {
        var eid = this.pipeEid
        var ctype = _.get(connection, 'connection_type', '')

        // insert input after any existing inputs
        var input_idx = _.findLastIndex(this.tasks, (t) => { return _.get(t, 'type') == TASK_TYPE_INPUT })

        // if there are no inputs, insert at the beginning of the pipe
        if (input_idx == -1)
          input_idx = 0
           else
          input_idx++

        var conn_identifier = _.get(connection, 'ename', '')
        conn_identifier = conn_identifier.length > 0 ? conn_identifier : _.get(connection, 'eid', '')

        var attrs = {
          index: input_idx,
          metadata: {
            connection_type: ctype
          },
          type: TASK_TYPE_INPUT,
          params: {}
        }

        if (conn_identifier.length > 0)
          _.set(attrs, 'params.connection', conn_identifier)

        if (ctype === CONNECTION_TYPE_STDIN)
          _.set(attrs, 'params.connection', ctype)
           else
          _.set(attrs, 'params.items', []) // all other connections start with empty items array

        // add input task
        this.$store.dispatch('createPipeTask', { eid, attrs })
      },
      addOutput(connection) {
        var eid = this.pipeEid
        var ctype = _.get(connection, 'connection_type', '')

        if (ctype == CONNECTION_TYPE_EMAIL)
        {
          var attrs = {
            type: TASK_TYPE_EMAIL_SEND,
            params: {
              to: ['${email_address}'],
              subject: 'Flex.io Pipe Email Output',
              data: 'attachment'
            }
          }

          // add email send task
          this.$store.dispatch('createPipeTask', { eid, attrs })
          return
        }

        // always insert at the end of the pipe
        var conn_identifier = _.get(connection, 'ename', '')
        conn_identifier = conn_identifier.length > 0 ? conn_identifier : _.get(connection, 'eid', '')

        var attrs = {
          metadata: {
            connection_type: ctype
          },
          type: TASK_TYPE_OUTPUT,
          params: {}
        }

        if (conn_identifier.length > 0)
          _.set(attrs, 'params.connection', conn_identifier)

        if (ctype === CONNECTION_TYPE_STDOUT)
          _.set(attrs, 'params.connection', ctype)

        // add default output location for connections that need this
        if (ctype == CONNECTION_TYPE_DROPBOX || ctype == CONNECTION_TYPE_GOOGLEDRIVE || ctype == CONNECTION_TYPE_SFTP)
          _.set(attrs, 'params.location', '/')

        // add output task
        this.$store.dispatch('createPipeTask', { eid, attrs })
      }
    }
  }
</script>

<style lang="less">
  @import "../stylesheets/variables.less";

  .css-pipe-transfer-column-header {
    background-color: @blue;
    color: #fff;
  }

  .css-pipe-transfer-column-summary {
    background-color: #fff;
  }

  @media @breakpoint-large {
    .css-pipe-transfer-column-header {
      background-color: transparent;
      color: #555; /* .mid-gray */
    }

    .css-pipe-transfer-column-summary {
      width: 26%;
      max-width: 314px;
      background-color: transparent;
    }
  }
</style>
