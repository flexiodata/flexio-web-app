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
          <router-link
            class="blue"
            :to="getProcessRouteByUser(scope.row)"
            v-if="hasUserEid(scope.row)"
          >
            <div class="truncate">{{getUserName(scope.row)}}</div>
          </router-link>
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
        :label="day.col_label"
        :prop="day.raw"
        :key="day.raw"
        :sortable="true"
        v-for="day in days"
      >
        <template slot-scope="scope">
          <router-link
            class="blue"
            :to="getProcessRouteByUserAndDate(scope.row, day.query_str, day.query_str)"
            v-if="scope.row[day.raw] > 0"
          >
            {{ scope.row[day.raw] }}
          </router-link>
          <div v-else>0</div>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="Total"
        prop="total_count"
        class-name="b"
        :sortable="true"
      >
        <template slot-scope="scope">
          <router-link
            class="blue"
            :to="getProcessRouteByUserTotal(scope.row)"
            v-if="scope.row.total_count > 0"
          >
            {{ scope.row.total_count }}
          </router-link>
          <div v-else>0</div>
        </template>
      </el-table-column>
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
        created_min: '',
        created_max: '',
        res: {}
      }
    },
    mounted() {
      var today = moment()
      var last_week = moment().subtract(6, 'days')
      var created_min = last_week.format('YMMDD')
      var created_max = today.format('YMMDD')
      var url = '/api/v2/admin/info/processes/summary/user?created_min=' + created_min + '&created_max=' + created_max

      this.created_min = created_min
      this.created_max = created_max

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
            query_str: moment(d).format('YMMDD'),
            col_label: moment(d).format('M/D')
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
      },
      getProcessRouteByUser(row) {
        return '/admin/activity?owned_by=' + this.getUserEid(row)
      },
      getProcessRouteByUserAndDate(row, start_date, end_date) {
        return this.getProcessRouteByUser(row) +'&created_min=' + start_date + '&created_max=' + end_date
      },
      getProcessRouteByUserTotal(row) {
        return this.getProcessRouteByUserAndDate(row, this.created_min, this.created_max)
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
