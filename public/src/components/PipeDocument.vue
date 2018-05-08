<template>
  <div
    class="bg-nearer-white ph4 pb4 overflow-y-auto relative"
    :id="doc_id"
  >
    <div
      class="h-100 flex flex-row items-center justify-center"
      v-if="is_fetching"
    >
      <Spinner size="large" message="Loading..." />
    </div>
    <div
      class="center"
      style="max-width: 1024px; margin-bottom: 6rem"
      v-else-if="is_fetched"
    >
      <!-- use `z-7` to ensure the title z-index is greater than the CodeMirror scrollbar -->
      <div class="flex flex-row items-center mv3 relative z-7 bg-nearer-white sticky">
        <h1 class="flex-fill mv0 pv3 fw6 mid-gray">{{title}}</h1>

        <el-button
          class="ttu b"
          type="plain"
          size="medium"
          v-if="false"
        >
          Cancel
        </el-button>
        <el-button
          class="ttu b"
          type="primary"
          size="medium"
          @click="runPipe"
        >
          Run
        </el-button>
      </div>
      <div class="pa4 bg-white br2 css-dashboard-box">
        <div class="flex flex-column justify-center mv5" v-if="is_process_running">
          <Spinner size="large" message="Running pipe..." />
        </div>
        <div v-else>
          <!-- header form -->
          <h3 class="mt0 mb3 fw6 mid-gray">Properties</h3>
          <PipeDocumentForm />

          <!-- code editor -->
          <h3 class="mt4 mb3 fw6 mid-gray">Configuration</h3>
          <CodeEditor
            class="ba b--black-10 overflow-y-auto"
            lang="javascript"
            :options="{ minRows: 12, maxRows: 24 }"
            v-model="code"
          />

          <!-- error -->
          <div
            class="mt3"
            v-if="is_process_failed && is_superuser"
          >
            <h3 class="mt4 mb3 fw6 mid-gray">Output</h3>
            <div class="bg-white ba b--black-10">
              <CodeEditor
                class="overflow-y-auto"
                style="max-height: 24rem"
                lang="application/json"
                :options="{
                  lineNumbers: false,
                  readOnly: true
                }"
                v-model="active_process_info_str"
              />
            </div>
          </div>

          <!-- preview -->
          <div
            class="mt3 relative"
            v-if="last_stream_eid.length > 0 && !is_process_failed"
          >
            <PipeContent
              :stream-eid="last_stream_eid"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import stickybits from 'stickybits'
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
  import PipeContent from './PipeContent.vue'

  export default {
    components: {
      Spinner,
      CodeEditor,
      PipeDocumentForm,
      PipeContent
    },
    watch: {
      eid: {
        handler: 'loadPipe',
        immediate: true
      },
      is_fetched() {
        if (!this.is_fetched)
          return

        setTimeout(() => { stickybits('.sticky') }, 100)
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
      title() {
        return _.get(this.orig_pipe, 'name', '')
      },
      code: {
        get () {
          return this.$store.state.pipe.edit_code
        },
        set (value) {
          this.$store.commit('pipe/UPDATE_CODE', value)
        }
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
      last_subprocess() {
        var log = _.get(this.active_process, 'log', [])
        return _.last(log)
      },
      last_stream_eid() {
        return _.get(this.last_subprocess, 'output.stdout.eid', '')
      },
      is_superuser() {
        // limit to @flex.io users for now
        var user_email = _.get(this.getActiveUser(), 'email', '')
        return _.includes(user_email, '@flex.io')
      },
    },
    mounted() {
      setTimeout(() => { stickybits('.sticky') }, 100)
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
          code: this.code
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
      }
    }
  }
</script>
