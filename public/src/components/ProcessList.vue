<template>
  <div>
    <el-table
      :data="fmt_processes"
      style="width: 100%"
    >
      <el-table-column
        prop="fmt_started"
        label="Started"
        width="240"
      />
      <el-table-column
        prop="fmt_finished"
        label="Finished"
        width="240"
      />
      <el-table-column
        prop="fmt_duration"
        label="Duration (in seconds)"
        width="240"
      />
      <el-table-column
        prop="fmt_process_status"
        label="Status"
      />
    </el-table>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import * as ps from '../constants/process'
  import moment from 'moment'

  const friendlyProcessStatus = (status) => {
    switch (status) {
      case ps.PROCESS_STATUS_PENDING   : return 'Pending'
      case ps.PROCESS_STATUS_WAITING   : return 'Waiting'
      case ps.PROCESS_STATUS_RUNNING   : return 'Running'
      case ps.PROCESS_STATUS_CANCELLED : return 'Canceled'
      case ps.PROCESS_STATUS_PAUSED    : return 'Paused'
      case ps.PROCESS_STATUS_FAILED    : return 'Failed'
      case ps.PROCESS_STATUS_COMPLETED : return 'Completed'
    }

    return ''
  }

  export default {
    props: {
      parentEid: {
        type: String,
         default: ''
      }
    },
    computed: {
      all_processes() {
        return this.getAllProcesses()
      },
      our_processes() {
        if (this.parentEid.length == 0) {
          return this.all_processes
        }

        return _.filter((p) => {
          return _.get(p, 'parent.eid') == this.parentEid
        })
      },
      fmt_processes() {
        return _.map(this.our_processes, (p) => {
          return _.assign({}, p, {
            fmt_started: p.started ? moment(p.started).format('LL hh:mm:ss') : '--',
            fmt_finished: p.finished ? moment(p.finished).format('LL hh:mm:ss') : '--',
            fmt_duration: p.duration ? p.duration.toFixed(4) : '--',
            fmt_process_status: friendlyProcessStatus(p.process_status)
          })
        })
      }
    },
    methods: {
      ...mapGetters([
        'getAllProcesses'
      ])
    }
  }
</script>
