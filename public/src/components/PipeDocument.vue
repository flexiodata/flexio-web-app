<template>
  <!-- pipe fetching -->
  <div class="bg-nearer-white" v-if="is_fetching">
    <div class="h-100 flex flex-row items-center justify-center">
      <Spinner size="large" message="Loading..." />
    </div>
  </div>

  <!-- pipe fetched -->
  <div class="bg-nearer-white" v-else-if="is_fetched">
    <!-- runtime view; run mode; no ui steps -->
    <div
      class="h-100 pa4 overflow-y-scroll"
      v-if="is_view_runtime && is_pipe_mode_run && edit_ui_list.length == 0"
    >
      <div class="mv4 center mw-doc">
        <div class="pa4 bg-white br2 tc css-white-box">
          <IconMessage title="This pipe cannot be run in a browser.">
            If you are the owner of this pipe, please set up a web interface. If this pipe was shared with you, please contact the person who shared it with you to have it set up for your use.
          </IconMessage>
        </div>
      </div>
    </div>

    <!-- runtime view; run mode -->
    <BuilderDocument
      class="h-100 overflow-y-scroll"
      :definition="edit_pipe"
      v-else-if="is_view_runtime && is_pipe_mode_run"
    />

    <!-- runtime view; build mode -->
    <div
      class="h-100 pa4 overflow-y-scroll"
      v-else-if="is_view_runtime && !is_pipe_mode_run"
    >
      <div class="mv4 center mw-doc">
        <div class="pa4 bg-white br2 tc css-white-box">
          <IconMessage title="This pipe cannot be run in a browser.">
            If you are the owner of this pipe, please turn it on. If this pipe was shared with you, please contact the person who shared it with you to have it turned on.
          </IconMessage>
        </div>
      </div>
    </div>

    <!-- build view; run mode -->
    <div
      class="h-100 pa4"
      v-else-if="is_pipe_mode_run"
    >
      <PipeDocumentHeader
        class="center mw-doc"
        :title="title"
        :is-mode-run.sync="is_pipe_mode_run"
      />
      <div class="mt3 mb4 center mw-doc">
        <div class="pa4 pt3 bg-white br2 css-white-box">
          <PipeDocumentRunPanel
            class="tc"
            :eid="eid"
            :is-mode-run.sync="is_pipe_mode_run"
          />
        </div>
      </div>
    </div>

    <!-- build view; build mode -->
    <div class="h-100" v-else>
      <vue-slide-up-down
        :active="show_save_cancel"
        :duration="200"
      >
        <div class="flex flex-row items-center el-alert el-alert--warning bb b--black-10">
          <div class="flex-fill f6">Your have made changes to this pipe. Would you like to save your changes?</div>
          <el-button
            class="ttu b"
            size="small"
            @click="cancelChanges"
          >
            Cancel
          </el-button>
          <el-button
            class="ttu b"
            size="small"
            type="primary"
            @click="saveChanges"
          >
            Save changes
          </el-button>
        </div>
      </vue-slide-up-down>
      <div class="flex flex-row h-100">
        <el-menu
          class="bg-nearer-white"
          default-active="0"
        >
          <el-menu-item
            index="0"
            @click="show_yaml = !show_yaml"
          >
            <i class="material-icons nl2 nr2 hint--right" aria-label="Toggle Pipe YAML">code</i>
          </el-menu-item>
        </el-menu>

        <multipane
          class="vertical-panes"
          layout="vertical"
        >
          <div
            class="pane trans-w"
            :style="{
              maxWidth: '50%',
              minWidth: show_yaml ? '100px' : '0',
              width: show_yaml ? '25%' : '0'
            }"
          >
            <PipeCodeEditor
              class="h-100"
              ref="code-editor"
              type="yaml"
              editor-cls="bg-white h-100"
              :task-only="false"
              :has-errors.sync="has_errors"
              @save="saveChanges"
              v-model="edit_pipe"
            />
          </div>
          <multipane-resizer />
          <div
            class="pane pa4 pt0 overflow-y-scroll"
            :id="content_pane_id"
            :style="{ flexGrow: 1 }"
          >
            <PipeDocumentHeader
              class="nl4 nr4 pv2 ph3 relative z-7 bg-nearer-white sticky"
              @schedule-click="show_pipe_schedule_dialog = true"
              @properties-click="show_pipe_properties_dialog = true"
              @run-click="testPipe"
              :title="title"
              :is-mode-run.sync="is_pipe_mode_run"
            />
            <div class="mv4 center mw-doc">
              <el-collapse class="el-collapse--plain" v-model="active_collapse_items">
                <el-collapse-item name="web-ui">
                  <template slot="title">
                    <div class="flex flex-row items-center">
                      <span class="f4">Web Interface</span>
                      <span class="ml1 lh-1 hint--bottom hint--large" aria-label="An optional web interface that can be used in a runtime enviroment to prompt users for parameters to use when running the pipe. Interface elements can be added using the YAML or JSON in the sidebar.">
                        <i class="el-icon-info blue"></i>
                      </span>
                    </div>
                  </template>
                  <div class="mv3 pa4 bg-white br2 css-white-box">
                    <BuilderList
                      builder-mode="wizard"
                      :items="edit_ui_list"
                      :container-id="content_pane_id"
                      :active-item-idx.sync="active_ui_idx"
                      :show-numbers="true"
                      :show-icons="false"
                      :show-insert-buttons="false"
                      :show-edit-buttons="false"
                      :show-delete-buttons="false"
                      @item-prev="active_ui_idx--"
                      @item-next="active_ui_idx++"
                      v-if="edit_ui_list.length > 0"
                    />
                    <div
                      class="tc f6"
                      v-else
                    >
                      <em>There is no web interface for this pipe.</em>
                    </div>
                  </div>
                </el-collapse-item>
                <el-collapse-item name="task-list">
                  <template slot="title">
                    <div class="flex flex-row items-center">
                      <span class="f4">Task List</span>
                      <span class="ml1 lh-1 hint--bottom hint--large" aria-label="The task list defines the actual logic for the pipe that will be run. Steps can be added either using the interface below or the YAML or JSON in the sidebar.">
                        <i class="el-icon-info blue"></i>
                      </span>
                    </div>
                  </template>
                  <div class="mv3 pa4 bg-white br2 css-white-box">
                    <PipeBuilderList
                      :container-id="content_pane_id"
                      :has-errors.sync="has_errors"
                      :active-item-idx.sync="active_task_idx"
                      @save="saveChanges"
                      v-model="edit_task_list"
                    />
                  </div>
                </el-collapse-item>
                <el-collapse-item name="output" :id="output_item_id">
                  <template slot="title">
                    <div class="flex flex-row items-center">
                      <span class="f4">Result</span>
                      <span class="ml1 lh-1 hint--bottom hint--large" aria-label="The output panel shows the output of the pipe after a test run.">
                        <i class="el-icon-info blue"></i>
                      </span>
                    </div>
                  </template>
                  <div class="mv3 pa4 bg-white br2 css-white-box">
                    <ProcessContent
                      :process-eid="active_process_eid"
                      v-if="active_process_eid.length > 0 && has_run_once"
                    />
                    <div
                      class="tc f6"
                      v-else-if="!has_run_once"
                    >
                      <em>Configure your pipe logic using the task list, then click the <code class="ph1 ba b--black-10 bg-near-white br2">Test</code> button above to see a preview of the pipe's output.</em>
                    </div>
                  </div>
                </el-collapse-item>
              </el-collapse>
            </div>
          </div>
        </multipane>
      </div>

    </div>

    <el-dialog
      custom-class="el-dialog--no-header el-dialog--no-footer"
      width="42rem"
      top="8vh"
      :modal-append-to-body="false"
      :visible.sync="show_pipe_properties_dialog"
    >
      <PipeDocumentForm
        ref="pipe-document-form"
        @close="revertProperties"
        @cancel="revertProperties"
        @submit="saveProperties"
      />
    </el-dialog>

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

  </div>
