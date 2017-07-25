<template>
  <div class="flex flex-column">
    <h1 class="ma3">Pipe usage over last 6 months</h1>
    <div class="flex-fill overflow-y-auto">
      <dev-dashboard-item
        v-for="(item, index) in processed_items"
        :item="item"
        :index="index"
      ></dev-dashboard-item>
    </div>
  </div>
</template>

<script>
  import axios from 'axios'
  import moment from 'moment'
  import DevDashboardItem from './DevDashboardItem.vue'

  var month_ago = moment().subtract(6, 'months').startOf('day')
  var now = moment().startOf('day')

  // create a starting array of values for the last month
  var base_seq = []
  while (month_ago.isBefore(now))
  {
    base_seq.push({
      created: moment(month_ago),
      total_count: 0
    })
    month_ago = month_ago.add(1, 'days')
  }

  export default {
    components: {
      DevDashboardItem
    },
    data() {
      return {
        items: [],
        base_seq
      }
    },
    computed: {
      created() {
        return moment(this.item.created).format('LL')
      },

      processed_items() {
        var items = _.map(this.items, (s) => {
          return _.assign(s, {
            created: moment(_.get(s, 'process_created')).startOf('day')
          })
        })

        items = _.groupBy(items, (s) => {
          return _.get(s, 'pipe.eid', '')
        })

        items = _.map(items, (s) => {
          // dates that have values for this pipe
          var count_vals = _.map(s, (i) => {
            return _.pick(i, ['created', 'total_count'])
          })

          return {
            pipe: _.get(s, '[0].pipe', {}),
            items: this.getSequence(count_vals)
          }
        })

        return items
      }
    },
    mounted() {
      axios.get('/api/v1/system/statistics/processes').then(response => {
        this.items = [].concat(response.data)
      }).catch(response => {

      })
    },
    methods: {
      getSequence(seq) {
        var seq = _.unionWith(seq, base_seq, (v1, v2) => {
          return _.get(v1, 'created').isSame(_.get(v2, 'created'))
        })

        return _.sortBy(seq, (s) => {
          return _.get(s, 'created')
        })
      }
    }
  }
</script>
