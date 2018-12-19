<template>
  <div>
    <el-table
      class="w-100 activity-list-by-user"
      size="small"
      :data="fmt_processes"
      :default-sort="{ prop: 'user.first_name', order: 'ascending' }"
    >
      <el-table-column
        prop="user.eid"
        label="ID"
        fixed
        :width="120"
        :sortable="true"
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
        label="Name"
        fixed
        prop="user.first_name"
        :width="160"
        :sortable="true"
      >
        <template slot-scope="scope">
          <div class="truncate" v-if="hasUserEid(scope.row)">
            {{getUserName(scope.row)}}
          </div>
          <div v-else>--</div>
        </template>
      </el-table-column>
      <el-table-column
        label="Created"
        prop="user.created"
        :width="160"
        :sortable="true"
        :sort-method="sortCreated"
        :formatter="fmtDate"
      />
      <el-table-column
        align="center"
        class-name="narrow"
        :label="day.fmt"
        :prop="day.raw"
        :key="day.raw"
        :sortable="true"
        v-for="day in days"
      />
      <el-table-column
        align="center"
        label="Total"
        prop="total_count"
        class-name="b"
        :sortable="true"
      />
      <div slot="empty">No activity to show</div>
    </el-table>
  </div>
</template>

<script>
  import axios from 'axios'
  import moment from 'moment'


  export default {
    data() {
      return {
        res: {}
      }
    },
    mounted() {
      var today = moment()
      var last_week = moment().subtract(6, 'days')
      var created_min = last_week.format('YYYYMMDD')
      var created_max = today.format('YYYYMMDD')
      var url = '/api/v2/admin/info/processes/summary/user?created_min=' + created_min + '&created_max=' + created_max
      axios.get(url).then(response => {
        this.res = response.data
      }).catch(error => {

      })
    },
    computed: {
      dates() {
        return _.get(this.res, 'header.days', [])
      },
      days() {
        return _.map(this.dates, (d) => {
          return {
            raw: d,
            fmt: moment(d).format('M/D')
          }
        })
      },
      processes() {
        return _.get(this.res, 'detail', [])
      },
      fmt_processes() {
        return _.map(this.processes, (p) => {
          var p2 = _.assign({}, p)
          _.each(this.dates, (d, idx) => {
            p2[d] = p2.daily_count[idx]
          })
          return p2
        })
      },
    },
    methods: {
      getUserName(row) {
        return _.get(row, 'user.first_name', '') + ' ' + _.get(row, 'user.last_name', '')
      },
      getUserEid(row) {
        return _.get(row, 'user.eid', '')
      },
      hasUserEid(row) {
        return _.get(row, 'user.eid', '').length > 0
      },
      sortCreated(a, b) {
        var ax = _.get(a, 'user.created', 0)
        var bx = _.get(b, 'user.created', 0)
        return moment(ax).unix() - moment(bx).unix()
      },
      fmtDate(row, col, val, idx) {
        return val ? moment(val).format('l LT') : '--'
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .activity-list-by-user
    font-size: 13px
    .el-button
      font-size: 13px

    &.el-table
      th.narrow
      td.narrow
        .cell
          padding-left: 4px
          padding-right: 4px
</style>
