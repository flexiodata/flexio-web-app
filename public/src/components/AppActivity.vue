<template>
  <!-- fetching -->
  <div v-if="is_fetching || force_loading">
    <div class="flex flex-column justify-center h-100">
      <Spinner size="large" message="Loading activity..." />
    </div>
  </div>

  <!-- fetched -->
  <div class="flex flex-column overflow-y-scroll" :id="doc_id" v-else-if="is_fetched">
    <!-- use `z-7` to ensure the title z-index is greater than the CodeMirror scrollbar -->
    <div class="mt4 relative z-7 bg-white sticky">
      <div class="center w-100 pa3 pl4-l pr4-l bb bb-0-l b--black-10 sticky" style="max-width: 1280px">
        <!-- control bar -->
        <div class="flex flex-row items-center">
          <div class="flex-fill flex flex-row items-center">
            <h1 class="mv0 f2 fw4 mr3">Activity</h1>
          </div>
          <el-pagination
            layout="prev"
            :page-size="page_size"
            :current-page.sync="current_page"
            :total="total_count"
            @current-change="updatePager"
          />
          <div class="f6">{{start + 1}} - {{end}} of {{total_count}}</div>
          <el-pagination
            layout="next"
            :page-size="page_size"
            :current-page.sync="current_page"
            :total="total_count"
            @current-change="updatePager"
          />
        </div>
        <div class="flex flex-row justify-end items-center mt2">
          <el-select
            style="width: 150px"
            placeholder="Status"
            clearable
            v-model="status_filter"
          >
            <el-option
              :label="option.label"
              :value="option.val"
              :key="option.val"
              v-for="option in status_options"
            >
            <div class="flex flex-row items-center">
              <i class="el-icon-success dark-green" v-if="option.val == 'C'"></i>
              <i class="el-icon-warning dark-red" v-else-if="option.val == 'F'"></i>
              <i class="el-icon-error dark-red" v-else-if="option.val == 'X'"></i>
              <i class="el-icon-loading blue" v-else-if="option.val == 'R'"></i>
              <i class="el-icon-info blue" v-else></i>
              <span class="ml2">{{option.label}}</span>
            </div>
            </el-option>
          </el-select>
          <el-date-picker
            class="ml2"
            style="width: 240px"
            type="daterange"
            align="right"
            format="MM/dd/yyyy"
            start-placeholder="Start date"
            end-placeholder="End date"
            :default-time="['00:00:00', '23:59:59']"
            v-model="date_range"
          />
        </div>
      </div>
    </div>

    <!-- list -->
    <ProcessList
      class="center w-100 pl4-l pr4-l pb4-l"
      style="max-width: 1280px; padding-bottom: 8rem""
      :items="output_processes"
      :start="start"
      :limit="page_size"
      @sort-change="sortBy"
      @details-click="openProcessDetailsDialog"
    />

    <!-- process details dialog -->
    <el-dialog
      custom-class="el-dialog--no-header el-dialog--no-footer"
      width="56rem"
      top="8vh"
      :modal-append-to-body="false"
      :visible.sync="show_process_details_dialog"
    >
      <ProcessDetailsPanel
        :process-eid="edit_process_eid"
        @close="show_process_details_dialog = false"
      />
    </el-dialog>
  </div>
</template>

<script>
  import moment from 'moment'
  import stickybits from 'stickybits'
  import { mapState, mapGetters } from 'vuex'
  import * as ps from '../constants/process'
  import Spinner from 'vue-simple-spinner'
  import ProcessList from './ProcessList.vue'
  import ProcessDetailsPanel from './ProcessDetailsPanel.vue'

  const fmtProcessStatus = (status) => {
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

  const statuses = [
    ps.PROCESS_STATUS_COMPLETED,
    ps.PROCESS_STATUS_FAILED,
    ps.PROCESS_STATUS_PENDING,
    ps.PROCESS_STATUS_RUNNING,
    ps.PROCESS_STATUS_CANCELLED
  ]

  const status_options = _.map(statuses, (val) => {
    return { label: fmtProcessStatus(val), val }
  })

  export default {
    metaInfo: {
      title: 'Activity'
    },
    components: {
      Spinner,
      ProcessList,
      ProcessDetailsPanel
    },
    watch: {
      is_fetched: {
        handler: 'initSticky',
        immediate: true
      }
    },
    data() {
      return {
        doc_id: _.uniqueId('app-activity-'),
        force_loading: false,
        filter: '',
        current_page: 1,
        page_size: 50,
        sort: 'started',
        sort_direction: 'desc',
        date_range: null,
        status_filter: '',
        status_options,
        show_process_details_dialog: false,
        edit_process_eid: ''
      }
    },
    computed: {
      // mix this into the outer object with the object spread operator
      ...mapState({
        'is_fetching': 'processes_fetching',
        'is_fetched': 'processes_fetched'
      }),
      all_processes() {
        return this.getAllProcesses()
      },
      filtered_processes() {
        return _.filter(this.all_processes, this.filterBy)
      },
      output_processes() {
        return _.orderBy(this.filtered_processes, [this.sort], [this.sort_direction])
      },
      total_count() {
        return _.size(this.output_processes)
      },
      start() {
        return ((this.current_page - 1) * this.page_size)
      },
      end() {
        return Math.min((this.start + this.page_size - 1) + 1, this.total_count)
      }
    },
    mounted() {
      this.initSticky()
      this.tryFetchProcesses()
      this.$store.track('Visited Activity Page')
      this.force_loading = true
      setTimeout(() => { this.force_loading = false }, 10)
    },
    methods: {
      ...mapGetters([
        'getAllProcesses'
      ]),
      tryFetchProcesses() {
        if (!this.is_fetched && !this.is_fetching) {
          this.$store.dispatch('fetchProcesses')
        }
      },
      updatePager(page) {
        // update the route
        var new_route = _.pick(this.$route, ['name', 'meta', 'params', 'path'])
        _.set(new_route, 'query.page', page)
        this.$router.replace(new_route)
      },
      sortBy({ column, prop, order }) {
        this.current_page = 1
        this.sort = prop
        this.sort_direction = order == 'descending' ? 'desc' : 'asc'
      },
      filterBy(item, index) {
        var res = true

        if (_.isArray(this.date_range)) {
          if (_.isNil(item.started) || _.isNil(item.finished)) {
            return false
          }

          var start = this.date_range[0]
          var end = this.date_range[1]

          res = res && moment(item.started) > moment(start) && moment(item.finished) < moment(end)
        }

        if (this.status_filter.length > 0) {
          res = res && item.process_status == this.status_filter
        }

        return res
      },
      openProcessDetailsDialog(eid) {
        this.edit_process_eid = eid
        this.show_process_details_dialog = true
      },
      initSticky() {
        setTimeout(() => {
          stickybits('.sticky', {
            scrollEl: '#' + this.doc_id,
            useStickyClasses: true
          })
        }, 100)
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .sticky
    transition: all 0.15s ease

  .sticky.js-is-sticky
  .sticky.js-is-stuck
    border-bottom: 1px solid rgba(0,0,0,0.1)
    box-shadow: 0 4px 16px -6px rgba(0,0,0,0.2)
</style>
