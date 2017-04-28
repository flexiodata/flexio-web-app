<template>
  <div class="relative" style="max-width: 1574px">
    <div class="flex flex-row relative ml3 ml0-l mr4 mr5-l">

      <!-- task icon -->
      <div class="flex-none">
        <div :class="[ index==0?'mt2':'' ]" @click="deleteTask">
          <div class="swap-child" v-if="show_connection_icon">
            <connection-icon
              class="mr2 mr3-ns br1 pointer child"
              style="width: 40px; height: 40px"
              :type="ctype"
            ></connection-icon>
            <div class="pointer pa2 mr2 mr3-ns br1 bg-light-silver white tc relative other-child">
              <i class="db material-icons f3 other-child hint--bottom-right" aria-label="Remove this step">close</i>
            </div>
          </div>
          <div class="pointer pa2 mr2 mr3-ns br1 white tc relative swap-child" :class="[ bg_color ]" v-else>
            <i class="db material-icons f3 child">{{task_icon}}</i>
            <i class="db material-icons f3 other-child hint--bottom-right" aria-label="Remove this step">close</i>
          </div>
        </div>
      </div>

      <!-- task number -->
      <div class="f5 lh-title mr2 mr3-ns" :class="[number_cls, index==0?'pt3':'pt2' ]">{{index+1}}.</div>

      <!-- vertical line -->
      <div
        class="bl bw1 b--black-10 pl3 absolute"
        style="top: 46px; bottom: 36px; left: 19px"
        :class="[ index==0?'mt2':'' ]"
        v-show="!show_progress && !this.isPrompting"
      ></div>

      <!-- insert before button -->
      <div
        class="absolute"
        style="top: -24px; left: 8px"
        v-show="!show_progress"
        v-if="index==0 && !show_progress && !this.isPrompting && false"
      >
        <div class="pointer moon-gray hover-blue link hint--right" :aria-label="insert_before_tooltip" @click="insertNewTask(0)">
          <i class="db material-icons f3">add_circle</i>
        </div>
      </div>

      <!-- insert after button -->
      <div
        class="absolute"
        style="bottom: 5px; left: 8px"
        v-show="!show_progress && !this.isPrompting">
        <div class="pointer moon-gray hover-blue link hint--right" :aria-label="insert_after_tooltip" @click="insertNewTask()">
          <i class="db material-icons f3">add_circle</i>
        </div>
      </div>

      <!-- main content -->
      <div
        class="flex-fill relative ph3a bg-white bl br b--white-box"
        :class="[ content_cls, index==0?'pt3':'pt2' ]"
      >
        <!-- 1. show progress -->

        <div v-if="show_progress">
          <!-- static task name -->
          <div class="f5 lh-title">{{display_name}}</div>

          <!-- static task description -->
          <div class="f7 lh-title gray mt1" v-if="description.length > 0">{{description}}</div>

          <!-- process progress item -->
          <process-progress-item
            class="mt2 pt2 bt b--black-10"
            :item="active_subprocess">
          </process-progress-item>
        </div>

        <!-- 2. show prompt/configure -->

        <div v-else-if="isPrompting === true">
          <!-- static task name -->
          <div class="f5 lh-title" v-if="variables.length == 0">{{display_name}}</div>

          <!-- static task description -->
          <div class="f7 lh-title gray mt1" v-if="variables.length == 0">{{description}}</div>

          <!-- task configure item -->
          <task-configure-item
            :item="item"
            :variables="variables"
            v-if="variables.length > 0"
          ></task-configure-item>
        </div>

        <!-- 3. show normal builder item -->

        <div v-else>
          <!-- task name -->
          <inline-edit-text
            class="f5 lh-title"
            edit-button-tooltip-cls="hint--top-left"
            input-key="name"
            :val="display_name"
            @save="editTaskSingleton">
          </inline-edit-text>

          <!-- task description -->
          <inline-edit-text
            class="f7 lh-title gray mt1"
            placeholder="Add a description"
            placeholder-cls="fw6 black-20 hover-black-40"
            edit-button-tooltip-cls="hint--top-left"
            input-key="description"
            :val="description"
            @save="editTaskSingleton">
          </inline-edit-text>

          <div class="relative">
            <!-- collapser -->
            <div
              class="absolute cursor-default"
              style="top: 6px; left: -31px"
              v-if="active_stream_eid.length > 0"
            >
              <div
                class="pointer moon-gray bg-white ba b--white-box br-100 hover-blue hover-b--blue hint--top-right"
                :aria-label="collapse_tooltip"
                @click="togglePreview"
              >
                <i class="db material-icons md-18 trans-t" :class="{ 'rotate-90': show_preview }">chevron_right</i>
              </div>
            </div>

            <!-- command bar -->
            <command-bar
              ref="commandbar"
              class="mt2 pa1 ba b--black-10 bg-white"
              :val="orig_cmd"
              :orig-json="task"
              :is-scrolling="isScrolling"
              :active-process="activeProcess"
              @change="updateCmd"
              @revert="cancelEdit"
              @save="saveChanges"
            ></command-bar>

            <!-- code editor -->
            <code-editor
              ref="code"
              class="mb2 bl br bb b--black-10 bg-white max-h5 overflow-y-auto"
              :val="orig_code"
              :lang="code_lang"
              @change="updateCode"
              v-if="is_task_execute"
            ></code-editor>

            <!-- error message and cancel/save buttons -->
            <transition name="slide-fade">
              <div class="flex flex-row items-start mt2" v-show="is_changed">
                <div class="flex-fill mr4">
                  <transition name="slide-fade">
                    <div class="f7 dark-red pre overflow-y-hidden overflow-x-auto code" v-if="syntax_msg.length > 0">{{syntax_msg}}</div>
                  </transition>
                </div>
                <btn btn-sm class="b ttu blue mr2" @click="cancelEdit">Cancel</btn>
                <btn btn-sm class="b ttu white bg-blue" @click="saveChanges">Save Changes</btn>
              </div>
            </transition>

            <!-- preview -->
            <transition name="slide-fade">
              <pipe-content
                class="mt2 relative"
                :stream-eid="active_stream_eid"
                :task-json="task"
                v-if="show_preview && active_stream_eid.length > 0"
              ></pipe-content>
            </transition>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { PROCESS_STATUS_RUNNING } from '../constants/process'
  import { TASK_TYPE_INPUT, TASK_TYPE_OUTPUT, TASK_TYPE_EXECUTE } from '../constants/task-type'
  import { mapGetters } from 'vuex'
  import api from '../api'
  import parser from '../utils/parser'
  import Btn from './Btn.vue'
  import ConnectionIcon from './ConnectionIcon.vue'
  import CodeEditor from './CodeEditor.vue'
  import CommandBar from './CommandBar.vue'
  import InlineEditText from './InlineEditText.vue'
  import PipeContent from './PipeContent.vue'
  import ProcessProgressItem from './ProcessProgressItem.vue'
  import TaskConfigureItem from './TaskConfigureItem.vue'
  import taskItemHelper from './mixins/task-item-helper'

  export default {
    props: {
      'item': {
        type: Object,
        required: true
      },
      'index': {
        type: Number,
        required: true
      },
      'tasks': {
        type: Array,
        required: true
      },
      'is-prompting': {
        type: Boolean,
        default: false
      },
      'active-process': {
        type: Object
      },
      'is-scrolling': {
        type: Boolean,
        default: false
      },
      'show-preview': {
        type: Boolean,
        default: true
      },
    },
    mixins: [taskItemHelper],
    components: {
      Btn,
      ConnectionIcon,
      CodeEditor,
      CommandBar,
      InlineEditText,
      PipeContent,
      ProcessProgressItem,
      TaskConfigureItem
    },
    inject: ['pipeEid'],
    watch: {
      task: function(val, old_val) {
        this.edit_json = _.assign({}, val)
        this.edit_cmd = this.getOrigCmd()
        this.edit_code = this.getOrigCode()
      },
      showPreview: function(val, old_val) {
        this.show_preview = val
      }
    },
    data() {
      // somewhat hack-ish, but effective; this will keep the computed
      // 'is_changed' property from being true until we've initialized
      // our data
      this.$nextTick(() => { this.is_inited = true })

      return {
        is_inited: false,
        description: _.get(this, 'item.description', ''),
        variables: this.getVariables(),
        show_preview: this.showPreview,
        syntax_msg: '',
        edit_json: this.getOrigJson(),
        edit_cmd: this.getOrigCmd(),
        edit_code: this.getOrigCode()
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
      orig_cmd() {
        var cmd_text = _.defaultTo(parser.toCmdbar(this.task), '')
        var end_idx = cmd_text.indexOf(' code:')
        return (this.is_task_execute && end_idx != -1)
          ? cmd_text.substring(0, end_idx)
          : cmd_text
      },
      orig_code() {
        var code = _.get(this, 'task.params.code', '')
        try { return atob(code) } catch(e) { return '' }
      },
      is_changed() {
        if (!this.is_inited)
          return false

        if (this.is_task_execute && this.orig_code != this.edit_code)
          return true

        return this.orig_cmd != this.edit_cmd ? true : false
      },
      insert_before_tooltip() {
        return 'Insert a new step before step ' + (this.index+1)
      },
      insert_after_tooltip() {
        return 'Insert a new step after step ' + (this.index+1)
      },
      collapse_tooltip() {
        return this.show_preview ? 'Hide preview (Ctrl+Click to hide all)' : 'Show preview (Ctrl+Click to show all)'
      },
      code_lang() {
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
        if (inputs.length == 0 && this.task_type == TASK_TYPE_INPUT)
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
      number_cls() {
        // compensate for content top border
        return this.index == 0 ? 'bt b--transparent' : ''
      },
      content_cls() {
        var task_cnt = _.get(this, 'tasks', []).length
        var is_last = this.index == task_cnt - 1

        if (task_cnt == 1)
          return ['pb4a','br2','ba'].join(' ')

        if (this.index == 0)
          return ['pb4a','br2','bt','br--top'].join(' ')

        if (is_last)
          return ['pb4a','br2','bb','br--bottom'].join(' ')

        return 'pb4a'
      }
    },
    methods: {
      ...mapGetters([
        'getAllConnections'
      ]),
      getOrigJson() {
        return _.get(this, 'item', {})
      },
      getOrigCmd() {
        var cmd_text = _.defaultTo(parser.toCmdbar(this.getOrigJson()), '')
        var end_idx = cmd_text.indexOf(' code:')

        return (_.get(this, 'item.type') == TASK_TYPE_EXECUTE && end_idx != -1)
          ? cmd_text.substring(0, end_idx)
          : cmd_text
      },
      getOrigCode() {
        var code = _.get(this, 'item.params.code', '')
        try { return atob(code) } catch(e) { return '' }
      },
      getBase64Code(code) {
        try { return btoa(code) } catch(e) { return '' }
      },
      getVariables() {
        return _.get(this, 'item.variables', [])
      },
      updateCmd(cmd, json) {
        this.edit_cmd = cmd
        this.edit_json = _.assign({}, json)
      },
      updateCode(code) {
        this.edit_code = code
      },
      validateCode(code, callback) {
        var base64_code = this.getBase64Code(code)

        var validate_attrs = [{
          key: 'code',
          value: base64_code,
          type: 'python'
        }]

        api.validate({ attrs: validate_attrs }).then((response) => {
          var result = _.get(response.body, '[0]', {})

          if (_.isFunction(callback))
            callback.call(this, result)
        }, (response) => {
          // error callback
        })
      },
      editTaskSingleton(attrs, input) {
        var eid = this.pipeEid
        var task_eid = this.eid
        var attrs = _.assign({}, this.task, attrs)
        this.$store.dispatch('updatePipeTask', { eid, task_eid, attrs }).then(response => {
          this.clearSyntaxError()
        })

        if (!_.isNil(input))
          input.endEdit()
      },
      cancelEdit() {
        // reset the edit json
        this.edit_json = _.assign({}, this.getOrigJson())

        // reset the command in the command bar
        var cmd_bar = this.$refs['commandbar']
        if (!_.isNil(cmd_bar))
          cmd_bar.reset()

        // reset the code in the code editor
        var code_editor = this.$refs['code']
        if (!_.isNil(code_editor))
          code_editor.reset()

        // remove all syntax errors
        this.clearSyntaxError()
      },
      saveChanges() {
        var edit_json = _.cloneDeep(this.edit_json)
        var edit_attrs = _.pick(edit_json, ['metadata', 'type', 'params'])
        var task_type = _.get(this, 'item.type')

        if (parser.validate(this.edit_cmd) !== true)
        {
          this.showSyntaxError('There is an error in the command syntax', 4000)
          return
        }

        // sync up the changes from the code editor if we're on an execute step
        if (task_type == TASK_TYPE_EXECUTE)
        {
          // this is a hack-ish workaround for the fact that the PHP backend returns
          // empty objects as empty arrays
          var params = _.get(edit_attrs, 'params')
          if (_.isArray(params) && params.length == 0)
            edit_attrs.params = {}

          _.set(edit_attrs, 'params.code', this.getBase64Code(this.edit_code))

          // only validate python right now
          if (this.code_lang == 'python')
          {
            this.validateCode(this.edit_code, (res) => {
              if (res.valid === false)
                this.showSyntaxError(res.message)
                 else
                this.editTaskSingleton(edit_attrs)
            })
          }
           else
          {
            this.editTaskSingleton(edit_attrs)
          }

          // we're done
          return
        }

        if (task_type == TASK_TYPE_INPUT || task_type == TASK_TYPE_OUTPUT)
        {
          var connection_identifier = _.get(edit_attrs, 'params.connection', '')

          // NOTE: it's really important to include the '_' on the same line
          // as the 'return', otherwise JS will return without doing anything
          var connection = _
            .chain(this.getAllConnections())
            .find((c) => {
              if (connection_identifier.length == 0)
                return false

              return _.get(c, 'eid') == connection_identifier ||
                     _.get(c, 'ename') == connection_identifier
            })
            .value()

          if (!_.isNil(connection))
            _.set(edit_attrs, 'metadata.connection_type', _.get(connection, 'connection_type', ''))
        }

        this.editTaskSingleton(edit_attrs)
      },
      showSyntaxError(msg, hide_timeout) {
        this.syntax_msg = msg

        if (_.isNumber(hide_timeout))
          setTimeout(() => { this.syntax_msg = '' }, hide_timeout)
      },
      clearSyntaxError() {
        this.syntax_msg = ''
      },
      insertNewTask(insert_idx) {
        var idx = _.defaultTo(insert_idx, this.index+1)
        this.$emit('insert-task', idx)
      },
      deleteTask() {
        var eid = this.pipeEid
        var task_eid = this.eid
        this.$store.dispatch('deletePipeTask', { eid, task_eid })
      },
      togglePreview(evt) {
        this.show_preview = !this.show_preview
        this.$emit('toggle-preview', this.show_preview, evt.ctrlKey /* toggle all */)
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

  .ph3a {
    padding-left: 1.25rem;
    padding-right: 1.25rem;
  }

  .pb4a {
    padding-bottom: 3rem;
  }
</style>
