<template>
  <!-- pipe fetching -->
  <div class="bg-nearer-white" v-if="is_fetching">
    <div class="flex flex-column justify-center h-100">
      <Spinner size="large" message="Loading..." />
    </div>
  </div>

  <!-- pipe fetched -->
  <div class="bg-nearer-white" v-else-if="is_fetched">
    <!-- pipe is deployed; runtime view; no ui steps -->
    <div
      class="h-100 pa4 overflow-y-scroll"
      v-if="is_runtime && is_deployed && edit_ui_list.length == 0"
    >
      <div class="mv4 center mw-doc">
        <div class="pa4 bg-white br2 tc css-white-box">
          <IconMessage title="This pipe cannot be run in a browser.">
            If you are the owner of this pipe, please set up a web interface. If this pipe was shared with you, please contact the person who shared it with you to have it set up for your use.
          </IconMessage>
        </div>
      </div>
    </div>

    <!-- runtime view; pipe is deployed -->
    <BuilderDocument
      class="h-100 overflow-y-scroll"
      :definition="edit_pipe"
      v-else-if="is_runtime && is_deployed"
    />

    <!-- runtime view; pipe is not deployed -->
    <div
      class="h-100 pa4 overflow-y-scroll"
      v-else-if="is_runtime && !is_deployed"
    >
      <div class="mv4 center mw-doc">
        <div class="pa4 bg-white br2 tc css-white-box">
          <IconMessage title="This pipe cannot be run in a browser.">
            If you are the owner of this pipe, please turn it on. If this pipe was shared with you, please contact the person who shared it with you to have it turned on.
          </IconMessage>
        </div>
      </div>
    </div>

    <!-- build view -->
    <div class="h-100" v-else>
      <div class="flex flex-row h-100">
        <el-menu
          class="flex-none bg-nearer-white"
          default-active="0"
          :style="{
            width: show_sidebar || is_deployed ? '0' : '49px',
            opacity: show_sidebar || is_deployed ? '0' : '1',
            borderRight: 0
          }"
        >
          <el-menu-item
            index="0"
            @click="showYaml(true)"
          >
            <i class="material-icons nl2 nr2 hint--right" :aria-label="show_yaml ? 'Hide Pipe Definition' : 'Show Pipe Definition'">code</i>
          </el-menu-item>
          <el-menu-item
            index="0"
            @click="showTesting(true)"
          >
            <i class="material-icons nl2 nr2 hint--right" :aria-label="show_testing ? 'Hide Testing Panel' : 'Show Testing Panel'">assignment</i>
          </el-menu-item>
        </el-menu>

        <multipane
          class="flex-fill vertical-panes"
          layout="vertical"
        >
          <div
            class="pane bg-white"
            :class="{
              'trans-a': false //!show_sidebar || transitioning_sidebar
            }"
            :style="{
              maxWidth: '50%',
              minWidth: show_sidebar ? '200px' : '1px',
              width: show_sidebar ? '22%' : '1px',
              marginLeft: show_sidebar ? '0' : '-2px',
              opacity: show_sidebar ? '1' : '0.01',
              boxShadow: '2px 2px 6px rgba(0,0,0,0.1)',
              zIndex: show_sidebar ? 2 : 0
            }"
          >
            <template v-if="show_yaml">
              <div class="flex flex-row items-center bg-nearer-white bb b--black-05 pa2">
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
                <div class="f6 fw6 flex-fill">Testing</div>
                <div class="pointer f5 black-30 hover-black-60 hint--bottom-left" aria-label="Hide Testing" @click="showTesting(false)">
                  <i class="el-icon-close fw6"></i>
                </div>
              </div>

              <div class="flex-fill overflow-y-auto">
                <!-- input panel; visible when pipe is not deployed -->
                <div
                  class="mb4 ph2"
                  name="input"
                  data-tour-step="pipe-onboarding-2"
                  v-if="!is_deployed"
                >
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

                <!-- output panel; visible when pipe is not deployed -->
                <div
                  class="mb4 ph2"
                  name="output"
                  data-tour-step="pipe-onboarding-4"
                  :id="output_item_id"
                  v-if="!is_deployed"
                >
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
          <multipane-resizer
            :class="{
              'no-pointer-events': !show_sidebar
            }"
            :style="{
              zIndex: show_sidebar ? 3 : 0
            }"
          />
          <div
            class="pane pa4 pt0 overflow-y-scroll"
            :id="scrollbar_container_id"
            :style="{ flexGrow: 1 }"
          >
            <PipeDocumentHeader
              class="relative z-7 bg-nearer-white sticky"
              data-tour-step="pipe-onboarding-0"
              :title="title"
              :is-mode-run.sync="is_deployed"
              :show-save-cancel="show_save_cancel"
              @properties-click="openPropertiesDialog"
              @cancel-click="cancelChanges"
              @save-click="saveChanges"
              @run-click="testPipe"
            />

            <!-- run panel; visible when pipe is deployed -->
            <div class="mv4 center mw-doc" v-if="is_deployed">
              <div class="pa4 pt3 bg-white br2 css-white-box">
                <PipeDocumentRunPanel
                  class="tc"
                  :eid="eid"
                  :is-mode-run.sync="is_deployed"
                />
              </div>
            </div>

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
                    <div class="flex flex-row items-center" data-tour-step="pipe-onboarding-1">
                      <span class="f4">Tasks</span>
                      <div class="flex flex-row items-center ml3" @click.stop v-if="is_deployed">
                        <ConfirmPopover
                          class="pointer"
                          style="margin-right: 2px"
                          placement="bottom-start"
                          message="Editing a pipe while it is deployed can have unintended consequences. Are you sure you want to continue?"
                          title="Confirm unlock?"
                          confirmButtonText="Unlock"
                          :width="420"
                          :offset="-50"
                          @confirm-click="toggleLock"
                          v-if="is_locked"
                        >
                          <div class="flex flex-row items-center" slot="reference">
                            <i class="material-icons blue">lock</i>
                          </div>
                        </ConfirmPopover>
                        <div
                          class="flex flex-row items-center"
                          style="display: flex"
                          :class="is_changed ? 'hint--top' : ''"
                          :aria-label="is_changed ? 'Cancel or save changes to the pipe before locking' : ''"
                          v-else
                        >
                          <el-button
                            type="text"
                            slot="reference"
                            style="border: 0; padding: 0; margin-right: 2px"
                            :disabled="is_changed"
                            @click="toggleLock"
                          >
                            <i class="material-icons">lock_open</i>
                          </el-button>
                        </div>
                        <span class="cursor-default" v-if="is_locked">Click the lock to make changes</span>
                        <span class="cursor-default" v-else="is_locked">Click the lock to prevent further changes</span>
                      </div>
                      <span v-if="false" class="ml1 lh-1 hint--bottom hint--large" aria-label="The task list defines the actual logic for the pipe that will be run. Steps can be added either using the interface below or by editing the 'task' node in the YAML sidebar.">
                        <i class="el-icon-info blue"></i>
                      </span>
                    </div>
                  </template>
                  <div class="pt3 ph3">
                    <PipeBuilderList
                      ref="task-list"
                      :class="is_deployed && is_locked ? 'o-40 no-pointer-events no-select' : ''"
                      :container-id="scrollbar_container_id"
                      :has-errors.sync="has_errors"
                      :active-item-idx.sync="active_task_idx"
                      @cancel="cancelChanges"
                      @save="saveChanges"
                      v-model="edit_task_list"
                    />
                    <div data-tour-step="pipe-onboarding-5" class="relative o-0 w3" style="left: 64px; top: -450px"></div>
                  </div>
                </el-collapse-item>

                <!-- deployment panel -->
                <el-collapse-item
                  class="mb4 pv1 ph3 bg-white br2 css-white-box"
                  name="deployment"
                  data-tour-step="pipe-onboarding-6"
                >
                  <template slot="title">
                    <div class="flex flex-row items-center">
                      <span class="f4">Deployment</span>
                      <LabelSwitch
                        class="dib ml3 hint--bottom"
                        style="height: 20px"
                        active-color="#13ce66"
                        :aria-label="is_deployed ? 'Turn pipe off' : 'Turn pipe on'"
                        :width="58"
                        @click.stop
                        v-model="is_deployed"
                      />
                    </div>
                  </template>
                  <div class="pt3 ph3">
                    <PipeDeployPanel
                      :is-mode-run.sync="is_deployed"
                      :pipe="edit_pipe"
                      :show-properties-panel.sync="show_pipe_properties_dialog"
                      :show-runtime-configure-panel.sync="show_runtime_configure_dialog"
                      :show-schedule-panel.sync="show_pipe_schedule_dialog"
                      @updated-deployment="onDeploymentUpdated"
                    />
                  </div>
                </el-collapse-item>
              </el-collapse>
            </div>
          </div>
        </multipane>
      </div>
    </div>

    <!-- pipe properties dialog -->
    <el-dialog
      custom-class="el-dialog--no-header el-dialog--no-footer"
      width="42rem"
      top="8vh"
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

    <!-- pipe runtime configure dialog -->
    <el-dialog
      custom-class="el-dialog--no-header el-dialog--no-footer el-dialog--full-body is-almost-fullscreen"
      :fullscreen="true"
      :modal-append-to-body="false"
      :close-on-click-modal="false"
      :visible.sync="show_runtime_configure_dialog"
    >
      <PipeRuntimeConfigurePanel
        :pipe="edit_pipe"
        @close="show_runtime_configure_dialog = false"
        @cancel="show_runtime_configure_dialog = false"
        @submit="saveRuntime"
        v-if="show_runtime_configure_dialog"
      />
    </el-dialog>

    <!-- pipe schedule dialog -->
    <el-dialog
      custom-class="el-dialog--no-header el-dialog--no-footer"
      width="42rem"
      top="8vh"
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

    <PopperTour
      ref="tour"
      :auto-start="false"
      :steps="tour_steps"
      :callbacks="tour_callbacks"
    />
  </div>

  <!-- pipe not found -->
  <PageNotFound class="bg-nearer-white" v-else-if="pipe_not_found" />
