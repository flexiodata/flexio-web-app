<template>
  <div class="flex flex-column">
    <!-- control bar -->
    <div class="flex flex-row items-center pa3 relative">
      <input
        type="text"
        class="input-reset ba b--black-20 focus-b--transparent focus-outline focus-ow1 focus-o--blue pa2 w-90 w-50-m w-20-l min-w5-ns f6 mr2"
        placeholder="Filter tests..."
        :disabled="is_running"
        @keydown.esc="filter = ''"
        v-model="filter"
      >
      <btn
        btn-md
        btn-primary
        class="ba"
        @click="runTests"
        v-if="!is_running"
      >
        <span class="ttu b">Run Tests</span>
      </btn>
      <btn
        btn-md
        btn-danger
        class="ba"
        :disabled="is_canceled"
        @click="cancelTests"
        v-if="is_running"
      >
        <span class="ttu b" v-if="is_canceled">Canceling...</span>
        <span class="ttu b" v-else>Cancel</span>
      </btn>
      <toggle-button
        class="ml3 mr1"
        :checked="show_errors_only"
        @click="toggleErrorsOnly"
      ></toggle-button>
      <span class="f5 fw6 pointer black-60" @click.stop="toggleErrorsOnly">Only show errors</span>
      <div class="flex-fill">&nbsp;</div>
      <div class="f5 b pv1 ph3 br b--black-20 yellow">Ajax Errors: {{ajax_fail_cnt}}</div>
      <div class="f5 b pv1 ph3 br b--black-20 dark-green">Passed: {{pass_cnt}}</div>
      <div class="f5 b pv1 ph3 br b--black-20 dark-red">Failed: {{fail_cnt}}</div>
      <div class="f5 b pv1 ph3">Total: {{total_cnt}}</div>
    </div>
    <div class="flex-fill flex flex-column overflow-auto">
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
  import Btn from './Btn.vue'
  import ToggleButton from './ToggleButton.vue'
  import AdminTestItem from './AdminTestItem.vue'
  import CommonFilter from './mixins/common-filter'
  import GetResponseText from './mixins/get-response-text'

  export default {
    mixins: [CommonFilter, GetResponseText],
    components: {
      Btn,
      ToggleButton,
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

        return this.commonFilter(test_arr, this.filter, ['id'])
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

      api.fetchTests().then(response => {
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

        api.runTest({ id: test_id }).then(response => {
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
            var error_text = this.getResponseText(response, (error_text) => {
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
      },
      toggleErrorsOnly() {
        this.show_errors_only = !this.show_errors_only
      }
    }
  }
</script>
