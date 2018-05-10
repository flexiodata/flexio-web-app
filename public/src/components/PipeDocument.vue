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
      class="mt4 mb3 nl4 nr4 relative z-7 bg-nearer-white sticky"
      v-if="is_fetched"
    >
      <div class="ph4">
        <div
          class="flex flex-row items-center center"
          style="max-width: 1440px"
        >
          <h1 class="flex-fill mv0 pv3 fw6 mid-gray">{{title}}</h1>
          <div class="flex-none flex flex-row items-center pl2">
            <div class="mh2" v-if="false">
              <el-button
                type="text"
                class="ml4"
                @click="show_pipe_schedule_dialog = true"
              >
                <div class="hint--bottom" aria-label="Scheduling options">
                  <div class="flex flex-row items-center gray hover-black">
                    <i class="material-icons mr1">date_range</i> <span class="ttu fw6 f7 dn db-l">Schedule</span>
                  </div>
                </div>
              </el-button>
            </div>
            <div class="mh2" v-if="false">
              <el-button
                type="text"
                class="ml4"
                @click="show_pipe_deploy_dialog = true"
              >
                <div class="hint--bottom" aria-label="Deployment options">
                  <div class="flex flex-row items-center gray hover-black">
                    <i class="material-icons mr1">archive</i> <span class="ttu fw6 f7 dn db-l">Deploy</span>
                  </div>
                </div>
              </el-button>
            </div>
            <transition name="el-zoom-in-top">
              <div class="flex flex-row pl3" v-if="is_changed">
                <el-button
                  size="medium"
                  class="ttu b"
                  @click="cancelChanges"
                >
                  Cancel
                </el-button>
                <el-button
                  size="medium"
                  type="primary"
                  class="ttu b"
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
      <div class="mb4 ph4 pv2 bg-white br2 css-dashboard-box">
        <el-collapse class="el-collapse-plain" v-model="collapse_properties">
          <el-collapse-item name="properties">
            <template slot="title"><h3 class="mv0 fw6 f4 mid-gray">Properties</h3></template>
            <PipeDocumentForm class="mt3" />
          </el-collapse-item>
        </el-collapse>
      </div>

      <div class="mb4 ph4 pv2 bg-white br2 css-dashboard-box">
        <el-collapse class="el-collapse-plain" v-model="collapse_configuration">
          <el-collapse-item name="configuration">
            <template slot="title">
              <div class="flex flex-row items-center">
                <h3 class="flex-fill mv0 fw6 f4 mid-gray">Configuration</h3>
                <div class="flex flex-row items-center mr3">
                  <el-button
                    class="ttu b"
                    style="width: 5rem"
                    type="primary"
                    size="small"
                    @click.stop="runPipe"
                  >
                    Run
                  </el-button>
                </div>
              </div>
            </template>
            <CodeEditor
              class="mt3 bg-white ba b--black-10 overflow-y-auto"
              lang="javascript"
              :options="{ minRows: 12, maxRows: 24 }"
              v-model="edit_code"
            />
            <div
              class="mt3 bg-white ba b--black-10 flex flex-column justify-center"
              style="height: 300px"
              v-if="is_process_running"
            >
              <Spinner size="large" message="Running pipe..." />
            </div>
            <div
              v-else-if="last_stream_eid.length > 0 && !is_process_failed"
            >
              <PipeContent
                class="mt3"
                :height="300"
                :stream-eid="last_stream_eid"
              />
            </div>
            <div
              v-else-if="is_superuser && is_process_failed"
            >
              <CodeEditor
                class="mt3 bg-white ba b--black-10 overflow-y-auto"
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
          </el-collapse-item>
        </el-collapse>
      </div>
    </div>

    <!-- pipe deploy dialog -->
    <el-dialog
      custom-class="no-header no-footer"
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
  import PipeDocumentForm from './PipeDocumentForm.vue'
  import PipeDeployPanel from './PipeDeployPanel.vue'
  import PipeContent from './PipeContent.vue'

  export default {
    components: {
      Spinner,
      CodeEditor,
      PipeDocumentForm,
      PipeDeployPanel,
      PipeContent
    },
    watch: {
      eid: {
        handler: 'loadPipe',
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
        show_pipe_schedule_dialog: false,
        show_pipe_deploy_dialog: false,
        collapse_properties: ['properties'],
        collapse_configuration: ['configuration']
      }
    },
    computed: {
      ...mapState({
        edit_pipe: state => state.pipe.edit_pipe,
        is_fetching: state => state.pipe.fetching,
        is_fetched: state => state.pipe.fetched
      }),
      eid() {
        return _.get(this.$route, 'params.eid', undefined)
      },
      doc_id() {
        return 'pipe-doc-' + this.eid
      },
      orig_pipe() {
        return this.getOriginalPipe()
      },
      orig_code() {
        // do this until we fix the object ref issue in the Flex.io JS SDK
        var task_obj = _.get(this.orig_pipe, 'task', {})
        task_obj = _.cloneDeep(task_obj)
        return Flexio.pipe(task_obj).toCode()
      },
      edit_code: {
        get () {
          return this.$store.state.pipe.edit_code
        },
        set (value) {
          this.$store.commit('pipe/UPDATE_CODE', value)
        }
      },
      title() {
        return _.get(this.orig_pipe, 'name', '')
      },
      is_code_changed() {
        return this.edit_code != this.orig_code
      },
      is_changed() {
        var keys = ['name', 'alias', 'description', 'task', 'schedule', 'schedule_status']
        var pipe1 = _.pick(this.edit_pipe, keys)
        var pipe2 = _.pick(this.orig_pipe, keys)
        return this.is_code_changed || !_.isEqual(pipe1, pipe2)
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
        'getOriginalPipe',
      ]),
      ...mapGetters([
        'getActiveDocumentProcesses',
        'getActiveUser'
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
          if (response.ok) {
            this.$nextTick(() => { this.is_running = false })
          }
        })
      },
      cancelChanges() {
        this.$store.commit('pipe/UPDATE_EDIT_PIPE', this.orig_pipe)
      },
      saveChanges() {
        var keys = ['name', 'alias', 'description', 'schedule', 'schedule_status', 'task']
        var eid = _.get(this.edit_pipe, 'eid', '')
        var attrs = _.pick(this.edit_pipe, keys)

        // don't POST null values
        attrs = _.omitBy(attrs, (val, key) => { return _.isNil(val) })

        return this.$store.dispatch('updatePipe', { eid, attrs }).then(response => {
          if (response.ok) {
            this.$store.commit('pipe/UPDATE_EDIT_PIPE', response.body)
          } else {
            // TODO: add error handling
          }
        })
      },
      fetchProcessLog() {
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
    transition: all 0.2s ease

  .sticky.js-is-sticky
    box-shadow: 0 4px 24px -8px rgba(0,0,0,0.2)
</style>
