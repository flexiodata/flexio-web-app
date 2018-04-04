<template>
  <div v-if="is_fetching">
    <div class="flex flex-row justify-center items-center min-h4 pa3">
      <spinner size="medium"></spinner>
      <span class="ml2 f5">Loading...</span>
    </div>
  </div>
  <div class="pa3" v-else>
    <div class="f3 ma0 pb2 mb3" v-if="title.length > 0">{{title}}</div>
    <div
      class="mb4"
      v-for="(item, index) in top_stats_by_pipe"
      :item="item"
      :index="index"
    >
      <div>
        <div class="f5 mb3 dib">{{index+1}}. {{item.label}}</div><span class="silver"> &ndash; {{item.owned_by.first_name}} {{item.owned_by.last_name}} ({{item.owned_by.eid}})</span>
      </div>
      <line-chart
        :height="chartHeight"
        :labels="labels"
        :datasets="[item]"
        :options="{
          legend: {
            display: false
          },
          scales: {
            yAxes: [{
              ticks: {
                beginAtZero: true,
                stepSize: 1
              }
            }]
          }
        }"
      ></line-chart>
    </div>
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
        default: 'Top 10 Pipes'
      },
      'top': {
        type: Number,
        default: 10
      },
      'chart-height': {
        type: Number,
        default: 120
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
      }
    },
    components: {
      Spinner,
      LineChart
    },
    computed: {
      is_fetching() {
        return _.get(this.$store, 'state.statistics_fetching.processes', false)
      },
      is_fetched() {
        return _.get(this.$store, 'state.statistics_fetched.processes', false)
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
        return _.get(this.$store, 'state.statistics.processes', [])
      },
      stats_with_created() {
        // add a moment date to each stat
        return _.map(this.store_stats, (s) => {
          return _.assign(s, {
            created: moment(_.get(s, 'process_created')).utc().startOf('day')
          })
        })
      },
      stats_by_pipe() {
        // group each stat by pipe
        var stats = _.groupBy(this.stats_with_created, (s) => {
          return _.get(s, 'pipe.eid', '')
        })

        // reduce to simply { label, data } for the specified time period
        stats = _.map(stats, (s) => {
          var pipe = _.get(s, '[0].pipe', {})

          // dates that have values for this pipe
          var count_vals = _.map(s, (v) => {
            return _.pick(v, ['created', 'total_count'])
          })

          var identifier = _.get(pipe, 'ename', '')
          identifier = identifier.length > 0 ? identifier : _.get(pipe, 'eid')


          var label = _.get(pipe, 'name', 'Pipe')
          label = label + ' ('+identifier+')'

          return {
            label,
            data: this.getDatasetData(count_vals, this.baseline_vals),
            pipe,
            owned_by: _.get(pipe, 'owned_by', {})
          }
        })

        return stats
      },
      top_stats_by_pipe() {
        var top = _.sortBy(this.stats_by_pipe, (s) => {
          return _.sum(_.get(s, 'data'))
        })
        return _.take(_.reverse(top), this.top)
      }
    },
    created() {
      this.tryFetchStats()
    },
    methods: {
      tryFetchStats() {
        if (!this.is_fetched)
          this.$store.dispatch('fetchAdminInfo', { type: 'processes' })
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
