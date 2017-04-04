<template>
  <div class="relative ml2-m ml3-l" style="max-width: 1600px">
    <div class="flex flex-row relative ml3 ml0-l mr4 mr5-l">
      <div class="flex-none">
        <div
          class="pointer pa2 mr2 mr3-ns br1 white trans-wh tc relative swap-child"
          :class="[ bg_color ]"
          @click="deleteTask"
        >
          <i class="db material-icons f3 child">{{task_icon}}</i>
          <i class="db material-icons f3 other-child hint--bottom-right" aria-label="Remove this step">close</i>
        </div>
      </div>
      <div class="f5 lh-title pt2 mr2 mr3-ns">{{index+1}}.</div>
      <div
        class="bl bw1 b--black-10 pl3 absolute"
        style="top: 46px; bottom: 36px; left: 19px"
        v-show="!show_progress"
      ></div>
      <div class="absolute"
        style="bottom: 5px"
        v-show="!show_progress">
        <div class="pointer moon-gray hover-blue link hint--right" style="margin-left: 8px" :aria-label="insert_tooltip" @click="insertNewTask">
          <i class="db material-icons f3">add_circle</i>
        </div>
      </div>
      <div
        class="flex-fill relative pa3 pt2 bg-white bl br b--white-box"
        :class="content_cls"
      >
        <inline-edit-text
          class="flex-fill f5 lh-title"
          edit-button-tooltip-cls="hint--top-left"
          input-key="name"
          :val="display_name"
          @save="editTaskSingleton">
        </inline-edit-text>
        <inline-edit-text
          class="f7 lh-title gray mt1"
          placeholder="Add a description"
          placeholder-cls="fw6 black-20 hover-black-40"
          edit-button-tooltip-cls="hint--top-left"
          input-key="description"
          :val="description"
          @save="editTaskSingleton">
        </inline-edit-text>
        <div v-if="show_progress" class="mt2 pt2 bt b--black-10">
          <process-progress-item :item="active_subprocess"></process-progress-item>
        </div>
        <div v-else>
          <command-bar2
            ref="commandbar"
            class="mt2 ba b--black-10 bg-white"
            :orig-json="task"
            @change="updateEditTask"
            @cancel="cancelEdit"
            @save="saveEdit"
          ></command-bar2>
          <code-editor
            ref="code"
            class="mb2 bl br bb b--black-10 bg-white max-h5 overflow-y-auto"
            :val="execute_code"
            :lang="execute_lang"
            @change="updateCode"
            v-if="is_task_execute"
          ></code-editor>
          <transition name="slide-fade">
            <div class="flex flex-row mt2" v-show="is_changed">
              <div class="flex-fill">&nbsp;</div>
              <btn btn-sm class="b ttu blue mr2" @click="cancelEdit">Cancel</btn>
              <btn btn-sm class="b ttu white bg-blue" @click="saveEdit">Save Changes</btn>
            </div>
          </transition>
          <pipe-content
            class="mt2 relative"
            :stream-eid="active_stream_eid"
            :task-json="task"
            v-if="active_stream_eid.length > 0"
          ></pipe-content>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { PROCESS_STATUS_RUNNING } from '../constants/process'
  import { TASK_TYPE_EXECUTE } from '../constants/task-type'
  import * as types from '../constants/task-type'
  import parser from '../utils/parser'
  import Btn from './Btn.vue'
  import CodeEditor from './CodeEditor.vue'
  import CommandBar from './CommandBar.vue'
  import InlineEditText from './InlineEditText.vue'
  import PipeContent from './PipeContent.vue'
  import ProcessProgressItem from './ProcessProgressItem.vue'
  import taskItemHelper from './mixins/task-item-helper'

  export default {
    props: ['item', 'index', 'tasks', 'pipe-eid', 'active-process', 'project-connections'],
    mixins: [taskItemHelper],
    components: {
      Btn,
      CodeEditor,
      CommandBar,
      InlineEditText,
      PipeContent,
      ProcessProgressItem
    },
    watch: {
      task: function(val, old_val) {
        this.edit_json = _.assign({}, val)
        this.edit_command = this.getParserCommand()
        this.execute_code = this.getReadableCode()
      }
    },
    data() {
      // somewhat hack-ish, but effective; this will keep the computed
      // 'is_changed' property from being true until we've initialized
      // our data
      this.$nextTick(() => { this.is_inited = true })

      return {
        is_inited: false,
        description: this.getDescription(),
        edit_json: this.getOrigJson(),
        edit_command: this.getParserCommand(),
        execute_code: this.getReadableCode()
      }
    },
    computed: {
      task() {
        return _.get(this, 'item', {})
      },
      eid() {
        return _.get(this, 'task.eid', '')
      },
      task_type() {
        return _.get(this, 'task.type', '')
      },
      is_task_execute() {
        return this.task_type == TASK_TYPE_EXECUTE
      },
      orig_command() {
        var cmd_text = _.defaultTo(parser.toCmdbar(this.task), '')
        var end_idx = cmd_text.indexOf(' code:')
        return (this.is_task_execute && end_idx != -1) ? cmd_text.substring(0, end_idx) : cmd_text
      },
      orig_code() {
        var code = _.get(this, 'task.params.code', '')
        try { return atob(code) } catch(e) { return '' }
      },
      is_changed() {
        if (!this.is_inited)
          return false

        return this.is_task_execute && (this.orig_code != this.execute_code)
          ? true : this.orig_command != this.edit_command
          ? true : false
      },
      insert_tooltip() {
        return 'Insert a new step after step ' + (this.index+1)
      },
      execute_lang() {
        return _.get(this, 'task.params.lang', 'python')
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
      active_stream_eid() {
        var stream = _.head(this.our_inputs)
        return _.get(stream, 'eid', '')
      },
      show_progress() {
        return _.get(this.activeProcess, 'process_status') == PROCESS_STATUS_RUNNING
      },
      content_cls() {
        return this.index == 0
          ? 'pb4a bt br2 br--top' : this.index == _.get(this, 'tasks', []).length - 1
          ? 'mb4a bb br2 br--bottom' : 'pb4a'
      }
    },
    methods: {
      getOrigJson() {
        return _.get(this, 'item', {})
      },
      getDescription() {
        return _.get(this, 'item.description', '')
      },
      getParserCommand() {
        var cmd_text = _.defaultTo(parser.toCmdbar(this.task), '')
        var end_idx = cmd_text.indexOf(' code:')
        return (this.is_task_execute && end_idx != -1) ? cmd_text.substring(0, end_idx) : cmd_text
      },
      getReadableCode() {
        var code = _.get(this, 'item.params.code', '')
        try { return atob(code) } catch(e) { return '' }
      },
      getBase64Code(code) {
        try { return btoa(code) } catch(e) { return '' }
      },
      updateEditTask(cmd_text, cmd_json) {
        this.edit_command = cmd_text
        this.edit_json = _.assign({}, cmd_json)
      },
      updateCode(code) {
        this.execute_code = code
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
        this.edit_json = _.assign({}, this.getOrigJson())
        this.edit_command = this.getParserCommand()
        this.execute_code = this.getReadableCode()

        // reset the command in the command bar
        var cmd_bar = this.$refs['commandbar']
        if (!_.isNil(cmd_bar))
          cmd_bar.setCmdText(this.edit_command)

        // reset the code in the code editor
        var code_editor = this.$refs['code']
        if (!_.isNil(code_editor))
          code_editor.setValue(this.execute_code)
      },
      saveEdit() {
        var edit_attrs = _.pick(this.edit_json, ['metadata', 'type', 'params'])

        // sync up the changes from the code editor if we're on an execute step
        if (this.is_task_execute)
        {
          // this is a hack-ish workaround for the fact that the PHP backend returns
          // empty objects as empty arrays
          var params = _.get(edit_attrs, 'params')
          if (_.isArray(params) && params.length == 0)
            edit_attrs.params = {}

          _.set(edit_attrs, 'params.code', this.getBase64Code(this.execute_code))
        }

        this.editTaskSingleton(edit_attrs)
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

<style lang="less">
  .slide-fade-enter-active,
  .slide-fade-leave-active {
    transition: all .3s ease;
  }

  .slide-fade-enter,
  .slide-fade-leave-to {
    transform: translateY(-20px);
    opacity: 0;
  }

  .pb4a {
    padding-bottom: 3rem;
  }


  .mb4a {
    margin-bottom: 3rem;
  }

</style>
