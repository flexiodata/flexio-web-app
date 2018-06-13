<template>
  <div
    class="bg-nearer-white ph4 overflow-y-scroll relative"
    style="padding-bottom: 8rem"
    :id="doc_id"
  >
    <div
      class="h-100 flex flex-row items-center justify-center"
      v-if="is_fetching"
    >
      <Spinner size="large" message="Loading..." />
    </div>

    <!-- use `z-7` to ensure the title z-index is greater than the CodeMirror scrollbar -->
    <div
      class="mt4 mb2 nl4 nr4 relative z-7 bg-nearer-white sticky"
      v-if="is_fetched"
    >
      <div class="ph4">
        <div
          class="flex flex-row items-center center"
          style="max-width: 1440px"
        >
          <h1 class="flex-fill mv0 pv3 fw6 mid-gray">{{title}}</h1>
          <div class="flex-none flex flex-row items-center pl2">
            <transition name="el-zoom-in-top">
              <div class="flex flex-row pl3" v-if="is_changed">
                <el-button
                  size="medium"
                  class="ttu b"
                  :disabled="!is_changed"
                  @click="cancelChanges"
                >
                  Cancel
                </el-button>
                <el-button
                  size="medium"
                  type="primary"
                  class="ttu b"
                  :disabled="!is_changed || has_errors"
                  @click="saveChanges"
                >
                  Save
                </el-button>
              </div>
            </transition>
          </div>
        </div>
      </div>
    </div>

    <div
      class="center"
      style="max-width: 1440px"
      v-if="is_fetched"
    >
      <el-tabs
        class="el-tabs--allow-overflow"
        v-model="active_tab_name"
      >
        <el-tab-pane name="properties" label="Properties">
          <div class="mv4 pa4 pt3 bg-white br2 css-white-box">
            <!-- title bar -->
            <div class="flex flex-row items-center pt1 pb3">
              <h3 class="flex-fill mv0 fw6 f4 mid-gray">Properties</h3>
              <el-button
                class="ttu b invisible"
                size="small"
              >
                Spacer
              </el-button>
            </div>
            <!-- content -->
            <PipeDocumentForm ref="pipe-document-form" />
          </div>
        </el-tab-pane>

        <el-tab-pane name="configure" :label="'Build & Test'">
          <div class="mv4 pa4 pt3 bg-white br2 css-white-box">
            <!-- title bar -->
            <div class="flex flex-row items-center pt1 pb3">
              <h3 class="flex-fill mv0 mr3 fw6 f4 mid-gray">Configuration</h3>
              <el-select
                class="tr"
                size="small"
                style="width: 10rem"
                :disabled="has_errors"
                v-model="editor"
              >
                <el-option
                  :label="option.label"
                  :value="option.value"
                  :key="option.value"
                  v-for="option in editor_options"
                />
              </el-select>
            </div>

            <!-- content -->
            <div v-if="editor == 'sdk-js'">
              <CodeEditor
                class="bg-white ba b--black-10 overflow-y-auto"
                lang="javascript"
                :options="{ minRows: 12, maxRows: 30 }"
                v-model="edit_code"
              />
              <transition name="el-zoom-in-top">
                <div class="f8 dark-red pre overflow-y-hidden overflow-x-auto code mt2" v-if="syntax_error.length > 0">Syntax error: {{syntax_error}}</div>
              </transition>
            </div>
            <div v-else-if="editor == 'builder'">
              <PipeBuilderList
                class="mv3"
                :container-id="doc_id"
                v-model="edit_task_list"
              />
            </div>
            <div v-else-if="editor == 'json'">
              <CodeEditor
                class="bg-white ba b--black-10 overflow-y-auto"
                lang="javascript"
                :options="{ minRows: 12, maxRows: 30 }"
                v-model="edit_json"
              />
              <transition name="el-zoom-in-top">
                <div class="f8 dark-red pre overflow-y-hidden overflow-x-auto code mt1" v-if="json_parse_error.length > 0">Parse error: {{json_parse_error}}</div>
              </transition>
            </div>
            <div v-else-if="editor == 'yaml'">
              <CodeEditor
                class="bg-white ba b--black-10 overflow-y-auto"
                lang="yaml"
                :options="{ minRows: 12, maxRows: 30 }"
                v-model="edit_yaml"
              />
              <transition name="el-zoom-in-top">
                <div class="f8 dark-red pre overflow-y-hidden overflow-x-auto code mt1" v-if="yaml_parse_error.length > 0">Parse error: {{yaml_parse_error}}</div>
              </transition>
            </div>
          </div>

          <div class="mv4 pa4 pt3 bg-white br2 css-white-box">
            <!-- title bar -->
            <div class="flex flex-row items-center pt1 pb3">
              <h3 class="flex-fill mv0 mr3 fw6 f4 mid-gray">Output</h3>
              <el-button
                class="ttu b"
                style="min-width: 5rem"
                type="primary"
                size="small"
                :disabled="has_errors"
                @click.stop="runPipe"
              >
                Run
              </el-button>
            </div>

            <!-- content -->
            <div
              class="bg-white ba b--black-10 flex flex-column justify-center"
              style="height: 300px"
              v-if="is_process_running"
            >
              <Spinner size="large" message="Running pipe..." />
            </div>
            <div
              v-else-if="has_run_once && last_stream_eid.length > 0 && !is_process_failed"
            >
              <PipeContent
                :height="300"
                :stream-eid="last_stream_eid"
              />
            </div>
            <div
              v-else-if="has_run_once && is_superuser && is_process_failed"
            >
              <CodeEditor
                class="bg-white ba b--black-10 overflow-y-auto"
                lang="application/json"
                :options="{
                  minRows: 12,
                  maxRows: 24,
                  lineNumbers: false,
                  readOnly: true
                }"
                v-model="active_process_info_str"
              />
            </div>
            <div
              v-else-if="!has_run_once"
            >
              <div
                class="bg-white ba b--black-10 pa3 f6"
                style="height: 300px"
              >
                <em>Configure your pipe in the configuration panel, then click the 'Run' button above to see a preview of the pipe's output.</em>
              </div>
            </div>
            <div
              v-else
            >
              <div
                class="bg-white ba b--black-10 pa3"
                style="height: 300px"
              >
              </div>
            </div>
          </div>
        </el-tab-pane>

        <el-tab-pane name="history" label="History">
          <div class="mv4 pa4 pt3 bg-white br2 css-white-box">
            <ProcessList />
          </div>
        </el-tab-pane>
      </el-tabs>
    </div>

    <!-- pipe schedule dialog -->
    <el-dialog
      custom-class="el-dialog--no-header el-dialog--no-footer"
      width="42rem"
      top="8vh"
      :modal-append-to-body="false"
      :visible.sync="show_pipe_schedule_dialog"
    >
      <PipeSchedulePanel
        :pipe="edit_pipe"
        @close="show_pipe_schedule_dialog = false"
        @cancel="show_pipe_schedule_dialog = false"
        @submit="updatePipeSchedule"
      />
    </el-dialog>

    <!-- pipe deploy dialog -->
    <el-dialog
      custom-class="el-dialog--no-header el-dialog--no-footer"
      width="56rem"
      top="8vh"
      :modal-append-to-body="false"
      :visible.sync="show_pipe_deploy_dialog"
    >
      <PipeDeployPanel
        :pipe="edit_pipe"
        @close="show_pipe_deploy_dialog = false"
      />
    </el-dialog>

  </div>
