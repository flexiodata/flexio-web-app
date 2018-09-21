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
      class="h-100 pa4 pt0 overflow-y-scroll"
      :id="scrollbar_container_id"
      v-else-if="is_pipe_mode_run"
    >
      <PipeDocumentHeader
        class="relative z-7 bg-nearer-white sticky"
        :title="title"
        :is-mode-run.sync="is_pipe_mode_run"
      />
      <div class="mt5 mb4 center mw-doc">
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
      <div class="flex flex-row h-100">
        <el-menu
          class="flex-none bg-nearer-white trans-a"
          default-active="0"
          :style="{
            width: show_yaml ? '0' : '49px'
          }"
        >
          <el-menu-item
            index="0"
            @click="show_yaml = !show_yaml"
          >
            <i class="material-icons nl2 nr2 hint--right" :aria-label="show_yaml ? 'Hide Pipe Definition' : 'Show Pipe Definition'">code</i>
          </el-menu-item>
        </el-menu>

        <multipane
          class="flex-fill vertical-panes"
          layout="vertical"
        >
          <div
            class="pane"
            :class="{
              'trans-a': !show_yaml || transitioning_yaml
            }"
            :style="{
              maxWidth: '50%',
              minWidth: show_yaml ? '100px' : '1px',
              width: show_yaml ? '20%' : '1px',
              marginLeft: show_yaml ? '0' : '-2px',
              opacity: show_yaml ? '1' : '0.01'
            }"
          >
            <div class="flex flex-row items-center bg-nearer-white bb b--black-10 pa2">
              <div class="f6 fw6 flex-fill">Pipe Definition</div>
              <div class="pointer f5 black-30 hover-black-60 hint--bottom-left" aria-label="Hide Pipe Definition" @click="show_yaml = false">
                <i class="el-icon-close fw6"></i>
              </div>
            </div>
            <PipeCodeEditor
              class="h-100"
              ref="code-editor"
              type="yaml"
              editor-cls="bg-white h-100"
              :class="{
                'no-pointer-events': !show_yaml
              }"
              :task-only="false"
              :has-errors.sync="has_errors"
              @save="saveChanges"
              v-model="edit_pipe"
            />
          </div>
          <multipane-resizer
            :class="{
              'no-pointer-events': !show_yaml
            }"
          />
          <div
            class="pane pa4 pt0 overflow-y-scroll"
            :id="scrollbar_container_id"
            :style="{ flexGrow: 1 }"
          >
            <PipeDocumentHeader
              class="relative z-7 bg-nearer-white sticky"
              data-v-step="pipe-onboarding-0"
              :title="title"
              :is-mode-run.sync="is_pipe_mode_run"
              :show-save-cancel="show_save_cancel"
              @schedule-click="show_pipe_schedule_dialog = true"
              @properties-click="show_pipe_properties_dialog = true"
              @cancel-click="cancelChanges"
              @save-click="saveChanges"
              @run-click="testPipe"
            />
            <div class="mv4 center mw-doc" style="padding-bottom: 8rem">
              <el-collapse class="el-collapse--plain" v-model="active_collapse_items">
                <el-collapse-item
                  class="mb4 pv1 ph3 bg-white br2 css-white-box"
                  name="input"
                >
                  <template slot="title">
                    <div class="flex flex-row items-center">
                      <span class="f4">Input</span>
                      <span v-if="false" class="ml1 lh-1 hint--bottom hint--large" aria-label="An optional web interface that can be used in a runtime enviroment to prompt users for parameters to use when running the pipe. Interface elements can be added by editing the 'ui' node in the YAML sidebar.">
                        <i class="el-icon-info blue"></i>
                      </span>
                    </div>
                  </template>
                  <div class="pt3 ph3">
                    <ProcessInput
                      ref="process-input"
                      v-model="edit_input"
                    />
                  </div>
                  <div class="pt3 ph3" v-if="false">
                    <BuilderList
                      builder-mode="wizard"
                      :items="edit_ui_list"
                      :container-id="scrollbar_container_id"
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
                <el-collapse-item
                  class="mb4 pv1 ph3 bg-white br2 css-white-box"
                  name="tasks"
                  data-v-step="pipe-onboarding-1"
                >
                  <template slot="title">
                    <div class="flex flex-row items-center">
                      <span class="f4">Tasks</span>
                      <span v-if="false" class="ml1 lh-1 hint--bottom hint--large" aria-label="The task list defines the actual logic for the pipe that will be run. Steps can be added either using the interface below or by editing the 'task' node in the YAML sidebar.">
                        <i class="el-icon-info blue"></i>
                      </span>
                    </div>
                  </template>
                  <div class="pt3 ph3">
                    <PipeBuilderList
                      ref="task-list"
                      :container-id="scrollbar_container_id"
                      :has-errors.sync="has_errors"
                      :active-item-idx.sync="active_task_idx"
                      data-v-step="pipe-onboarding-2"
                      @cancel="cancelChanges"
                      @save="saveChanges"
                      v-model="edit_task_list"
                    />
                    <div data-v-step="pipe-onboarding-5"></div>
                  </div>
                </el-collapse-item>
                <el-collapse-item
                  class="mb4 pv1 ph3 bg-white br2 css-white-box"
                  name="output"
                  data-v-step="pipe-onboarding-3"
                  :id="output_item_id"
                >
                  <template slot="title">
                    <div class="flex flex-row items-center">
                      <span class="f4">Output</span>
                      <span v-if="false" class="ml1 lh-1 hint--bottom hint--large" aria-label="The output panel shows the output of the pipe after it has been run.">
                        <i class="el-icon-info blue"></i>
                      </span>
                    </div>
                  </template>
                  <div class="pt3 ph3">
                    <ProcessContent :process-eid="active_process_eid">
                      <div class="tc f6" slot="empty">
                        <em>Click the <code class="ph1 ba b--black-10 bg-near-white br2">Test</code> button to see the result of your pipe logic here.</em>
                      </div>
                    </ProcessContent>
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
      top="8vh"
      :modal-append-to-body="false"
      :visible.sync="show_pipe_schedule_dialog"
    >
      <PipeSchedulePanel
        :pipe="edit_pipe"
        @close="show_pipe_schedule_dialog = false"
        @cancel="show_pipe_schedule_dialog = false"
        @submit="saveSchedule"
      />
    </el-dialog>

    <v-tour
      name="pipe-document-build-tour"
      :options="{
        useKeyboardNavigation: false
      }"
      :steps="tour_steps"
      :callbacks="tour_callbacks"
    />
  </div>
