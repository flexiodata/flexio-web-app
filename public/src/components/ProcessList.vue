<template>
  <div>
    <el-table
      class="w-100"
      :data="fmt_processes"
      :default-sort="{ prop: 'fmt_started', order: 'descending' }"
    >
      <el-table-column
        type="index"
      />

      <el-table-column
        prop="eid"
        label="Process ID"
        :min-width="120"
      />
      <el-table-column
        prop="fmt_started"
        label="Started"
        :min-width="140"
        :sortable="true"
        :sort-by="'started'"
      />
      <el-table-column
        prop="fmt_finished"
        label="Finished"
        :min-width="140"
        :sortable="true"
        :sort-by="'finished'"
      />
      <el-table-column
        prop="fmt_process_status"
        label="Status"
        :min-width="120"
        :sortable="true"
      >
        <template slot-scope="scope">
          <div class="flex flex-row items-center lh-copy">
            <i class="el-icon-success dark-green" v-if="scope.row.process_status == 'C'"></i>
            <i class="el-icon-error dark-red" v-else-if="scope.row.process_status == 'F'"></i>
            <i class="el-icon-info blue" v-else></i>
            <span class="ml2">{{scope.row.fmt_process_status}}</span>
          </div>
        </template>
      </el-table-column>
      <el-table-column
        prop="fmt_duration"
        label="Duration"
        align="right"
        :min-width="100"
        :sortable="true"
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
      filterBy: {
        type: Function
      },
      start: {
        type: Number
      },
      limit: {
        type: Number,
        default: 50
      }
    },
    computed: {
      all_processes() {
        var p = this.getAllProcesses()
        p = _.sortBy(p, [ function(p) { return new Date(p.created) } ])
        return _.reverse(p)
      },
      filtered_processes() {
        var processes = this.all_processes
        return this.filterBy ? _.filter(processes, this.filterBy) : processes
      },
      limited_processes() {
        if (!_.isNumber(this.start)) {
          return this.filtered_processes
        }

        return _.filter(this.filtered_processes, (item, index) => {
          return index >= this.start && index < (this.start + this.limit)
        })
      },
      fmt_processes() {
        return _.map(this.limited_processes, (p) => {
          return _.assign({}, p, {
            fmt_started: p.started ? moment(p.started).format('l LT') : '--',
            fmt_finished: p.finished ? moment(p.finished).format('l LT') : '--',
            fmt_duration: p.duration ? p.duration.toFixed(2) + ' seconds' : '--',
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
