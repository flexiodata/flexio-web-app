<template>
  <div>
    <div
      class="bg-white ba b--black-10 flex flex-column justify-center"
      style="height: 300px"
      v-if="is_process_running"
    >
      <Spinner size="large" message="Running..." />
    </div>
    <div
      v-else-if="stream_eid.length > 0 && !is_process_failed"
    >
      <StreamContent
        :height="300"
        :stream-eid="stream_eid"
      />
      <div class="mt4 mb2 tc">
        <a
          class="el-button el-button--primary ttu b no-underline"
          :href="download_url"
        >
          Download
        </a>
      </div>
    </div>
    <div
      v-else-if="is_superuser && is_process_failed"
    >
      <CodeEditor
        class="bg-white ba b--black-10"
        lang="json"
        :options="{
          minRows: 12,
          maxRows: 24,
          lineNumbers: false,
          readOnly: true
        }"
        v-model="process_info_str"
      />
    </div>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import { API_V2_ROOT } from '../api/resources'
  import {
    PROCESS_STATUS_RUNNING,
    PROCESS_STATUS_FAILED
  } from '../constants/process'

  import Spinner from 'vue-simple-spinner'
  import CodeEditor from './CodeEditor.vue'
  import StreamContent from './StreamContent.vue'

  export default {
    props: {
      processEid: {
        type: String,
        required: true
      }
    },
    components: {
      Spinner,
      CodeEditor,
      StreamContent
    },
    watch: {
      processEid: {
        handler: 'fetchProcessLog',
        immediate: true
      },
      process_status(val, old_val) {
        // we just finished running a process; fetch the process log
        if (old_val === PROCESS_STATUS_RUNNING) {
          this.fetchProcessLog()
        }
      }
    },
    computed: {
      is_superuser() {
        // limit to @flex.io users for now
        var user_email = _.get(this.getActiveUser(), 'email', '')
        return _.includes(user_email, '@flex.io')
      },
      process() {
        return _.get(this.$store, 'state.objects.' + this.processEid)
      },
      process_status() {
        return _.get(this.process, 'process_status', '')
      },
      process_info() {
        return _.get(this.process, 'process_info', {})
      },
      process_info_str() {
        return JSON.stringify(this.process_info, null, 2)
      },
      is_process_running() {
        return this.process_status == PROCESS_STATUS_RUNNING
      },
      is_process_failed() {
        return this.process_status == PROCESS_STATUS_FAILED
      },
      process_log() {
        var log = _.get(this.process, 'log', [])
        return _.last(log)
      },
      stream_eid() {
        return _.get(this.process_log, 'output.stdout.eid', '')
      },
      download_url() {
        return API_V2_ROOT + '/me/streams/' + this.stream_eid + '/content?download=true'
      }
    },
    methods: {
      ...mapGetters([
        'getActiveUser'
      ]),
      fetchProcessLog() {
        if (this.processEid.length > 0) {
          this.$store.dispatch('fetchProcessLog', { eid: this.processEid })
        }
      },
    }
  }
</script>
