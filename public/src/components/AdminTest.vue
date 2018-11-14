<template>
  <div class="flex flex-column bg-white">
    <!-- control bar -->
    <div class="pa2 relative bb b--black-05">
      <div class="flex flex-row items-center">
        <el-input
          class="w-100 mw5 mr2"
          size="small"
          placeholder="Filter tests..."
          :disabled="is_running"
          @keydown.esc.native="filter = ''"
          v-model="filter"
        />
        <el-button
          type="primary"
          size="small"
          class="ttu fw6"
          @click="runTests"
          v-if="!is_running"
        >
          Run Tests
        </el-button>
        <el-button
          type="danger"
          size="small"
          class="ttu fw6"
          :disabled="is_canceled"
          @click="cancelTests"
          v-if="is_running"
        >
          <span v-if="is_canceled">Canceling...</span>
          <span v-else>Cancel</span>
        </el-button>
        <el-switch
          class="ml3 mr1"
          v-model="show_errors_only"
        />
        <span
          class="f6 fw6 pl1 pointer"
          @click="show_errors_only = !show_errors_only"
        >
          Only show errors
        </span>
        <div class="flex-fill">&nbsp;</div>
        <div class="total-item mr2 br-pill f6 ttu fw6 white bg-yellow" v-show="ajax_fail_cnt > 0">AJAX Errors: {{ajax_fail_cnt}}</div>
        <div class="total-item mr2 br-pill f6 ttu fw6 white bg-dark-green">Passed: {{pass_cnt}}</div>
        <div class="total-item mr2 br-pill f6 ttu fw6 white bg-dark-red">Failed: {{fail_cnt}}</div>
        <div class="total-item mr2 br-pill f6 ttu fw6 white bg-silver">Total: {{total_cnt}}</div>
      </div>
    </div>
    <div class="flex-fill overflow-auto">
      <AdminTestItem
        v-for="(test, index) in filtered_tests"
        :item="test"
        :index="index"
      />
    </div>
  </div>
</template>

<script>
  import api from '../api'
  import AdminTestItem from './AdminTestItem.vue'
  import MixinFilter from './mixins/filter'
  import MixinResponse from './mixins/response'

  export default {
    metaInfo: {
      title: 'Admin Tests'
    },
    mixins: [MixinFilter, MixinResponse],
    components: {
      AdminTestItem
    },
    data() {
      return {
        tests: {},
        filter: '',
        is_canceled: false,
        show_errors_only: false
      }
    },
    computed: {
      filtered_tests() {
        var test_arr = _.values(this.tests)

        if (this.show_errors_only)
          test_arr = _.filter(test_arr, (t) => { return t.passed !== true })

        return this.$_Filter_filter(test_arr, this.filter, ['id'])
      },
      total_cnt() {
        return _
          .chain(this.filtered_tests)
          .map((t) => { return _.get(t, 'test_cnt', 0) })
          .reduce((sum, n) => { return sum + n }, 0)
          .value()
      },
      pass_cnt() {
        return _
          .chain(this.filtered_tests)
          .map((t) => { return _.get(t, 'passed_cnt', 0) })
          .reduce((sum, n) => { return sum + n }, 0)
          .value()
      },
      fail_cnt() {
        return _
          .chain(this.filtered_tests)
          .map((t) => { return _.get(t, 'failed_cnt', 0) })
          .reduce((sum, n) => { return sum + n }, 0)
          .value()
      },
      ajax_fail_cnt() {
        return _
          .chain(this.filtered_tests)
          .filter((t) => { return _.get(t, 'xhr_error') === true })
          .size()
          .value()
      },
      is_running() {
        return _.filter(this.filtered_tests, { is_running: true }).length > 0
      }
    },
    mounted() {
      api.v2_fetchAdminTests().then(response => {
        var tests = _.map(response.data, (id, idx) => {
          return { id, idx }
        })

        // map each object to a key value
        var keyed_tests = _.keyBy(tests, 'id')

        this.tests = keyed_tests
      })
    },
    methods: {
      runTests() {
        this.resetTests()

        // establish our run queue
        this.queue = _(this.filtered_tests)

        // start running the tests
        this.runTest(this.queue.next())
      },
      runTest(queue_item) {
        if (queue_item.done === true || this.is_canceled === true) {
          return
        }

        var test = queue_item.value
        var test_id = _.get(test, 'id', '')

        this.tests[test_id] = _.assign({}, test, { is_running: true })

        api.v2_runAdminTest({ id: test_id }).then(response => {
          var xhr_error = response.status == 200 ? false : true
          this.tests[test_id] = _.assign({}, test, { is_running: false, xhr_error }, response.data)
          this.runTest(this.queue.next())

          /*
          if (!_.isNil(response.data)) {
            this.tests[test_id] = _.assign({}, test, { is_running: false, xhr_error }, response.data)
            this.runTest(this.queue.next())
          } else {
            debugger
            // handle errors here...
            var error_text = this.$_Response_getResponseText(response, (error_text) => {
              xhr_error = false
              this.tests[test_id] = _.assign({}, test, { is_running: false, xhr_error, error_text })
              this.runTest(this.queue.next())
            })
          }
          */
        }).catch(error => {
          var response = _.get(error, 'response', {})
          var error_text = _.get(error, 'message', 'There was a problem with the AJAX call')
          var xhr_error = response.status == 200 ? false : true
          this.tests[test_id] = _.assign({}, test, { is_running: false, xhr_error, error_text })
          this.runTest(this.queue.next())
        })
      },
      resetTests() {
        this.is_canceled = false

        _.each(this.tests, (test) => {
          var test_id = _.get(test, 'id', '')
          this.tests[test_id] = _.assign({ is_running: false }, _.pick(test, ['id','idx']))
        })
      },
      cancelTests() {
        this.is_canceled = true
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .total-item
    padding: 6px 16px
</style>
