<template>
  <div>
    <el-table
      class="w-100 activity-list"
      size="small"
      :data="limited_processes"
      :default-sort="{ prop: 'started', order: 'descending' }"
      @sort-change="onSortChange"
    >
      <el-table-column
        prop="owned_by.eid"
        label="User ID"
        fixed
        v-if="showUser"
      >
        <template slot-scope="scope">
          <span
            class="code bg-white br2 ba b--black-10" style="padding: 3px 6px"
            v-if="hasUserEid(scope.row)"
          >
            {{getUserEid(scope.row)}}
          </span>
          <span v-else>--</span>
        </template>
      </el-table-column>
      <el-table-column
        prop="parent.name"
        label="Name"
        fixed
        :min-width="160"
      >
        <template slot-scope="scope">
          <em class="light-silver" v-if="!hasPipeEid(scope.row)">
            (Anonymous)
          </em>
          <router-link class="i light-silver" :to="getPipeRoute(scope.row)" v-else-if="!hasPipeName(scope.row)">
            (No name)
          </router-link>
          <router-link class="blue" :to="getPipeRoute(scope.row)" v-else>
            <div class="truncate">{{scope.row.parent.name}}</div>
          </router-link>
        </template>
      </el-table-column>
      <el-table-column
        prop="triggered_by"
        label="Trigger"
        :width="160"
        :sortable="true"
      >
        <template slot-scope="scope">
          <div class="flex flex-row items-center lh-copy">
            <i class="material-icons md-20">{{getTriggerIcon(scope.row.triggered_by, scope.row.process_mode)}}</i>
            <span class="ml1">{{getTriggerText(scope.row.triggered_by, scope.row.process_mode)}}</span>
          </div>
        </template>
      </el-table-column>

      <el-table-column
        prop="process_status"
        label="Status"
        :width="140"
        :sortable="true"
      >
        <template slot-scope="scope">
          <div class="flex flex-row items-center lh-copy">
            <i class="el-icon-success dark-green" v-if="scope.row.process_status == 'C'"></i>
            <i class="el-icon-warning dark-red" v-else-if="scope.row.process_status == 'F'"></i>
            <i class="el-icon-error dark-red" v-else-if="scope.row.process_status == 'X'"></i>
            <i class="el-icon-loading blue" v-else-if="scope.row.process_status == 'R'"></i>
            <i class="el-icon-info blue" v-else></i>
            <span class="ml1">{{getProcessText(scope.row.process_status)}}</span>
          </div>
        </template>
      </el-table-column>
      <el-table-column
        prop="started"
        label="Started (UTC)"
        :width="160"
        :sortable="true"
        :formatter="fmtDate"
      />
      <el-table-column
        prop="finished"
        label="Finished (UTC)"
        :width="160"
        :sortable="true"
        :formatter="fmtDate"
      />
      <el-table-column
        prop="duration"
        label="Duration"
        align="right"
        :width="115"
        :sortable="true"
        :formatter="fmtDuration"
      />
      <el-table-column
        align="right"
        :width="85"
      >
        <template slot-scope="scope">
          <el-button
            type="text"
            size="small"
            style="padding: 0"
            @click="$emit('details-click', scope.row.eid)"
          >
            Details
          </el-button>
        </template>
      </el-table-column>
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
        type: Number,
        default: 0
      },
      limit: {
        type: Number,
        default: 50
      },
      showUser: {
        type: Boolean,
        default: false
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
      getProcessText(status) {
        switch (status) {
          case ps.PROCESS_STATUS_PENDING   : return 'Pending'
          case ps.PROCESS_STATUS_WAITING   : return 'Waiting'
          case ps.PROCESS_STATUS_RUNNING   : return 'Running'
          case ps.PROCESS_STATUS_CANCELLED : return 'Canceled'
          case ps.PROCESS_STATUS_PAUSED    : return 'Paused'
          case ps.PROCESS_STATUS_FAILED    : return 'Failed'
          case ps.PROCESS_STATUS_COMPLETED : return 'Completed'
        }

        return '--'
      },
      getTriggerIcon(triggered_by, process_mode) {
        switch (triggered_by) {
          case ps.PROCESS_TRIGGERED_API       : return 'code'
          case ps.PROCESS_TRIGGERED_EMAIL     : return 'email'
          case ps.PROCESS_TRIGGERED_SCHEDULER : return 'schedule'
          case ps.PROCESS_TRIGGERED_INTERFACE :
            if (process_mode == ps.PROCESS_MODE_BUILD) {
              return 'storage'
            } else if (process_mode == ps.PROCESS_MODE_RUN) {
              return 'offline_bolt'
            }
        }

        return ''
      },
      getTriggerText(triggered_by, process_mode) {
        switch (triggered_by) {
          case ps.PROCESS_TRIGGERED_API       : return 'API Endpoint'
          case ps.PROCESS_TRIGGERED_EMAIL     : return 'Email'
          case ps.PROCESS_TRIGGERED_SCHEDULER : return 'Scheduler'
          case ps.PROCESS_TRIGGERED_INTERFACE :
            if (process_mode == ps.PROCESS_MODE_BUILD) {
              return 'Builder Test'
            } else if (process_mode == ps.PROCESS_MODE_RUN) {
              return 'Web Interface'
            }
        }

        return '--'
      },
      getUserEid(row) {
        return _.get(row, 'owned_by.eid', '')
      },
      hasUserEid(row) {
        return _.get(row, 'owned_by.eid', '').length > 0
      },
      hasPipeEid(row) {
        return _.get(row, 'parent.eid', '').length > 0
      },
      hasPipeName(row) {
        return _.get(row, 'parent.name', '').length > 0
      },
      getPipeRoute(row) {
        var name = _.get(row, 'parent.name', '')
        var identifier = name.length > 0 ? name : _.get(row, 'parent.eid', '')
        return `/pipes/${identifier}`
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

<style lang="stylus" scoped>
  .activity-list
    font-size: 13px
    .el-button
      font-size: 13px
</style>