</template>

<script>
  import stickybits from 'stickybits'
  import { mapState, mapGetters } from 'vuex'
  import { PROCESS_MODE_BUILD } from '../constants/process'

  import { Multipane, MultipaneResizer } from 'vue-multipane'
  import VueSlideUpDown from 'vue-slide-up-down'
  import Spinner from 'vue-simple-spinner'
  import IconMessage from './IconMessage.vue'
  import LabelSwitch from './LabelSwitch.vue'
  import BuilderDocument from './BuilderDocument.vue'
  import BuilderList from './BuilderList.vue'
  import PipeBuilderList from './PipeBuilderList.vue'
  import PipeCodeEditor from './PipeCodeEditor.vue'
  import PipeDocumentHeader from './PipeDocumentHeader.vue'
  import PipeDocumentRunPanel from './PipeDocumentRunPanel.vue'
  import PipeSchedulePanel from './PipeSchedulePanel.vue'
  import PipeDocumentForm from './PipeDocumentForm.vue'
  import ProcessContent from './ProcessContent.vue'

  const PIPE_MODE_UNDEFINED = ''
  const PIPE_MODE_BUILD     = 'B'
  const PIPE_MODE_RUN       = 'R'

  const PIPEDOC_VIEW_BUILD  = 'build'
  const PIPEDOC_VIEW_RUN    = 'run'

  export default {
    components: {
      Multipane,
      MultipaneResizer,
      VueSlideUpDown,
      Spinner,
      IconMessage,
      LabelSwitch,
      BuilderDocument,
      BuilderList,
      PipeBuilderList,
      PipeCodeEditor,
      PipeDocumentHeader,
      PipeDocumentRunPanel,
      PipeSchedulePanel,
      PipeDocumentForm,
      ProcessContent
    },
    watch: {
      eid: {
        handler: 'loadPipe',
        immediate: true
      },
      active_view: {
        handler: 'updateRoute',
        immediate: true
      },
      is_pipe_mode_run: {
        handler: 'initSticky',
        immediate: true
      },
      is_fetched: {
        handler: 'initSticky',
        immediate: true
      }
    },
    data() {
      return {
        active_view: _.get(this.$route, 'params.view', PIPEDOC_VIEW_BUILD),
        active_collapse_items: ['web-ui', 'task-list', 'output'],
        active_ui_idx: 0,
        active_task_idx: -1,
        content_pane_id: _.uniqueId('pane-'),
        output_item_id: _.uniqueId('item-'),
        show_pipe_schedule_dialog: false,
        show_pipe_properties_dialog: false,
        show_yaml: true,
        has_run_once: false,
        has_errors: false
      }
    },
    computed: {
      ...mapState({
        orig_pipe: state => state.pipe.orig_pipe,
        edit_keys: state => state.pipe.edit_keys,
        is_fetching: state => state.pipe.fetching,
        is_fetched: state => state.pipe.fetched
      }),
      eid() {
        return _.get(this.$route, 'params.eid', undefined)
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
      show_save_cancel() {
        var orig_mode = _.get(this.orig_pipe, 'pipe_mode')
        var edit_mode = _.get(this.edit_pipe, 'pipe_mode')

        // we're entering run mode, don't show the save/cancel banner
        if (orig_mode == PIPE_MODE_BUILD && edit_mode == PIPE_MODE_RUN) {
          return false
        }

        return this.is_changed || this.is_code_changed
      },
      edit_pipe: {
        get() {
          var pipe = _.get(this.$store.state.pipe, 'edit_pipe', {})
          return pipe
        },
        set(value) {
          try {
            var pipe = _.cloneDeep(value)
            this.$store.commit('pipe/UPDATE_EDIT_PIPE', pipe)
          }
          catch(e)
          {
            // TODO: add error handling
          }
        }
      },
      edit_ui_list: {
        get() {
          var ui = _.get(this.edit_pipe, 'ui.prompts', [])
          return ui
        },
        set(value) {

        }
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
          }
          catch(e)
          {
            // TODO: add error handling
          }
        }
      },
      is_view_runtime() {
        return this.active_view == PIPEDOC_VIEW_RUN
      },
      active_process_eid() {
        var process = _.last(this.getActiveDocumentProcesses())
        return _.get(process, 'eid', '')
      },
      is_pipe_mode_run: {
        get() {
          return _.get(this.orig_pipe, 'pipe_mode') == PIPE_MODE_RUN ? true : false
        },
        set(value) {
          var doSet = () => {
            var pipe_mode = value === false ? PIPE_MODE_BUILD : PIPE_MODE_RUN
            var pipe = _.cloneDeep(this.edit_pipe)
            _.assign(pipe, { pipe_mode })
            this.$store.commit('pipe/UPDATE_EDIT_PIPE', pipe)
            this.saveChanges()
          }

          if (value === false) {
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
      }
    },
    methods: {
      ...mapGetters('pipe', [
        'isCodeChanged',
        'isChanged'
      ]),
      ...mapGetters([
        'getActiveDocumentProcesses'
      ]),
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
      cancelChanges() {
        this.$store.commit('pipe/INIT_PIPE', this.orig_pipe)
        this.revertCodeEditor()
      },
      saveChanges() {
        var eid = this.eid
        var attrs = _.pick(this.edit_pipe, this.edit_keys)

        // don't POST null values
        attrs = _.omitBy(attrs, (val, key) => { return _.isNil(val) })

        return this.$store.dispatch('updatePipe', { eid, attrs }).then(response => {
          this.active_task_idx = -1

          if (response.ok) {
            this.$message({
              message: 'The pipe was updated successfully.',
              type: 'success'
            })

            this.$store.commit('pipe/INIT_PIPE', response.body)
            this.revertCodeEditor()
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
        this.show_pipe_properties_dialog = false
      },
      saveProperties() {
        var doc_form = this.$refs['pipe-document-form']
        doc_form.validate((valid) => {
          if (!valid)
            return

          this.saveChanges().then(() => {
            this.show_pipe_properties_dialog = false
          })
        })
      },
      updatePipeSchedule(attrs) {
        attrs = _.pick(attrs, ['schedule', 'schedule_status'])

        var pipe = _.cloneDeep(this.edit_pipe)
        _.assign(pipe, attrs)
        this.$store.commit('pipe/UPDATE_EDIT_PIPE', pipe)

        this.saveChanges().then(() => {
          this.show_pipe_schedule_dialog = false
        })
      },
      testPipe() {
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
        })

        this.scrollToItem(this.output_item_id)
      },
      updateRoute() {
        // update the route
        var new_route = _.pick(this.$route, ['name', 'meta', 'params', 'path'])
        var view = this.active_view
        _.set(new_route, 'params.view', view)
        this.$router.replace(new_route)
      },
      revertCodeEditor() {
        this.$nextTick(() => {
          // one of the few times we need to do something imperatively
          var editor = this.$refs['code-editor']
          if (editor && editor.revert) {
            editor.revert()
          }
        })
      },
      initSticky() {
        setTimeout(() => {
          stickybits('.sticky', {
            scrollEl: '#' + this.content_pane_id,
            useStickyClasses: true,
            stickyBitStickyOffset: 0
          })
        }, 100)
      },
      scrollToItem(item_id) {
        if (_.isString(item_id)) {
          setTimeout(() => {
            this.$scrollTo('#'+item_id, {
                container: '#'+this.content_pane_id,
                duration: 400,
                offset: -32
            })
          }, 10)
        }
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .vertical-panes
    width: 100%
    height: 100%

  .vertical-panes > .pane ~ .pane
    border-left: 1px solid #ddd

  .sticky
    box-shadow: 0 1px 0 0 rgba(0,0,0,0.05)
    transition: all 0.15s ease

  .sticky.js-is-sticky
  .sticky.js-is-stuck
    box-shadow: 0 4px 24px -8px rgba(0,0,0,0.2)
</style>
