<template>
  <!-- fetching -->
  <div v-if="is_fetching || force_loading">
    <div class="flex flex-column justify-center h-100">
      <Spinner size="large" message="Loading activity..." />
    </div>
  </div>

  <!-- fetched -->
  <div class="flex flex-column overflow-y-auto" v-else-if="is_fetched">
    <!-- control bar -->
    <div class="center w-100 pa3 pa4-l pb3-l bb bb-0-l b--black-10" style="max-width: 1152px">
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
        <div class="f6">{{start}} - {{end}} of {{total_count}}</div>
        <el-pagination
          layout="next"
          :page-size="page_size"
          :current-page.sync="current_page"
          :total="total_count"
          @current-change="updatePager"
        />
      </div>
    </div>

    <!-- list -->
    <ProcessList
      class="center w-100 pl4-l pr4-l pb4-l"
      style="max-width: 1152px"
      :filter-by="filterBy"
      :total-count="total_count"
    />
  </div>
</template>

<script>
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
    data() {
      return {
        force_loading: false,
        filter: '',
        current_page: 1,
        page_size: 50
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
      start() {
        return ((this.current_page - 1) * this.page_size) + 1
      },
      end() {
        return this.start + this.page_size - 1
      },
      total_count() {
        return _.size(this.all_processes)
      }
    },
    mounted() {
      this.tryFetchActivity()
      this.$store.track('Visited Activity Page')
      this.force_loading = true
      setTimeout(() => { this.force_loading = false }, 10)
    },
    methods: {
      ...mapGetters([
        'getAllProcesses'
      ]),
      tryFetchActivity() {
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
      filterBy(item, index) {
        return index >= this.start && index <= this.end
      }
    }
  }
</script>
