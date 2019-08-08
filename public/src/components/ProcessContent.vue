<template>
  <transition name="slide-fade" mode="out-in">
    <!-- no content -->
    <div v-if="!processEid || processEid.length == 0">
      <slot name="empty">
        <div class="tc f6">
          <em>There is no content to show.</em>
        </div>
      </slot>
    </div>

    <!-- loading -->
    <div
      class="flex flex-column justify-center"
      v-else-if="is_process_pending || is_process_running || force_loading"
    >
      <Spinner size="large" :message="is_process_pending ? 'Starting...' : 'Running...'" />
    </div>

    <!-- failed -->
    <div v-else-if="is_process_failed">
      <div>
        <IconMessage
          class="ma3 tc"
          title="Oops, looks like something went wrong... :-/"
        />
        <JsonDetailsPanel
          class="ma3"
          :json="process_error"
        />
      </div>
    </div>

    <!-- show content -->
    <div v-else-if="stream_eid.length > 0">
      <StreamContent
        :height="-1"
        :stream-eid="stream_eid"
      />
      <div
        class="mt3 tc"
        v-if="showDownloadButton"
      >
        <a
          class="el-button el-button--primary ttu fw6 no-underline"
          :href="download_url"
        >
          Download
        </a>
      </div>
    </div>

    <div v-else></div>
  </transition>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import { API_ROOT } from '../api/resources'
  import {
    PROCESS_STATUS_PENDING,
    PROCESS_STATUS_RUNNING,
    PROCESS_STATUS_CANCELLED,
    PROCESS_STATUS_FAILED,
    PROCESS_STATUS_COMPLETED
  } from '@/constants/process'

  import Spinner from 'vue-simple-spinner'
  import CodeEditor from '@/components/CodeEditor'
  import IconMessage from '@/components/IconMessage'
  import JsonDetailsPanel from '@/components/JsonDetailsPanel'
  import StreamContent from '@/components/StreamContent'

  export default {
    props: {
      processEid: {
        type: String,
        required: true
      },
      showDownloadButton: {
        type: Boolean,
        default: false
      }
    },
    components: {
      Spinner,
      CodeEditor,
      IconMessage,
      JsonDetailsPanel,
      StreamContent
    },
    watch: {
      processEid: {
        handler: 'fetchProcessLog',
        immediate: true
      },
      process_status(val, old_val) {
        // we just finished running a process; fetch the process log
        if (old_val === PROCESS_STATUS_RUNNING || old_val === PROCESS_STATUS_PENDING) {
          this.fetchProcessLog()
        }
      }
    },
    data() {
      return {
        force_loading: false
      }
    },
    computed: {
      ...mapState({
        active_team_name: state => state.teams.active_team_name
      }),
      is_superuser() {
        // limit to @flex.io users for now
        return _.includes(this.getActiveUserEmail(), '@flex.io')
      },
      process() {
        return _.get(this.$store, 'state.processes.items.' + this.processEid)
      },
      process_status() {
        return _.get(this.process, 'process_status', '')
      },
      process_info() {
        return _.get(this.process, 'process_info', {})
      },
      process_error() {
        var error = _.get(this.process_info, 'error', {})
        return this.is_superuser ? error : _.pick(error, ['code', 'message'])
      },
      is_process_pending() {
        return this.process_status == PROCESS_STATUS_PENDING
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
        return API_ROOT + '/' + this.active_team_name + '/streams/' + this.stream_eid + '/content?download=true'
      }
    },
    methods: {
      ...mapGetters('users', {
        'getActiveUserEmail': 'getActiveUserEmail'
      }),
      fetchProcessLog() {
        var team_name = this.active_team_name
        if (this.processEid.length > 0) {
          this.$store.dispatch('processes/fetchLog', { team_name, eid: this.processEid })
        }
      }
    }
  }
</script>
