<template>
  <main class="overflow-y-scroll bg-white">
    <div
      class="w-100 center mw-doc pv4 ph5 bg-white"
      style="margin-bottom: 15rem"
    >
      <div class="tc">
        <img src="../assets/logo-square-80x80.png" alt="Flex.io" class="br-100 ba bw1 b--white" style="width: 84px; box-shadow: 0 0 3px rgba(0,0,0,0.4)">
      </div>

      <h1 class="fw6 f2 tc pb2">{{title}}</h1>

      <!-- step: set up integrations -->
      <div v-if="active_step == 'setup'">
        <!-- fetching config -->
        <div v-if="is_fetching_config">
          <div class="br2 ba b--black-10 pv5 ph4">
            <Spinner size="large" message="Loading configuration..." />
          </div>
        </div>

        <FunctionMountSetupWizard
          :setup-template="active_setup_template"
          @submit="saveIntegration"
          v-else
        >
          <div slot="no-prompts">
            <div class="tc f6 fw4 lh-copy moon-gray"><em>No configuration is required for this integration.</em></div>
            <ButtonBar
              class="mt4"
              :cancel-button-visible="false"
              :cancel-button-text="'Back'"
              :submit-button-text="'Next'"
              @submit-click="saveIntegration({})"
            />
          </div>
        </FunctionMountSetupWizard>
      </div>

      <!-- step 4: show result (success) -->
      <div v-else-if="active_step == 'setup-success'">
        <ServiceIconWrapper :innerSpacing="10">
          <i
            class="el-icon-success bg-white f2 dark-green"
            slot="icon"
          ></i>
          <p class="tc">Your integration was created successfully. Click <strong>"Done"</strong> to begin importing functions.</p>
        </ServiceIconWrapper>
        <ButtonBar
          class="mt4"
          :cancel-button-visible="false"
          :submit-button-text="'Done'"
          @submit-click="submitIntegrationConfig"
        />
      </div>

    </div>
  </main>
</template>

