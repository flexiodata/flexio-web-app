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
            If you are the owner of this pipe, please set up a web user interface. If this pipe was shared with you, please contact the person who shared it with you to have it set up for your use.
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
      class="h-100 pa4 overflow-y-scroll"
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
      <transition name="el-zoom-in-top">
        <div
          class="flex flex-row items-center el-alert el-alert--warning bb b--black-10"
          v-if="show_save_cancel"
        >
          <div class="flex-fill">Your have made changes to this pipe. Would you like to save your changes?</div>
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
      </transition>
      <multipane
        class="vertical-panes"
        layout="vertical"
      >
        <div
          class="pane"
          :style="{ minWidth: '100px', width: '25%', maxWidth: '50%' }"
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
          class="pane pa4 overflow-y-scroll"
          :style="{ flexGrow: 1 }"
        >
          <PipeDocumentHeader
            class="center mw-doc"
            :title="title"
            :is-mode-run.sync="is_pipe_mode_run"
          />
          <div class="mt3 mb4 center mw-doc">
            <el-collapse class="el-collapse--plain" v-model="active_collapse_items">
              <el-collapse-item name="web-ui">
                <template slot="title">
                  <div class="flex flex-row items-center">
                    <span class="f4">Web User Interface</span>
                    <span class="ml1 lh-1 hint--bottom hint--large" aria-label="An optional web user interface that can be used in a runtime enviroment to prompt users for parameters to use when running the pipe. Interface elements can be added using the YAML or JSON in the sidebar.">
                      <i class="el-icon-info blue"></i>
                    </span>
                  </div>
                </template>
                <div class="pa4 bg-white br2 css-white-box">
                  <BuilderList
                    builder-mode="wizard"
                    :items="edit_ui_list"
                    :container-id="doc_id"
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
                  <div class="tc f5" v-else>There is no web user interface for this pipe.</div>
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
                <div class="pa4 bg-white br2 css-white-box">
                  <PipeBuilderList
                    :container-id="doc_id"
                    :has-errors.sync="has_errors"
                    :active-item-idx.sync="active_task_idx"
                    @save="saveChanges"
                    v-model="edit_task_list"
                  />
                </div>
              </el-collapse-item>
            </el-collapse>
            <div
              class="mb4 pa4 pt3 bg-white br2 css-white-box"
              v-if="!is_pipe_mode_run"
            >
              <!-- title bar -->
              <div class="flex flex-row items-center pt1 pb3">
                <h3 class="flex-fill mv0 mr3 fw6 f4">Output</h3>
                <el-button
                  class="ttu b"
                  style="min-width: 5rem"
                  type="primary"
                  size="small"
                  :disabled="is_changed || is_code_changed || has_errors || active_task_idx != -1"
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

          </div>
        </div>
      </multipane>
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import { PROCESS_MODE_BUILD } from '../constants/process'

  import { Multipane, MultipaneResizer } from 'vue-multipane'
  import Spinner from 'vue-simple-spinner'
  import IconMessage from './IconMessage.vue'
  import LabelSwitch from './LabelSwitch.vue'
  import BuilderDocument from './BuilderDocument.vue'
  import BuilderList from './BuilderList.vue'
  import PipeBuilderList from './PipeBuilderList.vue'
  import PipeCodeEditor from './PipeCodeEditor.vue'
  import PipeDocumentHeader from './PipeDocumentHeader.vue'
  import PipeDocumentRunPanel from './PipeDocumentRunPanel.vue'
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
      Spinner,
      IconMessage,
      LabelSwitch,
      BuilderDocument,
      BuilderList,
      PipeBuilderList,
      PipeCodeEditor,
      PipeDocumentHeader,
      PipeDocumentRunPanel,
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
      }
    },
    data() {
      return {
        active_view: _.get(this.$route, 'params.view', PIPEDOC_VIEW_BUILD),
        active_collapse_items: ['web-ui', 'task-list'],
        active_ui_idx: 0,
        active_task_idx: -1,
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
      doc_id() {
        return 'pipe-doc-' + this.eid
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
        })
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
</style>
