<template>
  <!-- fetching -->
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center bg-white h-100">
      <Spinner size="large" message="Loading activity..." />
    </div>
  </div>

  <div
    class="bg-white"
    v-else
  >
    <ProcessActivity
      class="h-100 pb5 overflow-y-scroll"
      :items="processes"
      :show-user="true"
      :created-min.sync="created_min"
      :created-max.sync="created_max"
      :owned-by.sync="owned_by"
    >
      <h1 class="pt4 pb0 mv0 f2 fw4 mr3" slot="title">Process Activity</h1>
    </ProcessActivity>
  </div>
</template>

<script>
  import moment from 'moment'
  import Spinner from 'vue-simple-spinner'
  import ProcessActivity from '@/components/ProcessActivity'

  export default {
    metaInfo: {
      title: '[Admin] Process Activity'
    },
    components: {
      Spinner,
      ProcessActivity
    },
    data() {
      return {
        processes: [],
        is_fetching: false
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
      this.fetchAdminProcesses()
    },
    methods: {
      fetchAdminProcesses() {
        if (this.is_fetching === true) {
          return
        }

        this.is_fetching = true

        var attrs = {
          owned_by: this.owned_by,
          created_min: this.created_min,
          created_max: this.created_max
        }

        this.$store.dispatch('processes/fetch', { team_name: 'admin', attrs }).then(response => {
          this.processes = response.data
        })
        .finally(() => {
          this.is_fetching = false
        })
      },
      updateRoute(query_params) {
        // update the route
        var new_route = _.pick(this.$route, ['name', 'meta', 'params', 'path', 'query'])
        new_route.query = _.assign({}, new_route.query, query_params)
        this.$router.replace(new_route)

        this.fetchAdminProcesses()
      }
    }
  }
</script>