</template>

<script>
  import stickybits from 'stickybits'
  import yaml from 'js-yaml'
  import Flexio from 'flexio-sdk-js'
  import { mapState, mapGetters } from 'vuex'
  import {
    PROCESS_STATUS_RUNNING,
    PROCESS_STATUS_FAILED,
    PROCESS_STATUS_COMPLETED,
    PROCESS_MODE_BUILD
  } from '../constants/process'

  import Spinner from 'vue-simple-spinner'
  import CodeEditor from './CodeEditor.vue'
  import PipeBuilderList from './PipeBuilderList.vue'
  import PipeDocumentForm from './PipeDocumentForm.vue'
  import PipeSchedulePanel from './PipeSchedulePanel.vue'
  import PipeDeployPanel from './PipeDeployPanel.vue'
  import PipeContent from './PipeContent.vue'
  import ProcessList from './ProcessList.vue'

  const PIPEDOC_VIEW_PROPERTIES = 'properties'
  const PIPEDOC_VIEW_CONFIGURE  = 'configure'
  const PIPEDOC_VIEW_HISTORY    = 'history'

  const PIPEDOC_EDITOR_SDK_JS  = 'sdk-js'
  const PIPEDOC_EDITOR_BUILDER = 'builder'
  const PIPEDOC_EDITOR_JSON    = 'json'
  const PIPEDOC_EDITOR_YAML    = 'yaml'

  // TODO: remove 'omitDeep' once we get rid of task eids
  const omitDeep = (collection, excludeKeys) => {
    function omitFn(val) {
      if (val && typeof val === 'object') {
        excludeKeys.forEach((key) => {
          delete val[key]
        })
      }
    }

    return _.cloneDeepWith(collection, omitFn)
  }

  export default {
    components: {
      Spinner,
      CodeEditor,
      PipeBuilderList,
      PipeDocumentForm,
      PipeSchedulePanel,
      PipeDeployPanel,
      PipeContent,
      ProcessList
    },
    watch: {
      eid: {
        handler: 'loadPipe',
        immediate: true
      },
      active_tab_name: {
        handler: 'onTabChange',
        immediate: true
      },
      editor: {
        handler: 'onEditorChange',
        immediate: true
      },
      is_fetched: {
        handler: 'initSticky',
        immediate: true
      },
      is_process_running(val, old_val) {
        if (val === false && old_val === true) {
          this.fetchProcessLog()
        }
      },
      active_process(val, old_val) {
        if (_.get(val, 'eid', '') != _.get(old_val, 'eid', '')) {
          this.fetchProcessLog()
        }
      }
    },
    data() {
      return {
        active_tab_name: _.get(this.$route, 'params.view', PIPEDOC_VIEW_PROPERTIES),
        editor: _.get(this.$route, 'query.editor', PIPEDOC_EDITOR_SDK_JS),
        editor_options: [
          { value: PIPEDOC_EDITOR_SDK_JS,  label: 'Javascript SDK' },
          { value: PIPEDOC_EDITOR_BUILDER, label: 'Visual Builder' },
          { value: PIPEDOC_EDITOR_JSON,    label: 'JSON'           }/*,
          { value: PIPEDOC_EDITOR_YAML,    label: 'YAML'           }*/
        ],
        has_run_once: false,
        processes_fetched: false,
        show_pipe_schedule_dialog: false,
        show_pipe_deploy_dialog: false,
        json_parse_error: '',
        yaml_parse_error: ''
      }
    },
    computed: {
      ...mapState({
        orig_pipe: state => state.pipe.orig_pipe,
        edit_pipe: state => state.pipe.edit_pipe,
        edit_keys: state => state.pipe.edit_keys,
        syntax_error: state => state.pipe.syntax_error,
        is_fetching: state => state.pipe.fetching,
        is_fetched: state => state.pipe.fetched
      }),
      eid() {
        return _.get(this.$route, 'params.eid', undefined)
      },
      doc_id() {
        return 'pipe-doc-' + this.eid
      },
      store_pipe() {
        return this.getStorePipe()
      },
      edit_code: {
        get() {
          return this.$store.state.pipe.edit_code
        },
        set(value) {
          this.$store.commit('pipe/UPDATE_CODE', value)
        }
      },
      edit_task_list: {
        get() {
          var task = _.get(this.edit_pipe, 'task', { op: 'sequence', items: [] })
          return task
        },
        set(value) {
            debugger
          try {
            var task = _.cloneDeep(value)
            var pipe = _.cloneDeep(this.edit_pipe)
            _.assign(pipe, { task })
            this.$store.commit('pipe/UPDATE_EDIT_PIPE', pipe)
            // TODO: add clear error handling
          }
          catch(e)
          {
            // TODO: add error handling
          }
        }
      },
      edit_json: {
        get() {
          var task = _.get(this.edit_pipe, 'task', { op: 'sequence', items: [] })

          // TODO: remove 'omitDeep' once we get rid of task eids
          task = omitDeep(task, ['eid'])

          // stringify JSON; indent 2 spaces
          var task_str = JSON.stringify(task, null, 2)

          return task_str
        },
        set(value) {
          try {
            var task = JSON.parse(value)
            var pipe = _.cloneDeep(this.edit_pipe)
            _.assign(pipe, { task })
            this.$store.commit('pipe/UPDATE_EDIT_PIPE', pipe)
            this.json_parse_error = ''
          }
          catch(e)
          {
            this.json_parse_error = e.message
          }
        }
      },
      edit_yaml: {
        get() {
          var task = _.get(this.edit_pipe, 'task', { op: 'sequence', items: [] })

          // TODO: remove 'omitDeep' once we get rid of task eids
          task = omitDeep(task, ['eid'])

          return yaml.safeDump(task)
        },
        set(value) {
          try {
            var task = yaml.safeLoad(value)
            var pipe = _.cloneDeep(this.edit_pipe)
            _.assign(pipe, { task })
            this.$store.commit('pipe/UPDATE_EDIT_PIPE', pipe)
            this.yaml_parse_error = ''
          }
          catch(e)
          {
            this.yaml_parse_error = e.message
          }
        }
      },
      title() {
        return _.get(this.orig_pipe, 'name', '')
      },
      is_code_changed() {
        return this.isCodeChanged()
      },
      is_changed() {
        return this.isChanged()
      },
      has_errors() {
        return this.syntax_error.length > 0 || this.json_parse_error.length > 0 || this.yaml_parse_error.length > 0
      },

      // -- all of the below computed values pertain to getting the preview --

      is_superuser() {
        // limit to @flex.io users for now
        var user_email = _.get(this.getActiveUser(), 'email', '')
        return _.includes(user_email, '@flex.io')
      },
      active_process() {
        return _.last(this.getActiveDocumentProcesses())
      },
      active_process_status() {
        return _.get(this.active_process, 'process_status', '')
      },
      active_process_info() {
        return _.get(this.active_process, 'process_info', {})
      },
      active_process_info_str() {
        return JSON.stringify(this.active_process_info, null, 2)
      },
      is_process_running() {
        return this.active_process_status == PROCESS_STATUS_RUNNING
      },
      is_process_failed() {
        return this.active_process_status == PROCESS_STATUS_FAILED
      },
      last_process_log() {
        var log = _.get(this.active_process, 'log', [])
        return _.last(log)
      },
      last_stream_eid() {
        return _.get(this.last_process_log, 'output.stdout.eid', '')
      }
    },
    methods: {
      ...mapGetters('pipe', [
        'getStorePipe',
        'isCodeChanged',
        'isChanged'
      ]),
      ...mapGetters([
        'getActiveDocumentProcesses',
        'getActiveUser'
      ]),
      onTabChange(val) {
        if (!this.processes_fetched && val == PIPEDOC_VIEW_HISTORY) {
          this.$store.dispatch('fetchProcesses', { parent_eid: this.eid }).then(response => {
            if (response.ok) {
              this.processes_fetched = true
            } else {
              // TODO: add error handling
            }
          })
        }

        this.updateRoute()
      },
      onEditorChange(val) {
        this.updateRoute()
      },
      updateRoute() {
        // update the route
        var new_route = _.pick(this.$route, ['name', 'meta', 'params', 'path'])
        var view = this.active_tab_name
        var editor = this.editor
        _.set(new_route, 'params.view', view)
        _.set(new_route, 'query', { editor })
        this.$router.replace(new_route)
      },
      loadPipe() {
        this.$store.commit('pipe/FETCHING_PIPE', true)

        this.$store.dispatch('fetchPipe', { eid: this.eid }).then(response => {
          if (response.ok) {
            var pipe = response.data
            this.$store.commit('pipe/INIT_PIPE', pipe)
            this.$store.commit('pipe/FETCHING_PIPE', false)
          } else {
            this.$store.commit('pipe/FETCHING_PIPE', false)
          }
        })
      },
      runPipe() {
        this.$store.track('Ran Pipe', {
          title: this.title,
          code: this.edit_code
        })

        var attrs = _.pick(this.edit_pipe, ['task'])

        _.assign(attrs, {
          parent_eid: this.eid,
          process_mode: PROCESS_MODE_BUILD,
          run: true // this will automatically run the process and start polling the process
        })

        this.$store.dispatch('createProcess', { attrs }).then(response => {
          // we've manually run the pipe once so we can now show an output result
          this.has_run_once = true

          if (response.ok) {
            this.$nextTick(() => { this.is_running = false })
          }
        })
      },
      updatePipeSchedule(attrs) {
        attrs = _.pick(attrs, ['schedule', 'schedule_status'])

        var pipe = _.cloneDeep(this.edit_pipe)
        _.assign(pipe, attrs)
        this.$store.commit('pipe/UPDATE_EDIT_PIPE', pipe)

        this.show_pipe_schedule_dialog = false
      },
      cancelChanges() {
        this.$store.commit('pipe/INIT_PIPE', this.store_pipe)
      },
      saveChanges() {
        var doc_form = this.$refs['pipe-document-form']
        doc_form.validate((valid) => {
          if (!valid)
            return

          var eid = this.eid
          var attrs = _.pick(this.edit_pipe, this.edit_keys)

          // don't POST null values
          attrs = _.omitBy(attrs, (val, key) => { return _.isNil(val) })

          return this.$store.dispatch('updatePipe', { eid, attrs }).then(response => {
            if (response.ok) {
              this.$message({
                message: 'The pipe was updated successfully.',
                type: 'success'
              })

              this.$store.commit('pipe/INIT_PIPE', response.body)
            } else {
              this.$message({
                message: 'There was a problem updating the pipe.',
                type: 'error'
              })
            }
          })
        })
      },
      fetchProcessLog() {
        if (!this.has_run_once) {
          return
        }

        var eid = _.get(this.active_process, 'eid', '')
        if (eid.length > 0) {
          this.$store.dispatch('fetchProcessLog', { eid })
        }
      },
      initSticky() {
        setTimeout(() => {
          stickybits('.sticky', {
            scrollEl: '#' + this.doc_id,
            useStickyClasses: true
          })
        }, 100)
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .sticky
    transition: all 0.15s ease

  .sticky.js-is-sticky
  .sticky.js-is-stuck
    box-shadow: 0 4px 24px -8px rgba(0,0,0,0.2)
</style>
