<template>
  <div>
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
        class="bg-white ba b--black-10 flex flex-column justify-center"
        style="height: 300px"
        v-else-if="is_process_pending || is_process_running || force_loading"
      >
        <Spinner size="large" :message="is_process_pending ? 'Starting...' : 'Running...'" />
      </div>
      <!-- failed -->
      <div
        class="bg-white ba b--black-10 pa4 overflow-y-auto"
        style="max-height: 600px"
        v-else-if="is_process_failed"
      >
        <IconMessage
          class="tc"
          title="Oops, looks like something went wrong... :-/"
        />
        <JsonDetailsPanel :json="process_error" />
      </div>
      <!-- show content -->
      <div v-else-if="stream_eid.length > 0">
        <StreamContent
          :height="300"
          :stream-eid="stream_eid"
        />
        <div class="mt3 tc">
          <a
            class="el-button el-button--primary ttu b no-underline"
            :href="download_url"
          >
            Download
          </a>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import { API_V2_ROOT } from '../api/resources'
  import {
    PROCESS_STATUS_PENDING,
    PROCESS_STATUS_RUNNING,
    PROCESS_STATUS_CANCELLED,
    PROCESS_STATUS_FAILED,
    PROCESS_STATUS_COMPLETED
  } from '../constants/process'

  import Spinner from 'vue-simple-spinner'
  import CodeEditor from './CodeEditor.vue'
  import IconMessage from './IconMessage.vue'
  import JsonDetailsPanel from './JsonDetailsPanel.vue'
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
        return API_V2_ROOT + '/me/streams/' + this.stream_eid + '/content?download=true'
      }
    },
    methods: {
      ...mapGetters([
        'getActiveUser'
      ]),
      fetchProcessLog() {
        if (this.processEid.length > 0) {
          this.$store.dispatch('v2_action_fetchProcessLog', { eid: this.processEid })
        }
      }
    }
  }
</script>
