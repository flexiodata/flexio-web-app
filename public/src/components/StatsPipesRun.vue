<template>
  <div v-if="is_fetching">
    <div class="flex flex-row justify-center items-center min-h4 pa3">
      <spinner size="medium"></spinner>
      <span class="ml2 f5">Loading...</span>
    </div>
  </div>
  <div class="pa3" v-else>
    <div class="f3 ma0 pb2 mb3" v-if="title.length > 0">{{title}}</div>
    <line-chart
      :height="chartHeight"
      :labels="labels"
      :datasets="datasets"
      :options="{
        legend: {
          display: false
        },
        layout: {
          padding: {
            // any unspecified dimensions are assumed to be 0
            left: 16
          }
        },
        scales: {
          yAxes: [{
            ticks: {
              beginAtZero: true
            }
          }]
        }
      }"
    ></line-chart>
  </div>
</template>

<script>
  import moment from 'moment'
  import Spinner from 'vue-simple-spinner'
  import LineChart from './LineChart.vue'

  export default {
    props: {
      'title': {
        type: String,
        default: ''
      },
      'chart-height': {
        type: Number,
        default: 300
      },
      // overrides duration if specified
      'start-date': {
        type: String,
        required: false
      },
      // defaults to today
      'end-date': {
        type: String,
        required: false
      },
      'duration': {
        type: Object,
        default: () => {
          return {
            number: 1,
            type: 'months'
          }
        }
      },
      'is-admin': {
        type: Boolean,
        default: false
      }
    },
    components: {
      Spinner,
      LineChart
    },
    computed: {
      is_fetching() {
        var lookup = 'state.statistics_fetching.'
        lookup += this.isAdmin ? 'admin-processes': 'processes'
        return _.get(this.$store, lookup, false)
      },
      is_fetched() {
        var lookup = 'state.statistics_fetched.'
        lookup += this.isAdmin ? 'admin-processes': 'processes'
        return _.get(this.$store, lookup, false)
      },
      end_date() {
        var end = _.isString(this.endDate) ? moment(this.endDate).utc() : moment().utc()
        return end.endOf('day')
      },
      start_date() {
        var start = _.isString(this.startDate) ? moment(this.startDate).utc() : moment(this.end_date).utc().subtract(this.duration.number, this.duration.type)
        return start.startOf('day')
      },
      baseline_vals() {
        // create a starting array of '0' values for the specified period
        var iter = moment(this.start_date).utc().startOf('day')
        var arr = []

        while (iter.isSameOrBefore(this.end_date))
        {
          arr.push({
            created: moment(iter),
            total_count: 0
          })
          iter = iter.add(1, 'days')
        }

        return arr
      },
      labels() {
        return _.map(this.baseline_vals, (s) => {
          return _.get(s, 'created').format('MMM D')
        })
      },
      store_stats() {
        var lookup = 'state.statistics.'
        lookup += this.isAdmin ? 'admin-processes': 'processes'
        return _.get(this.$store, lookup, [])
      },
      stats_with_created() {
        // add a moment date to each stat
        return _.map(this.store_stats, (s) => {
          return _.assign(s, {
            created: moment(_.get(s, 'process_created')).utc().startOf('day')
          })
        })
      },
      datasets() {
        // group each stat by date
        var stats = _.groupBy(this.stats_with_created, (s) => {
          return _.get(s, 'process_created', '')
        })

        var count_vals = _.map(stats, (s) => {
          return {
            created: _.get(s, '[0].created'),
            total_count: _.reduce(s, function(sum, v) {
              return sum + _.get(v, 'total_count', 0)
            }, 0)
          }
        })

        return [{
          label: 'Pipes',
          data: this.getDatasetData(count_vals, this.baseline_vals)
        }]
      }
    },
    created() {
      this.tryFetchStats()
    },
    methods: {
      tryFetchStats() {
        if (!this.is_fetched && this.isAdmin)
          this.$store.dispatch('fetchAdminInfo', { type: 'processes' })

        if (!this.is_fetched && !this.isAdmin)
          this.$store.dispatch('fetchStatistics', { type: 'processes' })
      },
      getDatasetData(stats, range) {
        // remove out-of-bounds values
        var seq = _.filter(stats, (v) => {
          var created = _.get(v, 'created')
          return created.isAfter(this.start_date) && created.isBefore(this.end_date)
        })

        seq = _.unionWith(seq, range, (v1, v2) => {
          return _.get(v1, 'created').isSame(_.get(v2, 'created'))
        })

        seq = _.sortBy(seq, (s) => {
          return _.get(s, 'created')
        })

        return _.map(seq, (s) => {
          return _.get(s, 'total_count', 0)
        })
      }
    }
  }
</script>
