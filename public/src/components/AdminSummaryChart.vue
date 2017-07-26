<template>
  <div class="dib" v-if="store_stats.length == 0">
    <div class="flex flex-row justify-center items-center min-h3 pa3">
      <spinner size="medium"></spinner>
      <span class="ml2 f5">Loading statistics...</span>
    </div>
  </div>
  <div class="pa3" v-else>
    <h2 class="ma0">Pipes run over the last month</h2>
    <div class="mt2 mb3 pb2 ph3 pt4 bt b--black-10">
      <line-chart
        :height="height"
        :labels="labels"
        :datasets="stats_by_date"
        :options="{
          legend: {
            display: false
          }
        }"
      ></line-chart>
    </div>
    <h2 class="ma0 mt4">Top 10 pipes</h2>
    <div
      class="mt2 mb3 pb2 ph3 bt b--black-10"
      v-for="(item, index) in top_stats_by_pipe"
      :item="item"
      :index="index"
    >
      <div>
        <h4 class="dib">{{index+1}}. {{item.label}}</h4><span class="silver"> &ndash; {{item.owned_by.first_name}} {{item.owned_by.last_name}} ({{item.owned_by.eid}})</span>
      </div>
      <line-chart
        :height="100"
        :labels="labels"
        :datasets="[item]"
        :options="{
          legend: {
            display: false
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
      'type': {
        type: String,
        required: true
      },
      'top-number': {
        type: Number,
        default: 10
      },
      'height': {
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
      }
    },
    components: {
      Spinner,
      LineChart
    },
    computed: {
      end_date() {
        var end = _.isString(this.endDate) ? moment(this.endDate) : moment()
        return end.startOf('day')
      },
      start_date() {
        var start = _.isString(this.startDate) ? moment(this.startDate) : moment(this.end_date).subtract(this.duration.number, this.duration.type)
        return start.startOf('day')
      },
      baseline_vals() {
        // create a starting array of '0' values for the specified period
        var iter = moment(this.start_date)
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
        return _.get(this.$store, 'state.statistics.'+this.type, [])
      },
      stats_with_created() {
        // add a moment date to each stat
        return _.map(this.store_stats, (s) => {
          return _.assign(s, {
            created: moment(_.get(s, 'process_created')).startOf('day')
          })
        })
      },
      stats_by_date() {
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
          label: 'All Pipes',
          data: this.getDatasetData(count_vals, this.baseline_vals)
        }]
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
        return _.take(_.reverse(top), this.topNumber)
      }
    },
    mounted() {
      this.$store.dispatch('fetchStatistics', { type: this.type })
    },
    methods: {
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
