<template>
  <div>
    <el-table
      :data="fmt_processes"
      style="width: 100%"
    >
      <el-table-column
        prop="eid"
        label="EID"
      />
      <el-table-column
        prop="fmt_started"
        label="Started"
      />
      <el-table-column
        prop="fmt_finished"
        label="Finished"
      />
      <el-table-column
        prop="fmt_duration"
        label="Duration (in seconds)"
      />
      <el-table-column
        prop="fmt_process_status"
        label="Status"
      >
        <template slot-scope="scope">
          <div class="flex flex-row items-center lh-copy">
            <i class="el-icon-success dark-green mr2" v-if="scope.row.process_status == 'C'"></i>
            <i class="el-icon-error dark-red mr2" v-else-if="scope.row.process_status == 'F'"></i>
            <i class="el-icon-info blue mr2" v-else></i>
            <span>{{scope.row.fmt_process_status}}</span>
          </div>
        </template>
      </el-table-column>
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
        var p = this.getAllProcesses()
        p = _.sortBy(p, [ function(p) { return new Date(p.created) } ])
        return _.reverse(p)
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
