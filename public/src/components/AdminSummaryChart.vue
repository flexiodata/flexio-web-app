<template>
  <div class="pa3">
    <line-chart
      :height="height"
      :labels="labels"
      :datasets="top5_stats"
      :options="{
        maintainAspectRatio: false,
        animation: {
          duration: 0
        }
      }"
    ></line-chart>
  </div>
</template>

<script>
  import moment from 'moment'
  import LineChart from './LineChart.vue'

  export default {
    props: {
      'type': {
        type: String,
        required: true
      },
      'top-number': {
        type: Number,
        default: 5
      },
      'height': {
        type: Number,
        default: 200
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
      reduced_stats() {
        // add a moment date to each stat
        var stats = _.map(this.store_stats, (s) => {
          return _.assign(s, {
            created: moment(_.get(s, 'process_created')).startOf('day')
          })
        })

        // group each stat by pipe
        stats = _.groupBy(stats, (s) => {
          return _.get(s, 'pipe.eid', '')
        })

        // reduce to simply { pipe, values } for the specified time period
        stats = _.map(stats, (s) => {
          // dates that have values for this pipe
          var count_vals = _.map(s, (v) => {
            return _.pick(v, ['created', 'total_count'])
          })

          // remove out-of-bounds values
          _.remove(count_vals, (v) => {
            var created = _.get(v, 'created')
            return created.isBefore(this.start_date) || created.isAfter(this.end_date)
          })

          return {
            label: _.get(s, '[0].pipe.name', 'Pipe'),
            data: this.getDatasetData(count_vals, this.baseline_vals)
          }
        })

        return stats
      },
      top5_stats() {
        console.log(this.reduced_stats)
        var top = _.sortBy(this.reduced_stats, (s) => {
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
        var seq = _.unionWith(stats, range, (v1, v2) => {
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
