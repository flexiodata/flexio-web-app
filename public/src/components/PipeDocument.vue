<template>
  <!-- fetching -->
  <div class="bg-nearer-white" v-if="is_fetching">
    <div class="flex flex-column justify-center h-100">
      <Spinner size="large" message="Loading..." />
    </div>
  </div>

  <!-- fetched -->
  <div class="bg-nearer-white" v-else-if="is_fetched">
    <div class="flex flex-row h-100">
      <multipane
        class="flex-fill vertical-panes"
        layout="vertical"
      >
        <div
          class="pane pa4 pt0 overflow-y-scroll"
          :id="scrollbar_container_id"
          :style="{
            minWidth: '800px',
            flexGrow: show_sidebar ? undefined : 1
          }"
        >
          <PipeDocumentHeader
            class="relative z-7 bg-nearer-white sticky"
            :title="title"
            :is-mode-run.sync="is_deployed"
            :show-save-cancel="show_save_cancel"
            @properties-click="openPropertiesDialog"
            @cancel-click="cancelChanges"
            @save-click="saveChanges"
            @run-click="testPipe"
          />

          <div class="mv4 center mw-doc" style="padding-bottom: 12rem">
            <el-collapse
              class="el-collapse--plain"
              v-model="active_collapse_items"
            >
              <!-- tasks panel -->
              <el-collapse-item
                class="mb4 pv1 ph3 bg-white br2 css-white-box"
                name="tasks"
              >
                <template slot="title">
                  <div class="flex flex-row items-center">
                    <span class="f4">Tasks</span>
                  </div>
                </template>
                <div class="pt3 ph3">
                  <PipeBuilderList
                    ref="task-list"
                    :container-id="scrollbar_container_id"
                    :has-errors.sync="has_errors"
                    :active-item-idx.sync="active_task_idx"
                    @cancel="cancelChanges"
                    @save="saveChanges"
                    v-model="edit_task_list"
                  />
                </div>
              </el-collapse-item>
            </el-collapse>
          </div>
        </div>

        <multipane-resizer
          :class="{
            'no-pointer-events': !show_sidebar
          }"
          :style="{
            zIndex: show_sidebar ? 3 : 0
          }"
        />

        <div
          class="pane bg-white"
          :style="{
            minWidth: show_sidebar ? '220px' : '1px',
            width: show_sidebar ? '22%' : '1px',
            marginRight: show_sidebar ? '0' : '-2px',
            opacity: show_sidebar ? '1' : '0.01',
            zIndex: show_sidebar ? 2 : 0,
            flexGrow: show_sidebar ? 1 : undefined
          }"
        >
          <template v-if="show_yaml">
            <div class="flex flex-row items-center bg-nearer-white bb b--black-05 pa2">
              <i class="material-icons mr1">code</i>
              <div class="f6 fw6 flex-fill">Pipe Definition</div>
              <el-radio-group
                class="mh2"
                size="micro"
                v-model="yaml_view"
              >
                <el-radio-button label="json"><span class="fw6">JSON</span></el-radio-button>
                <el-radio-button label="yaml"><span class="fw6">YAML</span></el-radio-button>
              </el-radio-group>
              <div class="pointer f5 black-30 hover-black-60 hint--bottom-left" aria-label="Hide Pipe Definition" @click="showYaml(false)">
                <i class="el-icon-close fw6"></i>
              </div>
            </div>
            <PipeCodeEditor
              class="h-100"
              ref="code-editor"
              editor-cls="bg-white h-100"
              :type="yaml_view"
              :class="{
                'no-pointer-events': !show_yaml
              }"
              :show-json-view-toggle="false"
              :task-only="false"
              :has-errors.sync="has_errors"
              @save="saveChanges"
              v-model="edit_pipe"
            />
          </template>
          <div class="flex flex-column h-100" v-if="show_testing">
            <div class="flex-none flex flex-row items-center bg-nearer-white bb b--black-05 pa2">
              <i class="material-icons mr1">assignment</i>
              <div class="f6 fw6 flex-fill">Testing</div>
              <div class="pointer f5 black-30 hover-black-60 hint--bottom-left" aria-label="Hide Testing" @click="showTesting(false)">
                <i class="el-icon-close fw6"></i>
              </div>
            </div>

            <div class="flex-fill overflow-y-auto">
              <!-- input panel -->
              <div class="mb4 ph2">
                <h4 class="mv0 pa3">Input</h4>

                <div class="ph3">
                  <p class="mt0 ttu fw6 f7 moon-gray">Test this pipe with the following POST parameters</p>
                  <ProcessInput
                    ref="process-input"
                    v-model="process_input"
                    :process-data.sync="process_data"
                  />
                </div>
              </div>

              <!-- output panel -->
              <div class="mb4 ph2">
                <h4 class="mv0 ph3 pb3">Output</h4>

                <div class="ph3">
                  <ProcessContent :process-eid="active_process_eid">
                    <div class="ba b--black-10 pa3 tc f6 lh-copy" slot="empty">
                      <em>Click the <code class="ph1 ba b--black-10 bg-nearer-white br2">Test</code> button to see the result of your pipe logic here.</em>
                    </div>
                  </ProcessContent>
                </div>
              </div>
            </div>
          </div>
        </div>
      </multipane>

      <div
        class="flex-none bg-nearer-white pt2"
        :style="{
          width: show_sidebar ? '0' : '50px',
          opacity: show_sidebar ? '0' : '1'
        }"
      >
        <el-button
          type="text"
          style="padding: 8px 12px"
          @click="showYaml(true)"
        >
          <i class="material-icons hint--left" :aria-label="show_yaml ? 'Hide Pipe Definition' : 'Show Pipe Definition'">code</i>
        </el-button>
        <div></div>
        <el-button
          type="text"
          style="padding: 8px 12px"
          @click="showTesting(true)"
        >
          <i class="material-icons hint--left" :aria-label="show_testing ? 'Hide Testing Panel' : 'Show Testing Panel'">assignment</i>
        </el-button>
      </div>
    </div>

    <!-- pipe properties dialog -->
    <el-dialog
      custom-class="el-dialog--no-header el-dialog--no-footer"
      width="42rem"
      top="4vh"
      :modal-append-to-body="false"
      :close-on-click-modal="false"
      :visible.sync="show_pipe_properties_dialog"
    >
      <PipePropertiesPanel
        :pipe="edit_pipe"
        @close="show_pipe_properties_dialog = false"
        @cancel="show_pipe_properties_dialog = false"
        @submit="saveProperties"
      />
    </el-dialog>

    <!-- pipe schedule dialog -->
    <el-dialog
      custom-class="el-dialog--no-header el-dialog--no-footer"
      width="42rem"
      top="4vh"
      :modal-append-to-body="false"
      :close-on-click-modal="false"
      :visible.sync="show_pipe_schedule_dialog"
    >
      <PipeSchedulePanel
        :pipe="edit_pipe"
        @close="show_pipe_schedule_dialog = false"
        @cancel="show_pipe_schedule_dialog = false"
        @submit="saveSchedule"
      />
    </el-dialog>
  </div>

  <!-- pipe not found -->
  <PageNotFound class="bg-nearer-white" v-else-if="pipe_not_found" />