<script>
  import axios from 'axios'
  import randomstring from 'randomstring'
  import Spinner from 'vue-simple-spinner'
  import { mapState, mapGetters } from 'vuex'
  import { OBJECT_TYPE_CONNECTION } from '@/constants/object-type'
  import api from '@/api'
  import ButtonBar from '@/components/ButtonBar'
  import IconList from '@/components/IconList'
  import FunctionMountSetupWizard from '@/components/FunctionMountSetupWizard'
  import ServiceIconWrapper from '@/components/ServiceIconWrapper'
  import MemberInvitePanel from '@/components/MemberInvitePanel'

  const getNameSuffix = (length) => {
    return randomstring.generate({
      length,
      charset: 'alphabetic',
      capitalization: 'lowercase'
    })
  }

  const getDefaultState = () => {
    return {
      is_fetching_config: false,
      route_title: '',
      active_step: 'integrations', // 'integrations', 'setup', 'addons', 'members'
      active_integration_idx: 0,
      active_setup_template: null,
      selected_integrations: [],
      output_mounts: [],
      email_invites: []
    }
  }

  export default {
    metaInfo() {
      return {
        title: this.title,
        titleTemplate: (chunk) => {
          return `${chunk} | Flex.io`
        }
      }
    },
    components: {
      Spinner,
      ButtonBar,
      IconList,
      FunctionMountSetupWizard,
      ServiceIconWrapper,
      MemberInvitePanel,
    },
    data() {
      return getDefaultState()
    },
    computed: {
      ...mapState({
        active_team_name: state => state.teams.active_team_name,
      }),
      integrations() {
        return this.getProductionIntegrations()
      },
      active_step_idx() {
        return _.indexOf(this.step_order, this.active_step)
      },
      active_integration() {
        return this.selected_integrations[this.active_integration_idx]
      },
      has_active_setup_template() {
        return !_.isNil(this.active_setup_template)
      },
      step_order() {
       return ['setup', 'setup-success']
      },
      integrations_from_route() {
        var integrations = _.get(this.$route, 'query.integration', '')
        return integrations.length > 0 ? integrations.split(',') : []
      },
      title() {
        if (this.route_title.length > 0) {
          return this.route_title
        } else {
          return 'Integration Setup'
        }
      }
    },
    mounted() {
      var team_name = this.getActiveUsername()
      this.$store.dispatch('teams/changeActiveTeam', { team_name })

      // pre-select integrations
      this.selectIntegrationsFromRoute()

      // update the active step from the route
      this.active_step = _.get(this.$route, 'params.action', 'integrations')

      if (this.active_step == 'setup') {
        this.fetchIntegrationConfig()
      }

      // update title from route
      this.route_title = _.get(this.$route, 'query.title', '')

      this.setRoute(this.active_step)
    },
    methods: {
      ...mapGetters('users', {
        'getActiveUsername': 'getActiveUsername'
      }),
      ...mapGetters('integrations', {
        'getProductionIntegrations': 'getProductionIntegrations',
      }),
      selectIntegrationsFromRoute() {
        _.each(this.integrations_from_route, cname => {
          var selected_integration = _.find(this.integrations, f => _.get(f, 'connection.name', '') == cname)
          if (selected_integration) {
            this.selected_integrations = this.selected_integrations.concat([selected_integration])
          }
        })
      },
      setRoute(action) {
        // update the route
        var current_action = _.get(this.$route, 'params.action', '')
        var new_route = _.pick(this.$route, ['name', 'meta', 'params', 'path', 'query'])
        new_route.params = _.assign({}, new_route.params, { action })
        this.$router[current_action.length == 0 ? 'replace' : 'push'](new_route)
      },
      onNextStepClick() {
        // we're on the last step; commit all changes to the backend and take the user to the app
        if (this.active_step_idx == this.step_order.length - 1) {
          this.submitIntegrationConfig()
          return
        }

        if (this.active_step == 'integrations' && this.selected_integrations.length == 0) {
          var msg = "Are you sure you'd like to continue without setting up an integration?"
          var title = 'Really continue without an integration?'

          this.$confirm(msg, title, {
            type: 'warning',
            confirmButtonClass: 'ttu fw6',
            cancelButtonClass: 'ttu fw6',
            confirmButtonText: 'Continue',
            cancelButtonText: 'Cancel',
            dangerouslyUseHTMLString: true,
          }).then(() => {
            // skip over integration set up if none were selected
            this.active_step = this.step_order[this.active_step_idx + 2]

            // make sure the route is in sync
            this.setRoute(this.active_step)
          })
        } else {
          this.active_step = this.step_order[this.active_step_idx + 1]

          if (this.active_step == 'setup') {
            this.fetchIntegrationConfig()
          }

          // make sure the route is in sync
          this.setRoute(this.active_step)
        }
      },
      fetchIntegrationConfig() {
        this.is_fetching_config = true

        var team_name = this.active_team_name
        var url = _.get(this.active_integration, 'connection.connection_info.url', '')

        api.fetchFunctionPackSetupTemplate(team_name, url).then(response => {
          this.active_setup_template = response.data
          this.is_fetching_config = false
        }).catch(error => {
          // TODO: this just skips the rest of the integration setup;
          //       this needs to be thought through more...
          this.active_setup_template = null
          this.active_step == 'addons'
          this.is_fetching_config = false
        })
      },
      processSetupConfig(setup_config, parent_eid, callback) {
        var team_name = this.active_team_name
        var setup_config = _.cloneDeep(setup_config)
        var xhrs = []

        // make sure we create/update any key values that are meant to be connections
        _.each(setup_config, (val, key) => {
          if (_.get(val, 'eid_type', '') == OBJECT_TYPE_CONNECTION) {
            var xhr
            var eid = _.get(val, 'eid', '')
            var attrs = _.omit(val, ['eid_type'])
            attrs.name = 'connection-' + getNameSuffix(4)
            attrs.parent_eid = parent_eid

            if (eid.length == 0) {
              // create the connection
              xhr = this.$store.dispatch('connections/create', { team_name, attrs })
            } else {
              // update the connection
              xhr = this.$store.dispatch('connections/update', { team_name, eid, attrs })
            }

            xhr.then(response => {
              // save only the minimal amount of information in the integration (e.g. { eid, eid_type, connection_type })
              setup_config[key] = _.pick(response.data, ['eid', 'eid_type', 'connection_type'])
            })

            // store the promise so that we can issue our callback after all promises are resolved
            xhrs.push(xhr)
          }
        })

        // issue our callback when all of the above XHRs are finished
        axios.all(xhrs).then(responses => {
          callback(setup_config)
        })
      },
      saveIntegration(setup_config) {
        // if the builder item is an auth builder item, create the connection at this time
        // and then we can move forward to the next step
        this.processSetupConfig(setup_config, undefined, setup_config => {
          var setup_template = this.active_setup_template
          var mount = _.cloneDeep(_.get(this.active_integration, 'connection', {}))
          mount = _.assign({}, mount, { setup_config, setup_template })

          this.output_mounts = [].concat(this.output_mounts).concat([mount])

          if (this.active_integration_idx == this.selected_integrations.length - 1) {
            this.onNextStepClick()
          } else {
            this.active_integration_idx++
            this.fetchIntegrationConfig()
          }
        })
      },
      submitIntegrationConfig() {
        var team_name = this.active_team_name
        var create_xhrs = []

        _.each(this.output_mounts, attrs => {
          var xhr = this.$store.dispatch('connections/create', { team_name, attrs })
          create_xhrs.push(xhr)
        })

        // create all of the integrations
        axios.all(create_xhrs).then(responses => {
          var sync_xhrs = []

          // start syncing all of the integrations
          _.each(responses, response => {
            var eid = _.get(response.data, 'eid', '')
            var setup_config = _.get(response.data, 'setup_config', {})
            var xhr = this.$store.dispatch('connections/sync', { team_name, eid })
            sync_xhrs.push(xhr)

            // make sure we add parent eids to all child connections if we need to
            this.processSetupConfig(setup_config, eid)
          })

          window.close()
        })
        .catch(error => {

        })
      }
    }
  }
</script>