</template>

<script>
  import stickybits from 'stickybits'
  import marked from 'marked'
  import { mapState, mapGetters } from 'vuex'
  import tours from '../data/tour/index-keyed'
  import {
    SCHEDULE_FREQUENCY_FIVE_MINUTES,
    SCHEDULE_DEFAULTS
  } from '../constants/schedule'
  import { PROCESS_MODE_BUILD } from '../constants/process'

  import { Multipane, MultipaneResizer } from 'vue-multipane'
  import Spinner from 'vue-simple-spinner'
  import IconMessage from '@comp/IconMessage'
  import ConfirmPopover from '@comp/ConfirmPopover'
  import ProcessInput from '@comp/ProcessInput'
  import BuilderDocument from '@comp/BuilderDocument'
  import BuilderList from '@comp/BuilderList'
  import PipeBuilderList from '@comp/PipeBuilderList'
  import PipeCodeEditor from '@comp/PipeCodeEditor'
  import PipeDocumentHeader from '@comp/PipeDocumentHeader'
  import PipeDocumentRunPanel from '@comp/PipeDocumentRunPanel'
  import PipePropertiesPanel from '@comp/PipePropertiesPanel'
  import PipeRuntimeConfigurePanel from '@comp/PipeRuntimeConfigurePanel'
  import PipeSchedulePanel from '@comp/PipeSchedulePanel'
  import PipeDeployPanel from '@comp/PipeDeployPanel'
  import ProcessContent from '@comp/ProcessContent'
  import PageNotFound from '@comp/PageNotFound'
  import PopperTour from '@comp/PopperTour'
  import LabelSwitch from '@comp/LabelSwitch'

  import MixinConfig from '@comp/mixins/config'

  const ACTIVE = 'A'
  const INACTIVE = 'I'

  const DEPLOY_MODE_UNDEFINED = ''
  const DEPLOY_MODE_BUILD     = 'B'
  const DEPLOY_MODE_RUN       = 'R'

  const PIPEDOC_VIEW_BUILD  = 'build'
  const PIPEDOC_VIEW_RUN    = 'run'

  const processTourSteps = (arr) => {
    return _.map(arr, (item) => {
      return _.assign(item, {
        title: marked(item.title || ''),
        content: marked(item.content || '')
      })
    })
  }

  const our_tours = {}
  Object.keys(tours).map((key, idx) => {
    our_tours[key] = processTourSteps(tours[key])
  })

  const tour_steps = our_tours['email-results-of-python-function']

  export default {
    metaInfo() {
      return {
        title: _.get(this.orig_pipe, 'name', '')
      }
    },
    mixins: [MixinConfig],
    components: {
      Multipane,
      MultipaneResizer,
      Spinner,
      IconMessage,
      ConfirmPopover,
      ProcessInput,
      BuilderDocument,
      BuilderList,
      PipeBuilderList,
      PipeCodeEditor,
      PipeDocumentHeader,
      PipeDocumentRunPanel,
      PipePropertiesPanel,
      PipeRuntimeConfigurePanel,
      PipeSchedulePanel,
      PipeDeployPanel,
      ProcessContent,
      PageNotFound,
      PopperTour,
      LabelSwitch
    },
    watch: {
      route_identifier: {
        handler: 'loadPipe',
        immediate: true
      },
      active_view: {
        handler: 'updateRoute',
        immediate: true
      },
      is_deployed: {
        handler: 'initStickyAndTour',
        immediate: true
      },
      is_fetched: {
        handler: 'initStickyAndTour',
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
      // add user's first name to the tour
      var this_user = this.getActiveUser()
      var first_name = this_user.first_name
      var first_step = tour_steps[0]
      first_step.title = first_step.title.replace(/{{first_name}}/, first_name)

      return {
        active_view: _.get(this.$route, 'params.view', PIPEDOC_VIEW_BUILD),
        active_collapse_items: ['tasks', 'output', 'deployment'],
        active_ui_idx: 0,
        active_task_idx: -1,
        scrollbar_container_id: _.uniqueId('pane-'),
        output_item_id: _.uniqueId('item-'),
        show_pipe_schedule_dialog: false,
        show_pipe_properties_dialog: false,
        show_runtime_configure_dialog: false,
        yaml_view: 'yaml',
        show_yaml: false,
        show_testing: false,
        transitioning_sidebar: false,
        has_tested_once: false,
        has_errors: false,
        is_saving: false,
        is_locked: true,
        show_save_cancel: false,
        save_cancel_zindex: 2050,
        pipe_not_found: false,
        process_input: {},
        process_data: {},

        tour_started: false,
        tour_current_step: 0,
        tour_steps,

        tour_callbacks: {
          onStart: this.onTourStart,
          onFinish: this.onTourStop,
          onSkip: this.onTourStop,
          onPrevStep: this.onTourPrevStep,
          onNextStep: this.onTourNextStep
        }
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
        var alias = this.edit_pipe.alias
        return alias.length > 0 ? alias : _.get(this.edit_pipe, 'eid', '')
      },
      pipe_schedule() {
        return _.get(this.edit_pipe, 'schedule', {})
      },
      is_runtime() {
        return this.active_view == PIPEDOC_VIEW_RUN
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
            this.is_locked = true
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
      openRuntimeConfigureDialog() {
        this.show_runtime_configure_dialog = true
        this.$store.track('Opened Runtime Configure Dialog')
      },
      openScheduleDialog() {
        this.show_pipe_schedule_dialog = true
        this.$store.track('Opened Schedule Dialog')
      },
      saveProperties(attrs) {
        attrs = _.pick(attrs, ['name', 'description', 'alias'])

        var pipe = _.cloneDeep(this.edit_pipe)
        _.assign(pipe, attrs)
        this.$store.commit('pipe/UPDATE_EDIT_PIPE', pipe)

        this.saveChanges().then(() => {
          this.show_pipe_properties_dialog = false
        })
      },
      saveRuntime(attrs) {
        attrs = _.pick(attrs, ['ui'])

        var pipe = _.cloneDeep(this.edit_pipe)
        _.assign(pipe, attrs)
        this.$store.commit('pipe/UPDATE_EDIT_PIPE', pipe)

        this.saveChanges().then(() => {
          this.show_runtime_configure_dialog = false
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

        // make sure the output is expanded
        this.active_collapse_items = [].concat(this.active_collapse_items).concat(['output'])

        // make sure we know we've tested the pipe at least once
        this.has_tested_once = true

        // scroll to the output item
        //this.scrollToItem(this.output_item_id, 300)

        this.$store.track('Tested Pipe')
      },
      updateRoute() {
        // update the route
        var new_route = _.pick(this.$route, ['name', 'meta', 'params', 'path'])
        var view = this.active_view
        _.set(new_route, 'params.view', view)
        this.$router.replace(new_route)
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
      toggleLock() {
        if (this.is_changed) {
          return
        }

        this.is_locked = !this.is_locked

        if (!this.is_locked) {
          this.active_collapse_items = this.active_collapse_items.concat(['tasks'])
        } else {
          this.active_collapse_items = _.without(this.active_collapse_items, 'tasks')
        }
      },
      initStickyAndTour() {
        if (this.is_deployed) {
          this.active_collapse_items = _.without(this.active_collapse_items, 'tasks')
        } else {
          this.active_collapse_items = this.active_collapse_items.concat(['tasks'])
        }

        setTimeout(() => {
          stickybits('.sticky', {
            scrollEl: '#' + this.scrollbar_container_id,
            useStickyClasses: true,
            stickyBitStickyOffset: 0
          })

          var cfg_path = 'app.prompt.onboarding.pipeDocument.build.shown'

          // for testing
          //this.$_Config_reset(cfg_path)

          if (this.$_Config_get(cfg_path, false) === false) {
            this.$refs['tour'].start()
            this.$_Config_set(cfg_path, true)
          }
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
      },
      onDeploymentUpdated(value) {
        var pipe = _.cloneDeep(this.edit_pipe)
        _.assign(pipe, value)

        // add default scheduling options to the pipe when
        // turning scheduling on for the first time
        if (_.get(pipe, 'deploy_schedule') == ACTIVE) {
          if (_.isNil(_.get(pipe, 'schedule'))) {
            _.set(pipe, 'schedule', SCHEDULE_DEFAULTS)
          }
        }

        // `deploy_api` is required for `deploy_ui` (Google Sheets) right now
        if (_.get(pipe, 'deploy_ui') == ACTIVE) {
          _.set(pipe, 'deploy_api', ACTIVE)
        }

        this.$store.commit('pipe/UPDATE_EDIT_PIPE', pipe)
        this.saveChanges()
      },
      onTourStart() {
        this.tour_current_step = 0
        var current_step = this.tour_current_step
        if (!this.tour_started) {
          this.$store.track('Started Tour', { current_step })
        }
        this.tour_started = true
      },
      onTourStop(callback) {
        var current_step = this.tour_current_step
        var finished = (current_step == this.tour_steps.length - 1) ? true : false
        if (finished) {
          this.$store.track('Finished Tour', { current_step })
        } else {
          this.$store.track('Skipped Tour', { current_step })
        }
        callback(true)
      },
      onTourPrevStep(current_step, callback) {
        this.$store.track('Clicked Tour Previous Button', { current_step })
        this.tour_current_step--
        callback(true)
      },
      onTourNextStep(current_step, callback) {
        this.$store.track('Clicked Tour Next Button', { current_step })

        if (current_step == 1) {
          this.active_collapse_items = ['input', 'tasks', 'output', 'deployment']
          this.process_data = {
            count: 3
          }

          this.tour_current_step++
          setTimeout(() => { callback(true) }, 1)

        } else if (current_step == 3) {
          // moving from output to adding email task
          var this_user = this.getActiveUser()
          var my_email = this_user.email
          var email_item = {
            "to": my_email,
            "subject": "Latest Hacker News Articles via Flex.io",
            "body": "Here's the latest from Hacker News:\n\n${input}\n\n----\n\nThis email was generated using a Flex.io pipe; you may edit it here: " + window.location.href,
            "attachments": [],
            "op": "email"
          }

          var edit_pipe = _.cloneDeep(this.edit_pipe)
          var items = _.get(edit_pipe, 'task.items', [])
          items.push(email_item)

          this.edit_pipe = _.assign({}, edit_pipe)
          this.saveChanges().then(() => {
            this.tour_current_step++
            setTimeout(() => { callback(true) }, 1)
          })
        } else if (current_step == 5) {
          // turn scheduling on and set the frequency to every 5 minutes
          var pipe = _.cloneDeep(this.edit_pipe)

          // activate schedule deployment flag
          _.set(pipe, 'deploy_schedule', ACTIVE)

          // initialize schedule object (defaults)
          if (_.isNil(_.get(pipe, 'schedule'))) {
            _.set(pipe, 'schedule', SCHEDULE_DEFAULTS)
          }

          // set frequency to 5 minutes
          _.set(pipe, 'schedule.frequency', SCHEDULE_FREQUENCY_FIVE_MINUTES)

          this.$store.commit('pipe/UPDATE_EDIT_PIPE', pipe)
          this.saveChanges().then(() => {
            this.tour_current_step++
            setTimeout(() => { callback(true) }, 1)
          })
        } else {
          this.tour_current_step++
          callback(true)
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
    border-left: 1px solid rgba(0,0,0,0.05)

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
