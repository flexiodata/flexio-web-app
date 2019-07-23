<template>
  <div class="bg-white">
    <ProcessActivity
      class="pt4 h-100"
      :items="processes"
      :show-user="true"
      :created-min.sync="created_min"
      :created-max.sync="created_max"
      :owned-by.sync="owned_by"
    >
      <h1 class="mv0 f2 fw4 mr3" slot="title">Process Activity</h1>
    </ProcessActivity>
  </div>
</template>

<script>
  import moment from 'moment'
  import ProcessActivity from '@comp/ProcessActivity'

  export default {
    metaInfo: {
      title: '[Admin] Process Activity'
    },
    components: {
      ProcessActivity
    },
    data() {
      return {
        processes: [],
        querying: false
      }
    },
    computed: {
      owned_by: {
        get() {
          return _.get(this.$route.query, 'owned_by', null)
        },
        set(val) {
          this.updateRoute({ owned_by: val })
        }
      },
      created_min: {
        get() {
          var yesterday = moment().subtract(1, 'days')
          return _.get(this.$route.query, 'created_min', yesterday.format('YMMDD'))
        },
        set(val) {
          var created_min = val ? moment(val).format('YMMDD') : null
          this.updateRoute({ created_min })
        }
      },
      created_max: {
        get() {
          var today = moment()
          return _.get(this.$route.query, 'created_max', today.format('YMMDD'))
        },
        set(val) {
          var created_max = val ? moment(val).format('YMMDD') : null
          this.updateRoute({ created_max })
        }
      }
    },
    mounted() {
      this.queryAdminProcesses()
    },
    methods: {
      queryAdminProcesses() {
        if (this.querying === true) {
          return
        }

        var attrs = {
          owned_by: this.owned_by,
          created_min: this.created_min,
          created_max: this.created_max
        }

        this.$store.dispatch('processes/fetch', { team_name: 'admin', attrs }).then(response => {
          this.processes = response.data
        })

        this.querying = true
        this.$nextTick(() => { this.querying = false })
      },
      updateRoute(query_params) {
        // update the route
        var new_route = _.pick(this.$route, ['name', 'meta', 'params', 'path', 'query'])
        new_route.query = _.assign({}, new_route.query, query_params)
        this.$router.replace(new_route)

        this.queryAdminProcesses()
      }
    }
  }
</script>
