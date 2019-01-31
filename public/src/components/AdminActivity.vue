<template>
  <div class="bg-white">
    <!-- list -->
    <AppActivity
      class="h-100"
      :items="processes"
      :show-user="true"
      :created-min.sync="created_min"
      :created-max.sync="created_max"
      :owned-by.sync="owned_by"
    />
  </div>
</template>

<script>
  import moment from 'moment'
  import AppActivity from './AppActivity.vue'

  export default {
    metaInfo: {
      title: '[Admin] Activity Overview'
    },
    components: {
      AppActivity
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

        this.$store.dispatch('v2_action_fetchAdminProcesses', { attrs }).then(response => {
          var processes = response.data
          this.processes = processes
        }).catch(error => {
          // TODO: add error handling
        })

        this.querying = true
        this.$nextTick(() => { this.querying = false })
      },
      updateRoute(query_params) {
        debugger

        // update the route
        var new_route = _.pick(this.$route, ['name', 'meta', 'params', 'path', 'query'])
        new_route.query = _.assign({}, new_route.query, query_params)
        this.$router.replace(new_route)

        this.queryAdminProcesses()
      }
    }
  }
</script>