</template>

<script>
  import stickybits from 'stickybits'
  import marked from 'marked'
  import { mapState, mapGetters } from 'vuex'
  import {
    SCHEDULE_FREQUENCY_FIVE_MINUTES,
    SCHEDULE_DEFAULTS
  } from '../constants/schedule'
  import { PROCESS_MODE_BUILD } from '../constants/process'

  import { Multipane, MultipaneResizer } from 'vue-multipane'
  import Spinner from 'vue-simple-spinner'
  import PipeBuilderList from '@comp/PipeBuilderList'
  import PipeCodeEditor from '@comp/PipeCodeEditor'
  import PipeDocumentHeader from '@comp/PipeDocumentHeader'
  import PipePropertiesPanel from '@comp/PipePropertiesPanel'
  import PipeSchedulePanel from '@comp/PipeSchedulePanel'
  import ProcessInput from '@comp/ProcessInput'
  import ProcessContent from '@comp/ProcessContent'
  import PageNotFound from '@comp/PageNotFound'

  const ACTIVE = 'A'
  const INACTIVE = 'I'

  const DEPLOY_MODE_UNDEFINED = ''
  const DEPLOY_MODE_BUILD     = 'B'
  const DEPLOY_MODE_RUN       = 'R'

  export default {
    metaInfo() {
      return {
        title: _.get(this.orig_pipe, 'name', 'pipes')
      }
    },
    components: {
      Multipane,
      MultipaneResizer,
      Spinner,
      PipeBuilderList,
      PipeCodeEditor,
      PipeDocumentHeader,
      PipePropertiesPanel,
      PipeSchedulePanel,
      ProcessInput,
      ProcessContent,
      PageNotFound
    },
    watch: {
      route_identifier: {
        handler: 'loadPipe',
        immediate: true
      },
      is_deployed: {
        handler: 'initSticky',
        immediate: true
      },
      is_fetched: {
        handler: 'initSticky',
        immediate: true
      },
      is_changed(val) {
        this.$nextTick(() => {
          this.save_cancel_zindex++
          this.show_save_cancel = val
        })
      },
      show_yaml() {
        this.transitioning_sidebar = true
        setTimeout(() => { this.transitioning_sidebar = false }, 150)
      },
      show_testing() {
        this.transitioning_sidebar = true
        setTimeout(() => { this.transitioning_sidebar = false }, 150)
      }
    },
    data() {
      return {
        active_collapse_items: ['tasks'],
        active_task_idx: -1,
        scrollbar_container_id: _.uniqueId('pane-'),
        show_pipe_schedule_dialog: false,
        show_pipe_properties_dialog: false,
        yaml_view: 'yaml',
        show_yaml: false,
        show_testing: false,
        transitioning_sidebar: false,
        has_tested_once: false,
        has_errors: false,
        is_saving: false,
        show_save_cancel: false,
        save_cancel_zindex: 2050,
        pipe_not_found: false,
        process_input: {},
        process_data: {}
      }
    },
    computed: {
      ...mapState({
        orig_pipe: state => state.pipe.orig_pipe,
        edit_keys: state => state.pipe.edit_keys,
        is_fetching: state => state.pipe.fetching,
        is_fetched: state => state.pipe.fetched,
        is_changed: state => state.pipe.changed
      }),
      route_identifier() {
        return _.get(this.$route, 'params.identifier', undefined)
      },
      eid() {
        return _.get(this.orig_pipe, 'eid', undefined)
      },
      title() {
        return _.get(this.orig_pipe, 'name', '')
      },
      save_cancel_style() {
        return 'z-index: ' + this.save_cancel_zindex
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
      edit_input: {
        get() {
          var input = _.get(this.edit_pipe, 'ui.input', {})
          return _.isObject(input) ? input : {}
        },
        set(value) {
          try {
            var input = _.cloneDeep(value)
            var pipe = _.cloneDeep(this.edit_pipe)
            _.set(pipe, 'ui.input', input)
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
          return _.isArray(ui) ? ui : []
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
      pipe_identifier() {
        var pname = this.edit_pipe.name
        return pname.length > 0 ? pname : _.get(this.edit_pipe, 'eid', '')
      },
      pipe_schedule() {
        return _.get(this.edit_pipe, 'schedule', {})
      },
      active_process_eid() {
        if (!this.has_tested_once) {
          return ''
        }

        var process = _.last(this.getActiveDocumentProcesses())
        return _.get(process, 'eid', '')
      },
      show_sidebar() {
        return this.show_yaml || this.show_testing
      },
      is_deployed: {
        get() {
          return _.get(this.orig_pipe, 'deploy_mode') == DEPLOY_MODE_RUN ? true : false
        },
        set(value) {
          var doSet = () => {
            var deploy_mode = value === false ? DEPLOY_MODE_BUILD : DEPLOY_MODE_RUN
            var pipe = _.cloneDeep(this.edit_pipe)
            _.assign(pipe, { deploy_mode })
            this.$store.commit('pipe/UPDATE_EDIT_PIPE', pipe)
            this.saveChanges()
          }

          if (value === false) {
            this.$confirm('This pipe is turned on and is possibly being used in a production environment. Are you sure you want to continue?', 'Really turn pipe off?', {
              confirmButtonClass: 'ttu fw6',
              cancelButtonClass: 'ttu fw6',
              confirmButtonText: 'Turn pipe off',
              cancelButtonText: 'Cancel',
              type: 'warning'
            }).then(() => {
              doSet()
              this.$store.track("Turned Pipe Off")
            }).catch(() => {
              this.$store.track("Turned Pipe Off (Canceled)")
            })
          } else {
            doSet()
            this.$store.track("Turned Pipe On")
          }
        }
      }
    },
    methods: {
      ...mapGetters([
        'getActiveUser',
        'getActiveDocumentProcesses'
      ]),
      loadPipe() {
        this.$store.commit('pipe/FETCHING_PIPE', true)

        this.$store.dispatch('v2_action_fetchPipe', { eid: this.route_identifier }).then(response => {
          var pipe = response.data
          this.pipe_not_found = false
          this.$store.commit('pipe/INIT_PIPE', pipe)
        }).catch(error => {
          this.pipe_not_found = true
        }).finally(() => {
          this.$store.commit('pipe/FETCHING_PIPE', false)
        })
      },
      cancelChanges() {
        this.$store.commit('pipe/INIT_PIPE', this.orig_pipe)
        this.revert()
        this.active_task_idx = -1
      },
      saveChanges() {
        this.is_saving = true

        var eid = this.eid
        var attrs = _.pick(this.edit_pipe, this.edit_keys)

        // don't POST null values
        attrs = _.omitBy(attrs, (val, key) => { return _.isNil(val) })

        return this.$store.dispatch('v2_action_updatePipe', { eid, attrs }).then(response => {
          this.$message({
            message: 'The pipe was updated successfully.',
            type: 'success'
          })

          var pipe = response.data
          this.$store.commit('pipe/INIT_PIPE', pipe)
          this.revert()
        }).catch(error => {
          this.$message({
            message: 'There was a problem updating the pipe.',
            type: 'error'
          })
        }).finally(() => {
          this.active_task_idx = -1
        })
      },
      openPropertiesDialog() {
        this.show_pipe_properties_dialog = true
        this.$store.track('Opened Properties Dialog')
      },
      openScheduleDialog() {
        this.show_pipe_schedule_dialog = true
        this.$store.track('Opened Schedule Dialog')
      },
      saveProperties(attrs) {
        attrs = _.pick(attrs, ['name', 'short_description', 'description'])

        var pipe = _.cloneDeep(this.edit_pipe)
        _.assign(pipe, attrs)
        this.$store.commit('pipe/UPDATE_EDIT_PIPE', pipe)

        this.saveChanges().then(() => {
          this.show_pipe_properties_dialog = false
        })
      },
      saveSchedule(attrs) {
        attrs = _.pick(attrs, ['schedule', 'deploy_schedule'])

        var pipe = _.cloneDeep(this.edit_pipe)
        _.assign(pipe, attrs)
        this.$store.commit('pipe/UPDATE_EDIT_PIPE', pipe)

        this.saveChanges().then(() => {
          this.show_pipe_schedule_dialog = false
        })
      },
      testPipe() {
        this.show_yaml = false
        this.show_testing = true

        var attrs = _.pick(this.edit_pipe, ['task'])
        var run_cfg = this.process_input

        _.assign(attrs, {
          parent_eid: this.eid,
          process_mode: PROCESS_MODE_BUILD/*,
          run: true // this will automatically run the process and start polling the process
          */
        })

        this.$store.dispatch('v2_action_createProcess', { attrs }).then(response => {
          var process = response.data
          var eid = process.eid
          this.$store.dispatch('v2_action_runProcess', { eid, cfg: run_cfg })
        })

        // make sure we know we've tested the pipe at least once
        this.has_tested_once = true

        this.$store.track('Tested Pipe')
      },
      revert() {
        this.$nextTick(() => {
          // one of the few times we need to do something imperatively
          var input_list = this.$refs['process-input']
          if (input_list && input_list.revert) {
            input_list.revert()
          }
          var editor = this.$refs['code-editor']
          if (editor && editor.revert) {
            editor.revert()
          }
          var task_list = this.$refs['task-list']
          if (task_list && task_list.revert) {
            task_list.revert()
          }
        })
      },
      showYaml(show) {
        this.show_yaml = !!show
        if (!!show) {
          this.$store.track('Opened Pipe Definition')
        } else {
          this.$store.track('Closed Pipe Definition')
        }
      },
      showTesting(show) {
        this.show_testing = !!show
        if (!!show) {
          this.$store.track('Opened Testing Panel')
        } else {
          this.$store.track('Closed Testing Panel')
        }
      },
      initSticky() {
        setTimeout(() => {
          stickybits('.sticky', {
            scrollEl: '#' + this.scrollbar_container_id,
            useStickyClasses: true,
            stickyBitStickyOffset: 0
          })
        }, 500)
      },
      scrollToItem(item_id, timeout) {
        if (_.isString(item_id)) {
          setTimeout(() => {
            this.$scrollTo('#'+item_id, {
              container: '#'+this.scrollbar_container_id,
              duration: 400,
              offset: -80
            })
          }, timeout ? timeout : 10)
        }
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .vertical-panes
    width: 100%
    height: 100%

  .sticky
    margin: 0 -2rem
    padding: 0.5rem 1rem
    border-bottom: 1px solid rgba(0,0,0,0.05)
    transition: all 0.15s ease

  .sticky.js-is-sticky
  .sticky.js-is-stuck
    border-bottom: 1px solid rgba(0,0,0,0.1)
    box-shadow: 0 4px 16px -6px rgba(0,0,0,0.2)
</style>
