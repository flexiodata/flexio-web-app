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
        <div class="flex flex-row items-center center mw-doc">
          <div class="flex-fill flex flex-row items-center pv3">
            <div class="dib">
              <h1 class="mv0 fw6">{{title}}</h1>
            </div>
            <LabelSwitch
              class="ml3"
              active-color="#13ce66"
              v-model="is_pipe_active"
            />
          </div>
          <el-select
            class="ml3"
            style="width: 10rem"
            :disabled="is_changed || is_code_changed || has_errors || active_item_idx != -1 || show_properties || show_history"
            v-show="!is_pipe_active"
            v-model="editor"
          >
            <el-option
              :label="option.label"
              :value="option.value"
              :key="option.value"
              v-for="option in editor_options"
            />
          </el-select>
          <el-dropdown
            class="ml2"
            trigger="click"
            @command="onCommand"
            v-show="!is_pipe_active"
          >
            <el-button style="padding: 6px 8px">
              <i class="material-icons">more_vert</i>
            </el-button>
            <el-dropdown-menu style="min-width: 10rem" slot="dropdown">
              <el-dropdown-item class="flex flex-row items-center ph2" command="schedule"><i class="material-icons mr3">date_range</i> Schedule</el-dropdown-item>
              <el-dropdown-item class="flex flex-row items-center ph2" command="deploy"><i class="material-icons mr3">archive</i> Deploy</el-dropdown-item>
              <el-dropdown-item divided></el-dropdown-item>
              <el-dropdown-item class="flex flex-row items-center ph2" command="properties"><i class="material-icons mr3">edit</i> Properties</el-dropdown-item>
            </el-dropdown-menu>
          </el-dropdown>
        </div>
      </div>
    </div>

    <div
      class="center mw-doc"
      v-if="is_fetched"
    >
      <div class="mb4 pa4 pt3 bg-white br2 css-white-box" v-if="show_properties">
        <!-- title bar -->
        <div class="flex flex-row items-center pt1 pb3">
          <h3 class="flex-fill mv0 fw6 f4">Properties</h3>
          <el-button
            class="ttu b invisible"
            size="small"
          >
            Spacer
          </el-button>
        </div>
        <!-- content -->
        <PipeDocumentForm ref="pipe-document-form" />
        <div class="flex flex-row items-center justify-end mt4" >
          <div class="flex flex-row pl3">
            <el-button
              size="medium"
              class="ttu b"
              @click="revertProperties"
            >
              Cancel
            </el-button>
            <el-button
              size="medium"
              type="primary"
              class="ttu b"
              :disabled="!is_changed || has_errors"
              @click="saveProperties"
            >
              Save
            </el-button>
          </div>
        </div>
      </div>

      <div
        class="mb4 pa4 pt3 bg-white br2 css-white-box"
        :class="{
          'o-40 no-pointer-events': show_properties || show_history
        }"
        v-show="!is_pipe_active"
      >
        <!-- title bar -->
        <div class="flex flex-row items-center pt1 pb3">
          <h3 class="flex-fill mv0 mr3 fw6 f4">Configuration</h3>
          <el-select
            class="tr"
            size="small"
            style="width: 10rem"
            :disabled="is_changed || is_code_changed || has_errors || active_item_idx != -1"
            v-model="editor"
            v-if="false"
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
        <div v-if="editor == 'builder'">
          <PipeBuilderList
            class="mv3"
            :container-id="doc_id"
            :has-errors.sync="has_errors"
            :active-item-idx.sync="active_item_idx"
            @save="saveChanges"
            v-model="edit_task_list"
          />
        </div>
        <div v-else>
          <PipeCodeEditor
            ref="code-editor"
            :type="editor"
            :options="{ minRows: 12, maxRows: 30 }"
            :has-errors.sync="has_errors"
            @save="saveChanges"
            v-model="edit_task_list"
          />
          <transition name="el-zoom-in-top">
            <div class="flex flex-row items-center justify-end mt3" v-if="show_save_cancel">
              <div class="flex flex-row pl3">
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
            </div>
          </transition>
        </div>
      </div>

      <div
        class="mb4 pa4 pt3 bg-white br2 css-white-box"
        :class="{
          'o-40 no-pointer-events': show_properties || show_history
        }"
      >
        <!-- title bar -->
        <div class="flex flex-row items-center pt1 pb3">
          <h3 class="flex-fill mv0 mr3 fw6 f4">Output</h3>
          <el-button
            class="ttu b"
            style="min-width: 5rem"
            type="primary"
            size="small"
            :disabled="is_changed || is_code_changed || has_errors || active_item_idx != -1"
            @click.stop="runPipe"
          >
            Run
          </el-button>
        </div>

        <!-- content -->
        <ProcessContent
          :process-eid="active_process_eid"
          v-if="active_process_eid.length > 0 && has_run_once"
        />
        <div
          class="bg-white ba b--black-10 pa3 f6"
          v-else-if="!has_run_once"
        >
          <em>Configure your pipe in the configuration panel, then click the 'Run' button above to see a preview of the pipe's output.</em>
        </div>
      </div>

      <div class="mb4 pa4 pt3 bg-white br2 css-white-box" v-if="show_history">
        <!-- title bar -->
        <div class="flex flex-row items-center pt1 pb3">
          <h3 class="flex-fill mv0 fw6 f4">History</h3>
          <i class="el-icon-close pointer f3 black-30 hover-black-60" @click="show_history = false"></i>
        </div>
        <!-- content -->
        <ProcessList />
        <div class="flex flex-row justify-end mt4">
          <el-button
            size="medium"
            class="ttu b"
            @click="show_history = false"
          >
            Close
          </el-button>
        </div>
      </div>
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
  import { mapState, mapGetters } from 'vuex'
  import { API_V2_ROOT } from '../api/resources'
  import { PROCESS_MODE_BUILD } from '../constants/process'

  import Spinner from 'vue-simple-spinner'
  import LabelSwitch from './LabelSwitch.vue'
  import CodeEditor from './CodeEditor.vue'
  import PipeCodeEditor from './PipeCodeEditor.vue'
  import PipeBuilderList from './PipeBuilderList.vue'
  import PipeDocumentForm from './PipeDocumentForm.vue'
  import PipeDocumentDropdown from './PipeDocumentDropdown.vue'
  import PipeSchedulePanel from './PipeSchedulePanel.vue'
  import PipeDeployPanel from './PipeDeployPanel.vue'
  import ProcessContent from './ProcessContent.vue'
  import ProcessList from './ProcessList.vue'

  const PIPE_MODE_UNDEFINED = ''
  const PIPE_MODE_BUILD     = 'B'
  const PIPE_MODE_RUN       = 'R'

  const PIPEDOC_VIEW_PROPERTIES = 'properties'
  const PIPEDOC_VIEW_CONFIGURE  = 'configure'
  const PIPEDOC_VIEW_HISTORY    = 'history'

  const PIPEDOC_EDITOR_SDK_JS  = 'sdk-js'
  const PIPEDOC_EDITOR_BUILDER = 'builder'
  const PIPEDOC_EDITOR_JSON    = 'json'
  const PIPEDOC_EDITOR_YAML    = 'yaml'

  export default {
    components: {
      Spinner,
      LabelSwitch,
      CodeEditor,
      PipeCodeEditor,
      PipeBuilderList,
      PipeDocumentForm,
      PipeDocumentDropdown,
      PipeSchedulePanel,
      PipeDeployPanel,
      ProcessContent,
      ProcessList
    },
    watch: {
      eid: {
        handler: 'loadPipe',
        immediate: true
      },
      active_view: {
        handler: 'onViewChange',
        immediate: true
      },
      show_history: {
        handler: 'fetchProcesses'
      },
      editor: {
        handler: 'onEditorChange',
        immediate: true
      },
      is_fetched: {
        handler: 'initSticky',
        immediate: true
      }
    },
    data() {
      return {
        active_view: _.get(this.$route, 'params.view', PIPEDOC_VIEW_CONFIGURE),
        editor: _.get(this.$route, 'query.editor', PIPEDOC_EDITOR_BUILDER),
        editor_options: [
          { value: PIPEDOC_EDITOR_BUILDER, label: 'Visual Builder' },
          { value: PIPEDOC_EDITOR_SDK_JS,  label: 'Javascript SDK' },
          { value: PIPEDOC_EDITOR_JSON,    label: 'JSON'           },
          { value: PIPEDOC_EDITOR_YAML,    label: 'YAML'           }
        ],
        has_run_once: false,
        has_errors: false,
        active_item_idx: -1,
        processes_fetched: false,
        show_properties: false,
        show_history: false,
        show_pipe_schedule_dialog: false,
        show_pipe_deploy_dialog: false
      }
    },
    computed: {
      ...mapState({
        orig_pipe: state => state.pipe.orig_pipe,
        edit_pipe: state => state.pipe.edit_pipe,
        edit_keys: state => state.pipe.edit_keys,
        is_fetching: state => state.pipe.fetching,
        is_fetched: state => state.pipe.fetched
      }),
      eid() {
        return _.get(this.$route, 'params.eid', undefined)
      },
      alias() {
        return _.get(this.orig_pipe, 'alias', '')
      },
      identifier() {
        return this.alias.length > 0 ? this.alias : this.eid
      },
      api_endpoint() {
        return 'https://api.flex.io/v1/me/pipes/' + this.identifier
      },
      title() {
        return _.get(this.orig_pipe, 'name', '')
      },
      doc_id() {
        return 'pipe-doc-' + this.eid
      },
      store_pipe() {
        return this.getStorePipe()
      },
      edit_task_list: {
        get() {
          var task = _.get(this.edit_pipe, 'task', { op: 'sequence', items: [] })
          return task
        },
        set(value) {
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
      is_pipe_active: {
        get() {
          return _.get(this.edit_pipe, 'pipe_mode') == PIPE_MODE_RUN ? true : false
        },
        set() {
          var doSet = () => {
            var pipe_mode = this.is_pipe_active ? PIPE_MODE_BUILD : PIPE_MODE_RUN
            var pipe = _.cloneDeep(this.edit_pipe)
            _.assign(pipe, { pipe_mode })
            this.$store.commit('pipe/UPDATE_EDIT_PIPE', pipe)
            this.saveChanges()
          }

          if (this.is_pipe_active) {
            this.$confirm('This pipe is turned on and is possibly being used in a production environment. Are you sure you want to continue?', 'Really turn pipe off?', {
              confirmButtonText: 'TURN PIPE OFF',
              cancelButtonText: 'CANCEL',
              type: 'warning'
            }).then(() => {
              doSet()
            }).catch(() => {
              // do nothing
            })
          } else {
            doSet()
          }
        }
      },
      is_code_changed() {
        return this.isCodeChanged()
      },
      is_changed() {
        return this.isChanged()
      },
      show_save_cancel() {
        return this.is_changed || this.is_code_changed
      },
      active_process_eid() {
        var process = _.last(this.getActiveDocumentProcesses())
        return _.get(process, 'eid', '')
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
      onViewChange(val) {
        if (val == PIPEDOC_VIEW_HISTORY) {
          this.fetchProcesses()
        }

        this.updateRoute()
      },
      onEditorChange(val) {
        this.updateRoute()
      },
      onCommand(cmd) {
        switch (cmd) {
          case 'schedule':
            this.show_pipe_schedule_dialog = true
            return
          case 'deploy':
            this.show_pipe_deploy_dialog = true
            return
          case 'properties':
            this.show_properties = true
            return
        }
      },
      switchEditor(val) {
        this.editor = val
      },
      fetchProcesses() {
        if (!this.processes_fetched) {
          this.$store.dispatch('fetchProcesses', { parent_eid: this.eid }).then(response => {
            if (response.ok) {
              this.processes_fetched = true
            } else {
              // TODO: add error handling
            }
          })
        }
      },
      updateRoute() {
        // update the route
        var new_route = _.pick(this.$route, ['name', 'meta', 'params', 'path'])
        var view = this.active_view
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

        this.$nextTick(() => {
          // one of the few times we need to do something imperatively
          var editor = this.$refs['code-editor']
          if (editor && editor.revert) {
            editor.revert()
          }
        })
      },
      saveChanges() {
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
      },
      revertProperties() {
        this.cancelChanges()
        this.show_properties = false
      },
      saveProperties() {
        var doc_form = this.$refs['pipe-document-form']
        doc_form.validate((valid) => {
          if (!valid)
            return

          this.saveChanges().then(() => {
            this.show_properties = false
          })
        })
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
