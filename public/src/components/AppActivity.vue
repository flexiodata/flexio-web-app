<template>
  <!-- fetching -->
  <div v-if="is_fetching || force_loading">
    <div class="flex flex-column justify-center h-100">
      <Spinner size="large" message="Loading activity..." />
    </div>
  </div>

  <!-- fetched -->
  <div class="flex flex-column overflow-y-auto" :id="doc_id" v-else-if="is_fetched">
    <!-- use `z-7` to ensure the title z-index is greater than the CodeMirror scrollbar -->
    <div class="mt4 mb3 relative z-7 bg-white sticky">
      <div class="center w-100 pa3 pl4-l pr4-l bb bb-0-l b--black-10 sticky" style="max-width: 1152px">
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
      </div>
    </div>

    <!-- list -->
    <ProcessList
      class="center w-100 pl4-l pr4-l pb4-l"
      style="max-width: 1152px"
      :items="output_processes"
      :start="start"
      :limit="page_size"
      @sort-change="sortBy"
    />
  </div>
</template>

<script>
  import stickybits from 'stickybits'
  import { mapState, mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import ProcessList from './ProcessList.vue'

  export default {
    metaInfo: {
      title: 'Activity'
    },
    components: {
      Spinner,
      ProcessList
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
        sort_direction: 'desc'
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
        return true
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
