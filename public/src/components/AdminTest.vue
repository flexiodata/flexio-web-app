<template>
  <div class="flex flex-column">
    <!-- control bar -->
    <div class="pa2 relative bg-white bb b--black-05" style="margin-top: 2px">
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
          class="ttu b"
          @click="runTests"
          v-if="!is_running"
        >
          Run Tests
        </el-button>
        <el-button
          type="danger"
          size="small"
          class="ttu b"
          :disabled="is_canceled"
          @click="cancelTests"
          v-if="is_running"
        >
          <span v-if="is_canceled">Canceling...</span>
          <span v-else>Cancel</span>
        </el-button>
        <el-switch
          class="ml3 mr1"
          active-color="#009900"
          v-model="show_errors_only"
        />
        <span
          class="f5 pl1 pointer"
          @click="show_errors_only = !show_errors_only"
        >
          Only show errors
        </span>
        <div class="flex-fill">&nbsp;</div>
        <div class="f6 ttu b ph2 yellow" v-show="ajax_fail_cnt > 0">AJAX Errors: {{ajax_fail_cnt}}</div>
        <span class="moon-gray" v-show="ajax_fail_cnt > 0">/</span>
        <div class="f6 ttu b ph2 dark-green">Passed: {{pass_cnt}}</div>
        <span class="moon-gray">/</span>
        <div class="f6 ttu b ph2 dark-red">Failed: {{fail_cnt}}</div>
        <span class="moon-gray">/</span>
        <div class="f6 ttu b ph2">Total: {{total_cnt}}</div>
      </div>
    </div>
    <div class="flex-fill overflow-auto">
      <admin-test-item
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
  import MixinFilter from './mixins/common-filter'
  import MixinResponse from './mixins/response'

  export default {
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
          .filter((t) => { return _.get(t, 'xhr.ok') === false })
          .size()
          .value()
      },
      is_running() {
        return _.filter(this.filtered_tests, { is_running: true }).length > 0
      }
    },
    mounted() {
      var me = this

      api.fetchAdminTests().then(response => {
        var tests = _.map(response.body, (id, idx) => {
          return { id, idx }
        })

        // map each object to a key value
        var keyed_tests = _.keyBy(tests, 'id')

        this.tests = keyed_tests
      })
    },
    methods: {
      runTests() {
        var me = this

        this.resetTests()

        // establish our run queue
        this.queue = _(this.filtered_tests)

        // start running the tests
        this.runTest(this.queue.next())
      },
      runTest(queue_item) {
        if (queue_item.done === true || this.is_canceled === true)
          return

        var test = queue_item.value
        var test_id = _.get(test, 'id', '')

        this.tests[test_id] = _.assign({}, test, { is_running: true })

        api.runAdminTest({ id: test_id }).then(response => {
          var xhr = _.pick(response, ['ok', 'status', 'statusText'])

          if (!_.isNil(response.body))
          {
            this.tests[test_id] = _.assign({}, test, { is_running: false, xhr }, response.body)
            this.runTest(this.queue.next())
          }
           else
          {
            var me = this

            // handle errors here...
            var error_text = this.$_Response_getResponseText(response, (error_text) => {
              xhr.ok = false
              me.tests[test_id] = _.assign({}, test, { is_running: false, xhr, error_text })
              me.runTest(this.queue.next())
            })
          }
        }, response => {
          var xhr = _.pick(response, ['ok', 'status', 'statusText'])
          var error_text = 'There was a problem with the AJAX call'
          this.tests[test_id] = _.assign({}, test, { is_running: false, xhr, error_text })
          this.runTest(this.queue.next())
        })
      },
      resetTests() {
        var me = this
        this.is_canceled = false

        _.each(this.tests, (test) => {
          var test_id = _.get(test, 'id', '')
          me.tests[test_id] = _.assign({ is_running: false }, _.pick(test, ['id','idx']))
        })
      },
      cancelTests() {
        this.is_canceled = true
      }
    }
  }
</script>
