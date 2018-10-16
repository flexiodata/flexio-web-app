<template>
  <div>
    <el-table
      class="w-100"
      :data="limited_processes"
      :default-sort="{ prop: 'started', order: 'descending' }"
      @sort-change="onSortChange"
    >
      <el-table-column
        type="index"
        :index="getIndex"
        v-if="has_start"
      />

      <el-table-column
        prop="eid"
        label="Process ID"
        :min-width="120"
      >
        <template slot-scope="scope">
          <span class="code f7 bg-white br2 ba b--black-05" style="padding: 3px 6px">{{scope.row.eid}}</span>
        </template>
      </el-table-column>
      <el-table-column
        prop="started"
        label="Started"
        :min-width="140"
        :sortable="true"
        :formatter="fmtDate"
      />
      <el-table-column
        prop="finished"
        label="Finished"
        :min-width="140"
        :sortable="true"
        :formatter="fmtDate"
      />
      <el-table-column
        prop="process_status"
        label="Status"
        :min-width="120"
        :sortable="true"
      >
        <template slot-scope="scope">
          <div class="flex flex-row items-center lh-copy">
            <i class="el-icon-success dark-green" v-if="scope.row.process_status == 'C'"></i>
            <i class="el-icon-error dark-red" v-else-if="scope.row.process_status == 'F'"></i>
            <i class="el-icon-info blue" v-else></i>
            <span class="ml2">{{fmtProcessStatus(scope.row.process_status)}}</span>
          </div>
        </template>
      </el-table-column>
      <el-table-column
        prop="duration"
        label="Duration"
        align="right"
        :min-width="100"
        :sortable="true"
        :formatter="fmtDuration"
      />
      <div slot="empty">No activity to show</div>
    </el-table>
  </div>
</template>

<script>
  import moment from 'moment'
  import { mapGetters } from 'vuex'
  import * as ps from '../constants/process'

  export default {
    props: {
      items: {
        type: Array
      },
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
        if (_.isArray(this.items)) {
          return this.items
        }

        return this.getAllProcesses()
      },
      filtered_processes() {
        var processes = this.all_processes
        return this.filterBy ? _.filter(processes, this.filterBy) : processes
      },
      limited_processes() {
        if (!_.isNumber(this.start)) {
          return this.filtered_processes
        }

        return _.filter(this.filtered_processes, (item, idx) => {
          return idx >= this.start && idx < (this.start + this.limit)
        })
      },
      has_start() {
        return _.isNumber(this.start)
      }
    },
    methods: {
      ...mapGetters([
        'getAllProcesses'
      ]),
      fmtDate(row, col, val, idx) {
        return val ? moment(val).format('l LT') : '--'
      },
      fmtDuration(row, col, val, idx) {
        return val ? val.toFixed(2) + ' seconds' : '--'
      },
      fmtProcessStatus: (status) => {
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
      },
      getIndex(idx) {
        return idx + this.start + 1
      },
      onSortChange(obj) {
        this.$emit('sort-change', obj)
      }
    }
  }
</script>
