<template>
  <div>
    <el-table
      class="w-100 activity-list-by-user"
      size="small"
      :data="fmt_processes"
    >
      <el-table-column
        label="User"
        width="200"
        fixed
        :sortable="true"
      >
        <template slot-scope="scope">
          <span v-if="hasUserEid(scope.row)">
            {{getUserName(scope.row)}}
            <span class="f8 code bg-white br2 ba b--black-10" style="padding: 1px 3px">
              {{getUserEid(scope.row)}}
            </span>
          </span>
          <span v-else>--</span>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
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
            fmt: moment(d).format('ddd')
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
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .activity-list-by-user
    font-size: 13px
    .el-button
      font-size: 13px
</style>
