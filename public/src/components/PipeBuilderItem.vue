<template>
  <div class="relative center" style="max-width: 1400px">
    <div class="flex flex-row mv2 pl3 pr4 pl0-l pr5-l">
      <div class="flex-none">
        <div
          class="pointer pa2 mr3 br1 white trans-wh tc relative swap-child"
          style="margin-top: 2px"
          :class="[ bg_color ]"
          @click="deleteTask"
        >
          <i class="db material-icons f3 child">{{task_icon}}</i>
          <i class="db material-icons f3 other-child hint--bottom-right" aria-label="Remove this step">close</i>
        </div>
      </div>
      <div class="flex-fill">
        <div class="flex flex-row">
          <div class="flex-fill">
            <div class="v-top dib f5 lh-copy">{{index+1}}.</div>
            <inline-edit-text
              class="v-top dib f5 lh-copy"
              input-key="name"
              :val="display_name"
              @save="editTaskSingleton">
            </inline-edit-text>
            <inline-edit-text
              class="f7 lh-title gray"
              placeholder="Add a description"
              placeholder-cls="fw6 black-20 hover-black-40"
              input-key="description"
              :val="description"
              @save="editTaskSingleton">
            </inline-edit-text>
            <div class="bl bw1 b--black-10 pl3 relative" style="margin: 0 0 -4px -37px; padding: 0 0 4px 37px">
              <div class="mv2">
                <!-- command bar -->
                <div v-if="false">
                  <code-editor
                    class="pa1 ba b--black-10"
                    lang="python"
                    :val="command"
                    :options="{ lineNumbers: false }"
                  ></code-editor>
                </div>
                <command-bar2
                  ref="commandbar"
                  :orig-json="task"
                  :task-json="task"
                  :connections="projectConnections"
                  :input-columns="input_columns"
                  :output-columns="output_columns"
                  :show-plus-button="false"
                  :show-examples="false"
                  :show-cancel-save-buttons="false"
                  @save="saveCommandChanges"
                  v-else
                ></command-bar2>
              </div>
              <pipe-content
                class="mv2 relative bg-white"
                style="height: 300px"
                :stream-eid="active_stream_eid"
                :task-json="task"
                v-if="active_stream_eid.length > 0"
              ></pipe-content>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="flex flex-row pl3 pr4 pl0-l pr5-l">
      <div class="pointer moon-gray hover-blue link hint--right" style="margin-left: 8px" :aria-label="insert_tooltip" @click="insertNewTask">
        <i class="db material-icons f3">add_circle</i>
      </div>
    </div>
  </div>
</template>

<script>
  import * as types from '../constants/task-type'
  import parser from '../utils/parser'
  import CodeEditor from './CodeEditor.vue'
  import CommandBar2 from './CommandBar2.vue'
  import InlineEditText from './InlineEditText.vue'
  import PipeContent from './PipeContent.vue'
  import taskItemHelper from './mixins/task-item-helper'

  export default {
    props: ['pipe-eid', 'item', 'index', 'active-process', 'project-connections'],
    mixins: [taskItemHelper],
    components: {
      CodeEditor,
      CommandBar2,
      InlineEditText,
      PipeContent
    },
    data() {
      return {
        description: this.getDescription(),
        command: this.getParserCommand()
      }
    },
    computed: {
      task() { return this.item },
      eid() { return _.get(this, 'task.eid', '') },
      insert_tooltip() { return 'Insert a new step after step ' + (this.index+1) },

      process_task_id() {
        var process_eid = _.get(this.activeProcess, 'eid', '')
        if (process_eid.length == 0)
          return ''
        return process_eid + '--' + this.eid
      },

      // find the active subprocess by finding this task eid in the subprocess array
      active_subprocess() {
        return _
          .chain(this.activeProcess)
          .get('subprocesses')
          .find((s) => { return _.get(s, 'task.eid') == this.eid })
          .value()
      },

      our_inputs() {
        var inputs = _.get(this.active_subprocess, 'output', [])

        // use the inputs specified in the input task
        if (inputs.length == 0 && this.task_type == types.TASK_TYPE_INPUT)
          return _.get(this.task, 'params.items', [])

        // ...otherwise, use the output array from the active subprocess
        return inputs
      },

      input_columns()  { return this.getOurInputColumns() },
      output_columns() { return this.getOurOutputColumns() },

      active_stream_eid() {
        var stream = _.head(this.our_inputs)
        return _.get(stream, 'eid', '')
      }
    },
    methods: {
      getDescription() {
        return _.get(this, 'item.description', '')
      },
      getParserCommand() {
        return _.defaultTo(parser.toCmdbar(this.task), '')
      },
      getOurInputColumns() {
        var columns = _.get(this.$store, 'state.objects.'+this.process_task_id+'.input_columns', [])

        // NOTE: it's really important to include the '_' on the same line
        // as the 'return', otherwise JS will return without doing anything
        return _
          .chain(columns)
          .sortBy([ function(c) { return c.name } ])
          .reverse()
          .value()
      },
      getOurOutputColumns() {
        var columns = _.get(this.$store, 'state.objects.'+this.process_task_id+'.output_columns', [])

        // NOTE: it's really important to include the '_' on the same line
        // as the 'return', otherwise JS will return without doing anything
        return _
          .chain(columns)
          .sortBy([ function(c) { return c.name } ])
          .reverse()
          .value()
      },
      editTaskSingleton(attrs, input) {
        var eid = this.pipeEid
        var task_eid = this.eid
        var attrs = _.assign({}, this.task, attrs)
        this.$store.dispatch('updatePipeTask', { eid, task_eid, attrs })

        if (!_.isNil(input))
          input.endEdit()
      },
      saveCommandChanges(attrs) {
        this.editTaskSingleton(_.pick(attrs, ['type', 'params']))
      },
      insertNewTask() {
        this.$emit('insert-task', this.index+1)
      },
      deleteTask() {
        var eid = this.pipeEid
        var task_eid = this.eid
        this.$store.dispatch('deletePipeTask', { eid, task_eid })
      }
    }
  }
</script>
