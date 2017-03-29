<template>
  <div class="relative ml2-m ml3-l" style="max-width: 1500px">
    <div class="flex flex-row mv2 pl3 pr4 pl0-l pr5-l">
      <div class="flex-none">
        <div
          class="pointer pa2 mr3 br1 white trans-wh tc relative swap-child"
          style="margin-top: 1px"
          :class="[ bg_color ]"
          @click="deleteTask"
        >
          <i class="db material-icons f3 child">{{task_icon}}</i>
          <i class="db material-icons f3 other-child hint--bottom-right" aria-label="Remove this step">close</i>
        </div>
      </div>
      <div class="flex-fill relative">
        <div class="bl bw1 b--black-10 pl3 absolute" style="top: 46px; bottom: -5px; left: -37px"></div>
        <div class="flex flex-row">
          <div class="f5 lh-title mr1">{{index+1}}.</div>
          <inline-edit-text
            class="flex-fill f5 lh-title"
            edit-button-tooltip-cls="hint--top-left"
            input-key="name"
            :val="display_name"
            @save="editTaskSingleton">
          </inline-edit-text>
        </div>
        <inline-edit-text
          class="f7 lh-title gray mt1"
          placeholder="Add a description"
          placeholder-cls="fw6 black-20 hover-black-40"
          edit-button-tooltip-cls="hint--top-left"
          input-key="description"
          :val="description"
          @save="editTaskSingleton">
        </inline-edit-text>
        <command-bar2
          ref="commandbar"
          class="mv2"
          :orig-json="task"
          :task-json="task"
          @cancel="cancelEdit"
          @save="saveEdit"
        ></command-bar2>
        <code-editor
          ref="code"
          class="mv2 ba b--black-10"
          :val="execute_code"
          :lang="execute_lang"
          @change="updateCode"
          v-if="is_task_execute"
        ></code-editor>
        <pipe-content
          class="mt2 mb3 relative bg-white"
          style="height: 300px"
          :stream-eid="active_stream_eid"
          :task-json="task"
          v-if="active_stream_eid.length > 0"
        ></pipe-content>
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
  import { TASK_TYPE_EXECUTE } from '../constants/task-type'
  import parser from '../utils/parser'
  import CodeEditor from './CodeEditor.vue'
  import CommandBar2 from './CommandBar2.vue'
  import InlineEditText from './InlineEditText.vue'
  import PipeContent from './PipeContent.vue'
  import taskItemHelper from './mixins/task-item-helper'

  export default {
    props: ['item', 'index', 'pipe-eid', 'active-process', 'project-connections'],
    mixins: [taskItemHelper],
    components: {
      CodeEditor,
      CommandBar2,
      InlineEditText,
      PipeContent
    },
    watch: {
      'item'(val, old_val) {
        this.execute_code = this.getReadableCode()
      }
    },
    data() {
      return {
        description: this.getDescription(),
        command: this.getParserCommand(),
        execute_code: this.getReadableCode()
      }
    },
    computed: {
      task() { return this.item },
      eid() { return _.get(this, 'task.eid', '') },
      task_type() { return _.get(this, 'task.type', '') },
      is_task_execute() { return this.task_type == TASK_TYPE_EXECUTE },
      insert_tooltip() { return 'Insert a new step after step ' + (this.index+1) },

      execute_lang() {
        return _.get(this, 'task.params.lang', 'python')
      },

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

      input_columns()  {
        return this.getOurInputColumns()
      },

      output_columns() {
        return this.getOurOutputColumns()
      },

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
      getReadableCode() {
        var code = _.get(this, 'item.params.code', '')
        try { return atob(code) } catch(e) { return '' }
      },
      getBase64Code(code) {
        try { return btoa(code) } catch(e) { return '' }
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
      cancelEdit() {
        this.execute_code = this.getReadableCode()

        // reset the code in the code editor
        var code_editor = this.$refs['code']
        if (!_.isNil(code_editor))
          code_editor.setValue(this.execute_code)
      },
      saveEdit(attrs) {
        var edit_attrs = _.pick(attrs, ['metadata', 'type', 'params'])

        // sync up the changes from the code editor if we're on an execute step
        if (this.is_task_execute)
          _.set(edit_attrs, 'params.code', this.getBase64Code(this.execute_code))

        this.editTaskSingleton(edit_attrs)
      },
      updateCode(code) {
        this.execute_code = code
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
