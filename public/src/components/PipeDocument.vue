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
          class="pane pa4 pv5 overflow-y-scroll"
          :id="scrollbar_container_id"
          :style="{
            minWidth: '800px',
            flexGrow: show_sidebar ? undefined : 1
          }"
        >
          <div
            class="w-100 center mw-doc pa4 bg-white br2 css-white-box"
            style="min-height: 20rem"
          >
            <PipeDocumentHeader
              :pipe="orig_pipe"
              :is-mode-run.sync="is_deployed"
              :show-save-cancel="show_save_cancel"
              :show-test-panel="show_result_sidebar"
              @properties-click="openPropertiesDialog"
              @cancel-click="cancelChanges"
              @save-click="saveChanges"
              @run-click="testPipe"
            />
            <div class="h2"></div>
            <PipeBuilderList
              ref="task-list"
              :container-id="scrollbar_container_id"
              :has-errors.sync="has_errors"
              :active-item-idx.sync="active_task_idx"
              @cancel="cancelChanges"
              @save="saveChanges"
              v-model="edit_task_list"
              v-require-rights:pipe.update
            />
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
          <div class="flex flex-column h-100" v-if="show_result_sidebar">
            <!-- input panel header -->
            <div class="flex-none flex flex-row items-center bg-nearer-white bb b--black-05 pa2">
              <div class="f6 fw6">{{show_input_parameters ? 'Input Parameters' : 'Result'}}</div>
              <el-button
                type="text"
                size="small"
                style="padding: 2px 0 0; margin-left: 8px"
                :class="show_input_parameters ? '' : 'hint--bottom'"
                :aria-label="show_input_parameters ? '' : 'Test this function with POST parameters'"
                @click="show_input_parameters = !show_input_parameters"
              >
                {{show_input_parameters ? 'Hide' : 'Edit input parameters'}}
              </el-button>
              <div class="flex-fill"></div>
              <div
                class="pointer f5 black-30 hover-black-60 hint--bottom-left"
                aria-label="Close test panel"
                @click="showTesting(false)"
              >
                <i class="el-icon-close fw6"></i>
              </div>
            </div>

            <div class="flex-fill flex flex-column">
              <!-- input panel -->
              <div
                class="flex-none pa3 bb b--black-05 overflow-y-auto"
                style="max-height: 17rem"
                v-show="show_input_parameters"
              >
                <ProcessInput
                  ref="process-input"
                  v-model="process_input"
                  :process-data.sync="process_data"
                />
              </div>

              <!-- output panel header -->
              <div
                class="flex-none flex flex-row items-center bg-nearer-white bb b--black-05 pa2"
                v-show="show_input_parameters"
              >
                <div class="f6 fw6">Result</div>
              </div>

              <!-- output panel -->
              <ProcessContent
                class="flex-fill flex flex-column"
                :process-eid="active_process_eid"
              >
                <!-- don't show any empty text -->
                <div class="pa3 tc f6 lh-copy" slot="empty"></div>
              </ProcessContent>
            </div>
          </div>
        </div>
      </multipane>
    </div>

    <!-- function edit dialog -->
    <el-dialog
      custom-class="el-dialog--no-header el-dialog--no-footer"
      width="46rem"
      top="4vh"
      :modal-append-to-body="false"
      :close-on-click-modal="false"
      :visible.sync="show_pipe_edit_dialog"
    >
      <PipeEditPanel
        mode="edit"
        :pipe="orig_pipe"
        @close="show_pipe_edit_dialog = false"
        @cancel="show_pipe_edit_dialog = false"
        @submit="saveProperties"
      />
    </el-dialog>

    <!-- function schedule dialog -->
    <el-dialog
      custom-class="el-dialog--no-header el-dialog--no-footer"
      width="46rem"
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

  <!-- function not found -->
  <PageNotFound class="bg-nearer-white" v-else-if="pipe_not_found" />
</template>

