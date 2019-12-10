<template>
  <!-- if the user's free trial has expired, show them this message -->
  <div class="flex flex-column bg-nearer-white " v-if="hasFreeTrialExpired()">
    <div class="flex-fill ph4 pv5 overflow-y-scroll">
      <div class="w-100 center mw-doc ">
        <h1 class="mt0 mb4 tc f3">Your free trial period has ended. Please choose your plan.</h1>
        <BillingEditPanel class="pa4 bg-white br2 css-white-box" />
      </div>
    </div>
  </div>

  <!-- fetching -->
  <div v-else-if="is_fetching">
    <div class="flex flex-column justify-center bg-nearer-white h-100">
      <Spinner size="large" message="Loading activity..." />
    </div>
  </div>

  <div
    class="flex flex-column bg-nearer-white overflow-y-scroll"
    v-else
  >
    <div class="ph4 pv5">
      <div
        class="w-100 center pa4 bg-white br2 css-white-box overflow-hidden"
        style="max-width: 1280px; min-height: 20rem; margin-bottom: 15rem"
      >
        <ProcessActivity
          :items="processes"
          :created-min.sync="created_min"
          :created-max.sync="created_max"
        >
          <div slot="title">
            <h3 class="mv0 fw6 f3">Activity</h3>
          </div>
        </ProcessActivity>
      </div>
    </div>
  </div>
</template>

<script>
  import moment from 'moment'
  import { mapState, mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import ProcessActivity from '@/components/ProcessActivity'
  import BillingEditPanel from '@/components/BillingEditPanel'

  export default {
    metaInfo() {
      return {
        title: 'Activity',
        titleTemplate: (chunk) => {
          return chunk ? `${chunk} | ${this.getActiveTeamLabel()} | Flex.io` : 'Activity | Flex.io'
        }
      }
    },
    components: {
      Spinner,
      ProcessActivity,
      BillingEditPanel
    },
    data() {
      return {
        processes: [],
        is_fetching: false
      }
    },
    computed: {
      // mix this into the outer object with the object spread operator
      ...mapState({
        active_team_name: state => state.teams.active_team_name
      }),
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
      this.fetchProcesses()
    },
    methods: {
      ...mapGetters('teams', {
        'getActiveTeamLabel': 'getActiveTeamLabel'
      }),
      ...mapGetters('users', {
        'hasFreeTrialExpired': 'hasFreeTrialExpired'
      }),
      fetchProcesses() {
        if (this.is_fetching === true) {
          return
        }

        this.is_fetching = true

        var team_name = this.active_team_name
        var attrs = {
          owned_by: this.owned_by,
          created_min: this.created_min,
          created_max: this.created_max
        }

        this.$store.dispatch('processes/fetch', { team_name, attrs }).then(response => {
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

        this.fetchProcesses()
      }
    }
  }
</script>