</template>

<script>
  import stickybits from 'stickybits'
  import { mapState, mapGetters } from 'vuex'
  import { PROCESS_MODE_BUILD } from '../constants/process'

  import { Multipane, MultipaneResizer } from 'vue-multipane'
  import Spinner from 'vue-simple-spinner'
  import IconMessage from './IconMessage.vue'
  import ProcessInput from './ProcessInput.vue'
  import BuilderDocument from './BuilderDocument.vue'
  import BuilderList from './BuilderList.vue'
  import PipeBuilderList from './PipeBuilderList.vue'
  import PipeCodeEditor from './PipeCodeEditor.vue'
  import PipeDocumentHeader from './PipeDocumentHeader.vue'
  import PipeDocumentRunPanel from './PipeDocumentRunPanel.vue'
  import PipePropertiesPanel from './PipePropertiesPanel.vue'
  import PipeSchedulePanel from './PipeSchedulePanel.vue'
  import ProcessContent from './ProcessContent.vue'
  import MixinConfig from './mixins/config'

  const PIPE_MODE_UNDEFINED = ''
  const PIPE_MODE_BUILD     = 'B'
  const PIPE_MODE_RUN       = 'R'

  const PIPEDOC_VIEW_BUILD  = 'build'
  const PIPEDOC_VIEW_RUN    = 'run'

  export default {
    mixins: [MixinConfig],
    components: {
      Multipane,
      MultipaneResizer,
      Spinner,
      IconMessage,
      ProcessInput,
      BuilderDocument,
      BuilderList,
      PipeBuilderList,
      PipeCodeEditor,
      PipeDocumentHeader,
      PipeDocumentRunPanel,
      PipePropertiesPanel,
      PipeSchedulePanel,
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
        this.transitioning_yaml = true
        setTimeout(() => { this.transitioning_yaml = false }, 150)
      }
    },
    data() {
      return {
        active_view: _.get(this.$route, 'params.view', PIPEDOC_VIEW_BUILD),
        active_collapse_items: ['input', 'tasks', 'output'],
        active_ui_idx: 0,
        active_task_idx: -1,
        scrollbar_container_id: _.uniqueId('pane-'),
        output_item_id: _.uniqueId('item-'),
        show_pipe_schedule_dialog: false,
        show_pipe_properties_dialog: false,
        show_yaml: false,
        transitioning_yaml: false,
        has_run_once: false,
        has_errors: false,
        is_saving: false,
        show_save_cancel: false,
        save_cancel_zindex: 2050,
        process_input: {},

        tour_steps: [
          {
            target: '[data-v-step="pipe-onboarding-0"]',
            content: '<div class="tl mv3"><div class="b mb1">Step 1 of 6:</div>Here\'s a two-minute tour to help you get started with Flex.io.</div>',
            header: {
              title: 'Welcome to Flex.io!'
            },
            params: {
              placement: 'none'
            }
          },
          {
            target: '[data-v-step="pipe-onboarding-1"]',
            content: '<div class="tl mb3"><div class="b mb1">Step 2 of 6:</div>Pipes can execute functions and related tasks. New tasks can be added by clicking on the plus button.</div>',
            params: {
              placement: 'left'
            }
          },
          {
            target: '[data-v-step="pipe-onboarding-2"]',
            content: '<div class="tl mb3"><div class="b mb1">Step 3 of 6:</div>Click on the "Test" button to run this pipe.</div>'
          },
          {
            target: '[data-v-step="pipe-onboarding-3"]',
            content: '<div class="tl mb3"><div class="b mb1">Step 4 of 6:</div>And here\'s the pipe output.</div>',
            params: {
              placement: 'left'
            }
          },
          {
            target: '[data-v-step="pipe-onboarding-5"]',
            content: '<div class="tl mb3"><div class="b mb1">Step 5 of 6:</div>Now we\'ve added a new task to your pipe. The output from the execute task is passed to the input of this email task.<br><br>Now test your pipe again and check your inbox.</div>',
            params: {
              placement: 'left'
            }
          },
          {
            target: '[data-v-step="pipe-onboarding-6"]',
            content: '<div class="tl mb3"><div class="b mb1">Step 6 of 6:</div>Finally, click the "Schedule" button and set the pipe to run every five minutes.</div>'
          },
          {
            target: '[data-v-step="pipe-onboarding-7"]',
            content: '<div class="tl mb3"><div class="b mb1">Thanks for checking out Flex.io!</div>Click the pipe list to see other examples or create your own pipes.</div>'
          }
        ],

        tour_callbacks: {
          onNextStep: this.onTourNextStepCallback
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
      eid() {
        return _.get(this.$route, 'params.eid', undefined)
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
      ...mapGetters([
        'getActiveUser',
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
        this.revert()
        this.active_task_idx = -1
      },
      saveChanges() {
        this.is_saving = true

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
            this.revert()
          } else {
            this.$message({
              message: 'There was a problem updating the pipe.',
              type: 'error'
            })
          }

          this.active_task_idx = -1
        })
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
      saveSchedule(attrs) {
        attrs = _.pick(attrs, ['schedule', 'schedule_status'])

        var pipe = _.cloneDeep(this.edit_pipe)
        _.assign(pipe, attrs)
        this.$store.commit('pipe/UPDATE_EDIT_PIPE', pipe)

        this.saveChanges().then(() => {
          this.show_pipe_schedule_dialog = false
        })
      },
      testPipe() {
        var attrs = _.pick(this.edit_pipe, ['task'])
        var run_attrs = _.get(this.edit_pipe, 'ui.input', {})

        _.assign(attrs, {
          parent_eid: this.eid,
          process_mode: PROCESS_MODE_BUILD/*,
          run: true // this will automatically run the process and start polling the process
          */
        })

        this.$store.dispatch('createProcess', { attrs }).then(response => {
          var eid = response.body.eid
          this.$store.dispatch('runProcess', { eid, attrs: run_attrs })
        })

        // make sure the output is expanded
        this.active_collapse_items = [].concat(this.active_collapse_items).concat(['output'])

        // scroll to the output item
        this.scrollToItem(this.output_item_id, 300)
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
      initStickyAndTour() {
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
            this.$tours['pipe-document-build-tour'].start()
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
                offset: -32
            })
          }, timeout ? timeout : 10)
        }
      },
      onTourNextStepCallback(current_step) {
        // moving from output to adding email task
        if (current_step == 3) {
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

          setTimeout(() => {
            this.edit_pipe = _.assign({}, edit_pipe)
            this.saveChanges()
          }, 500)
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

  /*
  nl4 nr4 pv2 ph3
  nl4 nr4 pv2 ph3
  */
  .sticky
    margin: 0 -2rem
    padding: 0.5rem 1.5rem
    border-bottom: 1px solid rgba(0,0,0,0.05)
    transition: all 0.15s ease

  .sticky.js-is-sticky
  .sticky.js-is-stuck
    border-bottom: 1px solid rgba(0,0,0,0.1)
    box-shadow: 0 4px 16px -6px rgba(0,0,0,0.2)
</style>