<script>
  import { PROCESS_MODE_BUILD } from '@/constants/process'
  import { mapState, mapGetters } from 'vuex'
  import { Multipane, MultipaneResizer } from 'vue-multipane'
  import stickybits from 'stickybits'
  import Spinner from 'vue-simple-spinner'
  import PipeBuilderList from '@/components/PipeBuilderList'
  import PipeDocumentHeader from '@/components/PipeDocumentHeader'
  import PipeEditPanel from '@/components/PipeEditPanel'
  import PipeSchedulePanel from '@/components/PipeSchedulePanel'
  import ProcessInput from '@/components/ProcessInput'
  import ProcessContent from '@/components/ProcessContent'
  import PageNotFound from '@/components/PageNotFound'

  const ACTIVE = 'A'
  const INACTIVE = 'I'

  const DEPLOY_MODE_UNDEFINED = ''
  const DEPLOY_MODE_BUILD     = 'B'
  const DEPLOY_MODE_RUN       = 'R'

  const getInitialState = () => {
    return {
      active_task_idx: -1,
      active_process_eid: '',
      scrollbar_container_id: _.uniqueId('pane-'),
      show_pipe_schedule_dialog: false,
      show_pipe_edit_dialog: false,
      show_result_sidebar: false,
      show_input_parameters: false,
      has_errors: false,
      is_saving: false,
      show_save_cancel: false,
      save_cancel_zindex: 2050,
      pipe_not_found: false,
      process_input: {},
      process_data: {}
    }
  }

  export default {
    components: {
      Multipane,
      MultipaneResizer,
      Spinner,
      PipeBuilderList,
      PipeDocumentHeader,
      PipeEditPanel,
      PipeSchedulePanel,
      ProcessInput,
      ProcessContent,
      PageNotFound
    },
    watch: {
      route_object_name: {
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
      }
    },
    data() {
      return getInitialState()
    },
    computed: {
      ...mapState({
        orig_pipe: state => state.apppipes.orig_pipe,
        edit_keys: state => state.apppipes.edit_keys,
        is_fetching: state => state.apppipes.is_fetching,
        is_fetched: state => state.apppipes.is_fetched,
        is_changed: state => state.apppipes.is_changed,
        active_team_name: state => state.teams.active_team_name
      }),
      route_object_name() {
        return _.get(this.$route, 'params.object_name', undefined)
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
          var pipe = _.get(this.$store.state.apppipes, 'edit_pipe', {})
          return pipe
        },
        set(value) {
          try {
            var pipe = _.cloneDeep(value)
            this.$store.commit('apppipes/UPDATE_EDIT_PIPE', pipe)
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
            this.$store.commit('apppipes/UPDATE_EDIT_PIPE', pipe)
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
            this.$store.commit('apppipes/UPDATE_EDIT_PIPE', pipe)
          }
          catch(e)
          {
            // TODO: add error handling
          }
        }
      },
      pipe_schedule() {
        return _.get(this.edit_pipe, 'schedule', {})
      },
      show_sidebar() {
        return this.show_result_sidebar
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
            this.$store.commit('apppipes/UPDATE_EDIT_PIPE', pipe)
            this.saveChanges()
          }

          if (value === false) {
            this.$confirm('This function is turned on and is possibly being used in a production environment. Are you sure you want to continue?', 'Really turn function off?', {
              confirmButtonClass: 'ttu fw6',
              cancelButtonClass: 'ttu fw6',
              confirmButtonText: 'Turn function off',
              cancelButtonText: 'Cancel',
              type: 'warning'
            }).then(() => {
              doSet()
              this.$store.track("Turned Function Off")
            }).catch(() => {
              this.$store.track("Turned Function Off (Canceled)")
            })
          } else {
            doSet()
            this.$store.track("Turned Function On")
          }
        }
      }
    },
    methods: {
      loadPipe() {
        this.$store.commit('apppipes/FETCHING_PIPE', true)

        // reset our local component data
        _.assign(this.$data, getInitialState())

        var team_name = this.active_team_name
        var name = this.route_object_name

        this.$store.dispatch('pipes/fetch', { team_name, name }).then(response => {
          var pipe = response.data
          this.pipe_not_found = false
          this.$store.commit('apppipes/INIT_PIPE', pipe)
        }).catch(error => {
          this.pipe_not_found = true
        }).finally(() => {
          this.$store.commit('apppipes/FETCHING_PIPE', false)
        })
      },
      cancelChanges() {
        this.$store.commit('apppipes/INIT_PIPE', this.orig_pipe)
        this.revert()
        this.active_task_idx = -1
      },
      saveChanges() {
        this.is_saving = true

        var eid = this.eid
        var attrs = _.pick(this.edit_pipe, this.edit_keys)
        var team_name = this.active_team_name

        // don't POST null values
        attrs = _.omitBy(attrs, (val, key) => { return _.isNil(val) })

        return this.$store.dispatch('pipes/update', { team_name, eid, attrs }).then(response => {
          var pipe = response.data

          this.$message({
            message: 'The function was updated successfully.',
            type: 'success'
          })

          // change the object name in the route
          if (pipe.name != this.orig_pipe.name) {
            var object_name = pipe.name

            var new_route = _.pick(this.$route, ['name', 'meta', 'params', 'path'])
            new_route.params = _.assign({}, new_route.params, { object_name })
            this.$router.replace(new_route)
          }

          this.$store.commit('apppipes/INIT_PIPE', pipe)
          this.revert()
        }).catch(error => {
          this.$message({
            message: 'There was a problem updating the function.',
            type: 'error'
          })
        }).finally(() => {
          this.active_task_idx = -1
        })
      },
      openPropertiesDialog() {
        this.show_pipe_edit_dialog = true
        this.$store.track('Opened Function Properties Dialog')
      },
      openScheduleDialog() {
        this.show_pipe_schedule_dialog = true
        this.$store.track('Opened Function Schedule Dialog')
      },
      saveProperties(attrs) {
        attrs = _.pick(attrs, ['name', 'title', 'syntax', 'description'])
        this.$store.commit('apppipes/UPDATE_EDIT_PIPE', attrs)

        this.saveChanges().finally(() => {
          this.show_pipe_edit_dialog = false
        })
      },
      saveSchedule(attrs) {
        attrs = _.pick(attrs, ['schedule', 'deploy_schedule'])

        var pipe = _.cloneDeep(this.edit_pipe)
        _.assign(pipe, attrs)
        this.$store.commit('apppipes/UPDATE_EDIT_PIPE', pipe)

        this.saveChanges().then(() => {
          this.show_pipe_schedule_dialog = false
        })
      },
      testPipe() {
        this.show_result_sidebar = true

        var team_name = this.active_team_name
        var attrs = _.pick(this.edit_pipe, ['task'])
        var cfg = this.process_input

        _.assign(attrs, {
          parent_eid: this.eid,
          process_mode: PROCESS_MODE_BUILD
        })

        this.$store.dispatch('processes/create', { team_name, attrs }).then(response => {
          var process = response.data
          var eid = process.eid
          this.active_process_eid = eid
          this.$store.dispatch('processes/run', { team_name, eid, cfg })
        })

        this.$store.track('Tested Function')
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
      showTesting(show) {
        this.show_result_sidebar = !!show
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
    padding: 1rem 2rem /* match container horizontal padding */
    border-bottom: 1px solid transparent
    transition: all 0.15s ease

  .sticky.js-is-sticky
  .sticky.js-is-stuck
    border-bottom: 1px solid rgba(0,0,0,0.1)
    box-shadow: 0 4px 16px -6px rgba(0,0,0,0.2)
</style>
