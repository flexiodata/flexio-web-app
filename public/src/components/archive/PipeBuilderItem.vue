<template>
  <div
    :style="style"
    :id="eid"
  >
    <div class="flex flex-row relative hide-child">

      <!-- compensate for the remove step button on right -->
      <div
        class="w2 pv1 tc"
        :class="{ 'mt3a': index == 0 || (index != 0 && isPrompting) }"
      >&nbsp;</div>

      <!-- task icon and step number -->
      <div
        class="flex-none"
        :class="{ 'mt3a': index == 0 || (index != 0 && isPrompting) }"
      >
        <div class="flex flex-row items-center">
          <!-- task icon -->
          <div>
            <div class="mr2 mr3-ns" v-if="show_connection_icon">
              <service-icon
                class="br1"
                style="width: 40px; height: 40px"
                :type="ctype"
              ></service-icon>
            </div>
            <div class="cursor-default pa2 mr2 mr3-ns br1 white tc relative" :class="[ bg_color ]" v-else>
              <i class="db material-icons f3">{{task_icon}}</i>
            </div>
          </div>

          <!-- task number -->
          <div class="f5 lh-title mr2 mr3-ns">{{index+1}}.</div>
        </div>
      </div>

      <!-- lines and insert buttons -->
      <div class="absolute h-100 ml4" v-if="!show_progress && !isPrompting">
        <!-- vertical line -->
        <div
          class="bl bw1 b--black-10 pl3 absolute"
          style="top: 45px; bottom: 35px; left: 19px"
          :class="{ 'mt3a': index == 0 || (index != 0 && isPrompting) }"
        ></div>

        <!-- insert before button -->
        <div
          class="absolute"
          style="top: -12px; left: 8px"
          v-if="index==0 && showInsertBeforeFirstTask"
        >
          <div class="pointer moon-gray hover-blue link hint--right" :aria-label="insert_before_tooltip" @click="insertNewTask(0)">
            <i class="db material-icons f3">add_circle</i>
          </div>
        </div>

        <!-- insert after button -->
        <div class="absolute" style="bottom: 5px; left: 8px">
          <div class="pointer moon-gray hover-blue link hint--right" :aria-label="insert_after_tooltip" @click="insertNewTask()">
            <i class="db material-icons f3">add_circle</i>
          </div>
        </div>
      </div>

      <!-- main content -->
      <div
        class="flex-fill relative ph3a bg-white bl br b--white-box"
        :class="[
          content_cls,
          index == 0 ? 'pt3a' : '',
          index != 0 && isPrompting ? 'pt3a' : '',
          isPrompting && !is_active_prompt_task?'o-40 no-pointer-events':''
        ]"
        :style="content_style"
      >
        <!-- progress status icon -->
        <div class="relative" v-if="show_progress">
          <div
            class="absolute cursor-default"
            style="top: 8px; left: -33px"
          >
            <i
              class="db material-icons"
              style="font-size: 23px"
              :class="status_icon_cls"
            >{{status_icon}}</i>
          </div>
        </div>

        <!-- always task description -->
        <inline-edit-text
          class="db lh-title mid-gray"
          placeholder-cls="black-40 hover-mid-gray"
          edit-button-tooltip-cls="hint--top-left"
          input-key="description"
          static-cls="hover-bg-near-white"
          :placeholder="display_name"
          :val="description"
          :allow-edit="!show_progress && !isPrompting"
          :edit-on="['edit-button']"
          :is-markdown="true"
          :is-block="true"
          @save="editTaskSingleton">
        </inline-edit-text>

        <!-- option 1. show process progress item, or... -->

        <!-- progress bar -->
        <process-progress-item
          class="mt2 pt2 bt b--black-10"
          :item="active_subprocess"
          v-if="show_progress">
        </process-progress-item>

        <!-- option 2. show task prompt item, or... -->

        <task-prompt-item
          class="mt4"
          :item="item"
          :index="index"
          :variables="variables"
          :active-prompt-idx="activePromptIdx"
          :first-prompt-idx="firstPromptIdx"
          :last-prompt-idx="lastPromptIdx"
          :is-active-prompt-task="is_active_prompt_task"
          @prompt-value-change="onPromptValueChange"
          @go-prev-prompt="$emit('go-prev-prompt')"
          @go-next-prompt="emitGoNextPrompt"
          @run-once-with-values="$emit('run-once-with-values')"
          @save-values-and-run="$emit('save-values-and-run')"
          v-else-if="isPrompting && is_prompt"
        ></task-prompt-item>

        <!-- option 3. show normal builder item -->

        <div class="relative min-h2" v-else>
          <!-- error icon -->
          <div
            class="absolute cursor-default"
            style="top: 4px; left: -33px"
            v-if="has_error"
          >
            <i class="db material-icons md-18 dark-red" style="font-size: 23px">error</i>
          </div>

          <!-- collapser -->
          <div
            class="absolute cursor-default"
            style="top: 6px; left: -31px"
            v-else-if="active_stream_eid.length > 0"
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
            v-if="show_command_bar"
          ></command-bar>

          <!-- input chooser -->
          <transition name="slide-fade">
            <pipe-builder-connection-chooser
              class="ph3 pv2 ba bt-0 b--black-10"
              style="margin-top: -1px; box-shadow: 0 1px 4px -2px rgba(0,0,0,0.4)"
              io-type="input"
              @choose-input="onChooseInput"
              v-if="show_input_chooser"
            ></pipe-builder-connection-chooser>
          </transition>

          <!-- output chooser -->
          <transition name="slide-fade">
            <pipe-builder-connection-chooser
              class="ph3 pv2 ba bt-0 b--black-10"
              style="margin-top: -1px; box-shadow: 0 1px 4px -2px rgba(0,0,0,0.4)"
              io-type="output"
              @choose-output="onChooseOutput"
              v-if="show_output_chooser"
            ></pipe-builder-connection-chooser>
          </transition>

          <!-- input file list -->
          <pipe-transfer-input-item
            class="ph3 pv2 ba bt-0 b--black-10 overflow-y-auto"
            style="margin-top: -1px; box-shadow: 0 1px 4px -2px rgba(0,0,0,0.4); max-height: 280px"
            :item="task"
            v-if="is_input_task && !show_input_chooser"
          ></pipe-transfer-input-item>

          <!-- output panel -->
          <pipe-transfer-output-item
            class="ph3 pv2 ba bt-0 b--black-10 overflow-y-auto"
            style="margin-top: -1px; box-shadow: 0 1px 4px -2px rgba(0,0,0,0.4); max-height: 280px"
            :item="task"
            v-if="(is_output_task || is_email_task) && !show_output_chooser"
          ></pipe-transfer-output-item>

          <!-- helper area -->
          <div v-if="show_helper">
            <div class="mt3 mb2 f6 fw6 silver">...or autofill from these common steps:</div>
            <div class="flex flex-column flex-row-ns flex-wrap nl2-l nl-1">
              <div
                class="f6 fw6 ttu br1 ma2-l ma1 pa2 pointer bg-blue white darken-10"
                @click="autofillItemClick(item)"
                v-for="(item, index) in autofill_items"
              >{{item.label}}</div>
            </div>
          </div>

          <!-- code editor -->
          <code-editor
            ref="code"
            class="mb2 bl br bb b--black-10 bg-white max-h5 overflow-y-auto"
            :val="orig_code"
            :lang="code_lang"
            @change="updateCode"
            v-if="is_execute_task"
          ></code-editor>

          <!-- syntax error message and cancel/save buttons -->
          <transition name="slide-fade">
            <div class="flex flex-row items-start mt2" v-if="is_changed && !show_helper">
              <div class="flex-fill mr4">
                <transition name="slide-fade">
                  <div class="f7 dark-red pre overflow-y-hidden overflow-x-auto code" v-if="syntax_msg.length > 0">{{syntax_msg}}</div>
                </transition>
              </div>
              <btn btn-sm class="b ttu blue mr2" @click="cancelEdit">Cancel</btn>
              <btn btn-sm class="b ttu white bg-blue" @click="saveChanges">Save Changes</btn>
            </div>
          </transition>

          <!-- error message -->
          <div class="flex flex-row items-start mt2" v-if="!is_changed && error_msg.length > 0">
            <div class="f7 dark-red pre overflow-y-hidden overflow-x-auto code">{{error_msg}}</div>
          </div>

          <!-- preview -->
          <transition name="slide-fade">
            <pipe-content
              class="mt2 relative"
              :stream-eid="active_stream_eid"
              v-if="show_preview && active_stream_eid.length > 0"
            ></pipe-content>
          </transition>
        </div>
      </div>

      <!-- remove step button -->
      <div
        class="w2 pv1 tc"
        :class="{ 'mt3a': index == 0 || (index != 0 && isPrompting) }"
      >
        <div
          class="dib pointer moon-gray hover-blue tc relative child"
          @click="deleteTask"
          v-show="!show_progress && !isPrompting"
        >
          <i class="db material-icons f3 other-child hint--top-left" aria-label="Remove this step">close</i>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import {
    PROCESS_STATUS_PENDING,
    PROCESS_STATUS_WAITING,
    PROCESS_STATUS_RUNNING,
    PROCESS_STATUS_CANCELLED,
    PROCESS_STATUS_PAUSED,
    PROCESS_STATUS_FAILED,
    PROCESS_STATUS_COMPLETED
  } from '../constants/process'
  import {
    TASK_OP_INPUT,
    TASK_OP_OUTPUT,
    TASK_OP_EXECUTE,
    TASK_OP_COMMENT,
    TASK_OP_EMAIL_SEND
  } from '../constants/task-op'
  import {
    CONNECTION_TYPE_HTTP,
    CONNECTION_TYPE_RSS,
    CONNECTION_TYPE_STDIN,
    CONNECTION_TYPE_EMAIL,
    CONNECTION_TYPE_STDOUT,
    CONNECTION_TYPE_BOX,
    CONNECTION_TYPE_DROPBOX,
    CONNECTION_TYPE_GOOGLEDRIVE,
    CONNECTION_TYPE_SFTP
  } from '../constants/connection-type'
  import autofill_items from '../constants/pipe-autofill'
  import api from '../api'
  import parser from '../utils/parser'
  import Btn from './Btn.vue'
  import ServiceIcon from './ServiceIcon.vue'
  import CodeEditor from './CodeEditor.vue'
  import CommandBar from './CommandBar.vue'
  import InlineEditText from './InlineEditText.vue'
  import PipeContent from './PipeContent.vue'
  import ProcessProgressItem from './ProcessProgressItem.vue'
  import PipeBuilderConnectionChooser from './PipeBuilderConnectionChooser.vue'
  import PipeTransferInputItem from './PipeTransferInputItem.vue'
  import PipeTransferOutputItem from './PipeTransferOutputItem.vue'
  import TaskPromptItem from './TaskPromptItem.vue'
  import TaskItemHelper from './mixins/task-item-helper'

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
      'active-prompt-idx': {
        type: Number,
        default: 0
      },
      'first-prompt-idx': {
        type: Number,
        default: 0
      },
      'last-prompt-idx': {
        type: Number,
        default: 0
      },
      'is-prompting': {
        type: Boolean,
        default: false
      },
      'is-scrolling': {
        type: Boolean,
        default: false
      },
      'active-process': {
        type: Object
      },
      'show-preview': {
        type: Boolean,
        default: true
      },
      'show-insert-before-first-task': {
        type: Boolean,
        default: false
      }
    },
    mixins: [TaskItemHelper],
    components: {
      Btn,
      ServiceIcon,
      CodeEditor,
      CommandBar,
      InlineEditText,
      PipeContent,
      ProcessProgressItem,
      PipeBuilderConnectionChooser,
      PipeTransferInputItem,
      PipeTransferOutputItem,
      TaskPromptItem
    },
    inject: ['pipeEid'],
    watch: {
      task: function(val, old_val) {
        this.edit_json = _.assign({}, val)
        this.edit_cmd = this.getEditCmdFromJson()
        this.edit_code = this.getCodeFromBase64()
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
        show_preview: this.showPreview,
        syntax_msg: '',
        edit_json: this.getOrigJson(),
        edit_cmd: this.getEditCmdFromJson(),
        edit_code: this.getCodeFromBase64(),
        autofill_items: autofill_items
      }
    },
    computed: {
      task() {
        return _.get(this, 'item', {})
      },
      eid() {
        return _.get(this, 'task.eid', '')
      },
      task_op() {
        return _.get(this, 'task.op', '')
      },
      edit_op() {
        return _.get(this.edit_json, 'op', '')
      },
      meta_ctype() {
        return _.get(this, 'task.metadata.connection_type', '')
      },
      is_input_task() {
        return this.task_op == TASK_OP_INPUT
      },
      is_output_task() {
        return this.task_op == TASK_OP_OUTPUT
      },
      is_email_task() {
        return this.task_op == TASK_OP_EMAIL_SEND
      },
      is_execute_task() {
        return this.task_op == TASK_OP_EXECUTE
      },
      is_active_prompt_task() {
        return this.index == this.activePromptIdx
      },
      show_command_bar() {
        return this.task_op != TASK_OP_COMMENT
      },
      show_input_chooser() {
        if (this.edit_op == TASK_OP_INPUT)
        {
          if (this.meta_ctype == CONNECTION_TYPE_HTTP || this.meta_ctype == CONNECTION_TYPE_RSS)
            return false

          if (_.get(this.edit_json, 'params.connection', '') == '')
            return true
        }

        return false
      },
      show_output_chooser() {
        if (this.edit_op == TASK_OP_OUTPUT)
        {
          if (_.get(this.edit_json, 'params.connection', '') == '')
            return true
        }

        return false
      },
      show_helper() {
        if (this.show_input_chooser || this.show_output_chooser)
          return false

        if (this.edit_cmd.length > 0)
          return false

        if (this.edit_op == '')
          return true

        return false
      },
      orig_cmd() {
        var cmd_text = _.defaultTo(parser.toCmdbar(this.task), '')
        var end_idx = cmd_text.indexOf(' code:')
        return (this.is_execute_task && end_idx != -1)
          ? cmd_text.substring(0, end_idx)
          : cmd_text
      },
      orig_code() {
        var code = _.get(this, 'task.params.code', '')
        try { return decodeURIComponent(escape(atob(code))) } catch(e) { return '' }
      },
      is_changed() {
        if (!this.is_inited)
          return false

        if (this.is_execute_task && this.orig_code != this.edit_code)
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
          .get('log')
          .find((s) => { return _.get(s, 'task.eid') == this.eid })
          .value()
      },
      variables() {
        return _.get(this, 'task.variables', [])
      },
      is_prompt() {
        return _.get(this, 'task.is_prompt', false)
      },
      active_stream_eid() {
        return _.get(this.active_subprocess, 'output.stdout.eid', '')
      },
      show_progress() {
        return _.includes([
          PROCESS_STATUS_PENDING,
          PROCESS_STATUS_WAITING,
          PROCESS_STATUS_RUNNING
        ], _.get(this.activeProcess, 'process_status'))
      },
      our_status() {
        return _.get(this.active_subprocess, 'process_status', '')
      },
      show_status_icon() {
        return this.our_status == PROCESS_STATUS_FAILED || this.show_progress
      },
      has_error() {
        return this.our_status == PROCESS_STATUS_FAILED
      },
      error_msg() {
        return _.get(this.active_subprocess, 'process_info.error.message', '')
      },
      status_icon() {
        switch (this.our_status)
        {
          case PROCESS_STATUS_PENDING:   return 'check_circle'
          case PROCESS_STATUS_WAITING:   return ''
          case PROCESS_STATUS_RUNNING:   return 'sync'
          case PROCESS_STATUS_CANCELLED: return 'cancel'
          case PROCESS_STATUS_PAUSED:    return ''
          case PROCESS_STATUS_FAILED:    return 'error'
          case PROCESS_STATUS_COMPLETED: return 'check_circle'
        }

        return ''
      },
      status_icon_cls() {
        switch (this.our_status)
        {
          case PROCESS_STATUS_PENDING:   return 'moon-gray'
          case PROCESS_STATUS_WAITING:   return ''
          case PROCESS_STATUS_RUNNING:   return 'moon-gray spin'
          case PROCESS_STATUS_CANCELLED: return 'dark-red'
          case PROCESS_STATUS_PAUSED:    return ''
          case PROCESS_STATUS_FAILED:    return 'dark-red'
          case PROCESS_STATUS_COMPLETED: return 'dark-green'
        }

        return ''
      },
      content_cls() {
        var task_cnt = _.get(this, 'tasks', []).length
        var is_last = this.index == task_cnt - 1

        if (task_cnt == 1)
          return ['pb4','br2','ba'].join(' ')

        if (this.index == 0)
          return ['pb4','br2','bt','br--top'].join(' ')

        if (is_last)
          return ['pb4','br2','bb','br--bottom'].join(' ')

        return 'pb4'
      },
      style() {
        return ''

        if (this.isPrompting && this.is_active_prompt_task)
          return this.index == 0 ? 'margin-bottom: 1.25rem' : 'margin-top: 1.25rem; margin-bottom: 1.25rem'

        return ''
      },
      content_style() {
        if (this.isPrompting && this.is_active_prompt_task)
         return 'z-index:2; padding: 1.25rem; box-shadow: 0 0 20px rgba(0,0,0,0.2)'

        return ''
      }
    },
    methods: {
      ...mapGetters([
        'getAvailableConnections'
      ]),
      getOrigJson() {
        return _.get(this, 'item', {})
      },
      // defaults to original task json
      getEditCmdFromJson(json) {
        var json = _.isObject(json) ? json : this.getOrigJson()
        var cmd_text = _.defaultTo(parser.toCmdbar(json), '')
        var end_idx = cmd_text.indexOf(' code:')

        return (_.get(json, 'op') == TASK_OP_EXECUTE && end_idx != -1)
          ? cmd_text.substring(0, end_idx)
          : cmd_text
      },
      // defaults to original task json's base64 code
      getCodeFromBase64(base64_code) {
        var code = _.isString(base64_code) ? base64_code : _.get(this, 'item.params.code', '')
        try { return decodeURIComponent(escape(atob(code))) } catch(e) { return e }
      },
      getBase64Code(code) {
        try { return btoa(unescape(encodeURIComponent(code))) } catch(e) { return e }
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
        var lang = _.get(this.edit_json, 'params.lang', '')

        var validate_attrs = [{
          key: 'code',
          value: base64_code,
          type: lang
        }]

        api.validate({ attrs: validate_attrs }).then(response => {
          var result = _.get(response.body, '[0]', {})

          if (_.isFunction(callback))
            callback.call(this, result)
        }, response => {
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
        var edit_attrs = _.pick(edit_json, ['metadata', 'op', 'params'])
        var task_op = _.get(edit_attrs, 'op', '')
        var friendly_task_name = _.capitalize(task_op)

        var command = this.edit_cmd
        var code = this.edit_code

        if (parser.validate(this.edit_cmd) !== true)
        {
          var message = 'There is an error in the command syntax'
          this.showSyntaxError(message, 4000)
          //analytics.track('Updated Step: Command Syntax Error', { command, message })
          return
        }

        // sync up the changes from the code editor if we're on an execute step
        if (task_op == TASK_OP_EXECUTE)
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
              {
                var message = res.message
                this.showSyntaxError(message)
                //analytics.track('Updated Step: '+friendly_task_name+' (Error)', { command, code, message })
              }
               else
              {
                this.editTaskSingleton(edit_attrs)
                //analytics.track('Updated Step: '+friendly_task_name, { command, code })
              }
            })
          }
           else
          {
            this.editTaskSingleton(edit_attrs)
            //analytics.track('Updated Step: '+friendly_task_name, { command, code })
          }

          // we're done
          return
        }

        // create analytics payload for all other task operations
        var analytics_payload = { command, message }

        if (task_op == TASK_OP_INPUT || task_op == TASK_OP_OUTPUT)
        {
          var connection_identifier = _.get(edit_attrs, 'params.connection', '')

          // NOTE: it's really important to include the '_' on the same line
          // as the 'return', otherwise JS will return without doing anything
          var connection = _
            .chain(this.getAvailableConnections())
            .find((c) => {
              if (connection_identifier.length == 0)
                return false

              return _.get(c, 'eid') == connection_identifier ||
                     _.get(c, 'ename') == connection_identifier
            })
            .value()

          if (!_.isNil(connection))
          {
            var ctype = _.get(connection, 'connection_type', '')
            _.set(edit_attrs, 'metadata.connection_type', ctype)
            _.set(analytics_payload, 'connection_type', ctype)
          }
           else
          {
            // add connection type metadata for stdin and stdout
            var ctype = _.get(connection, 'connection_type', connection_identifier)

            // add connection type metadata for web link and rss inputs
            if (ctype.length == 0)
            {
              var items = _.get(edit_attrs, 'params.items', [])

              var is_rss = !_.isNil(_.find(items, (item) => {
                return _.includes(_.get(item, 'path', ''), 'rss://')
              }))

              var is_http = !_.isNil(_.find(items, (item) => {
                return _.includes(_.get(item, 'path', ''), 'http://') || _.includes(_.get(item, 'path', ''), 'https://')
              }))

              if (is_rss)
                ctype = CONNECTION_TYPE_RSS

              if (is_http)
                ctype = CONNECTION_TYPE_HTTP
            }

            _.set(edit_attrs, 'metadata.connection_type', ctype)
            _.set(analytics_payload, 'connection_type', ctype)
          }
        }

        this.editTaskSingleton(edit_attrs)
        //analytics.track('Updated Step: '+friendly_task_name, analytics_payload)
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
      },
      onPromptValueChange(val, variable_set_key) {
        this.$emit('prompt-value-change', val, variable_set_key)
      },
      emitGoNextPrompt(task_eid) {
        this.$emit('go-next-prompt', task_eid)
      },
      autofillItemClick(item) {
        this.edit_json = _.assign({}, _.get(item, 'task_json', {}))
        this.edit_cmd = this.getEditCmdFromJson(this.edit_json)
        this.edit_code = this.getCodeFromBase64(_.get(this.edit_json, 'params.code', ''))
        this.saveChanges()
      },
      onChooseInput(connection) {
        var eid = this.pipeEid
        var task_eid = this.eid
        var ctype = _.get(connection, 'connection_type', '')
        var conn_identifier = _.get(connection, 'ename', '')
        conn_identifier = conn_identifier.length > 0 ? conn_identifier : _.get(connection, 'eid', '')

        var attrs = {
          metadata: {
            connection_type: ctype
          },
          op: TASK_OP_INPUT,
          params: {}
        }

        if (conn_identifier.length > 0)
          _.set(attrs, 'params.connection', conn_identifier)

        if (ctype === CONNECTION_TYPE_STDIN || ctype === CONNECTION_TYPE_EMAIL)
          _.set(attrs, 'params.connection', ctype)
           else
          _.set(attrs, 'params.items', []) // all other connections start with empty items array

        // update input task
        this.$store.dispatch('updatePipeTask', { eid, task_eid, attrs }).then(response => {
          var task = response.body

          var analytics_payload = {
            eid,
            connection_type: ctype,
            createdAt: _.get(task, 'created') // Segment-friendly key value
          }

          //analytics.track('Created Step: Input (via Chooser List)', analytics_payload)
        })
      },
      onChooseOutput(connection) {
        var eid = this.pipeEid
        var task_eid = this.eid
        var ctype = _.get(connection, 'connection_type', '')
        var conn_identifier = _.get(connection, 'ename', '')
        conn_identifier = conn_identifier.length > 0 ? conn_identifier : _.get(connection, 'eid', '')

        // email output is a one-off output connection
        if (ctype == CONNECTION_TYPE_EMAIL)
        {
          var attrs = {
            op: TASK_OP_EMAIL_SEND,
            params: {
              to: ['${email_address}'],
              subject: 'Flex.io Pipe Email Output',
              data: 'attachment'
            }
          }
        }
         else
        {
          var attrs = {
            metadata: {
              connection_type: ctype
            },
            op: TASK_OP_OUTPUT,
            params: {}
          }

          if (conn_identifier.length > 0)
            _.set(attrs, 'params.connection', conn_identifier)

          if (ctype === CONNECTION_TYPE_STDOUT)
            _.set(attrs, 'params.connection', ctype)

          // add default output location for connections that need this
          if (ctype == CONNECTION_TYPE_BOX || ctype == CONNECTION_TYPE_DROPBOX || ctype == CONNECTION_TYPE_GOOGLEDRIVE || ctype == CONNECTION_TYPE_SFTP)
            _.set(attrs, 'params.location', '/')
        }

        // update output task
        this.$store.dispatch('updatePipeTask', { eid, task_eid, attrs }).then(response => {
          var task = response.body

          var analytics_payload = {
            eid,
            connection_type: ctype,
            createdAt: _.get(task, 'created') // Segment-friendly key value
          }

          //analytics.track('Created Step: Output (via Chooser List)', analytics_payload)
        })
      }
    }
  }
</script>

<style lang="less">
  .ph3a {
    padding-left: 1.25rem;
    padding-right: 1.25rem;
  }

  .mt3a {
    margin-top: 1.25rem;
  }

  .pt3a {
    padding-top: 1.25rem;
  }

  .pb4a {
    padding-bottom: 3rem;
  }

  .spin {
    animation: spinner 1.2s linear infinite;
  }

  @keyframes spinner {
    0% { transform: rotate(360deg); }
    100% { transform: rotate(0deg); }
  }
</style>
