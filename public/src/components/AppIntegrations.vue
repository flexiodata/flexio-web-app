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

      <!-- step: show spinner text and take user to the copy sheet page -->
      <div v-if="matching_integrations.length > 0 && active_step == 'template'">
        <div
          class="mv4 mh3 ph4 pv5 ba b--black-10 br3"
          v-if="is_auto_loading_template"
        >
          <Spinner
            size="large"
            message="Loading template..."
          />
        </div>
        <div
          class="mv4 mh3 pa4 bg-nearer-white br3"
          v-else
        >
          <p>Would you like to open this template in Google Sheets or Microsoft Excel?</p>
          <div class="flex-l flex-row-l">
            <div class="flex-fill">
              <el-button
                class="w-100 fw6"
                plain
                @click="redirectToGoogleSheets"
              >
                <div class="flex flex-row items-center justify-center">
                  <img src="../assets/icon/icon-google-sheets-128.png" alt="Google Sheets" style="height: 32px" />
                  <div class="ml2 fw6 f5">Google Sheets</div>
                </div>
              </el-button>
            </div>

            <div class="flex-fill pt3 pt0-l pl4-l">
              <el-button
                class="w-100 fw6"
                plain
              >
                <div class="flex flex-row items-center justify-center">
                  <img src="../assets/icon/icon-excel-128.png" alt="Microsoft Excel" style="height: 32px" />
                  <div class="ml3 fw6 f5">Microsoft Excel 365</div>
                </div>
              </el-button>
            </div>
          </div>
        </div>
      </div>

      <!-- step: set up integrations -->
      <div v-else-if="active_step == 'setup'">
        <!-- fetching config -->
        <div v-if="is_fetching_config">
          <div class="br2 ba b--black-10 pv5 ph4">
            <Spinner size="large" message="Loading configuration..." />
          </div>
        </div>

        <FunctionMountSetupWizard
          :connection="active_connection"
          :setup-template="setup_template"
          @submit="saveIntegration"
          @oauth-connect="oauthConnect"
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

      <!-- step: show result (success) -->
      <div v-else-if="active_step == 'success'">
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
          :submit-button-text="is_submitting ? 'Submitting...' : 'Done'"
          :submit-button-disabled="is_submitting"
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
  import { atobUnicode } from '@/utils'
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
      is_submitting: false,
      is_fetching_config: false,
      is_auto_loading_template: false,
      route_title: '',
      step_order: ['setup', 'success'],
      active_step: '', // 'template' or 'setup' or 'success'
      active_integration: {},
      setup_template: null,
      output_mount: {},
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
    watch: {
      route_action: {
        handler: 'handleRouteActionChange',
        immediate: true
      }
    },
    data() {
      return getDefaultState()
    },
    computed: {
      ...mapState({
        active_team_name: state => state.teams.active_team_name,
      }),
      function_mounts() {
        return this.getAvailableFunctionMounts()
      },
      matching_integrations() {
        return _.filter(this.function_mounts, f => {
          var manifest_url = _.get(f, 'connection_info.url', '')
          return manifest_url.indexOf('functions-'+this.route_integration) >= 0 ? true : false
        })
      },
      integrations() {
        return this.getProductionIntegrations()
      },
      active_step_idx() {
        return _.indexOf(this.step_order, this.active_step)
      },
      active_connection() {
        return _.get(this.active_integration, 'connection', {})
      },
      has_setup_template() {
        return !_.isNil(this.setup_template)
      },
      route_integration() {
        return _.get(this.$route, 'query.integration', '')
      },
      route_action() {
        return _.get(this.$route, 'params.action', 'setup')
      },
      template_target() {
        return _.get(this.$route, 'query.target', '')
      },
      spreadsheet_id() {
        return _.get(this.$route, 'query.spreadsheet_id', '')
      },
      spreadsheet_path() {
        return _.get(this.$route, 'query.spreadsheet_path', '')
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

      // update title from route
      this.route_title = _.get(this.$route, 'query.title', '')
    },
    methods: {
      ...mapGetters('users', {
        'getActiveUsername': 'getActiveUsername',
      }),
      ...mapGetters('connections', {
        'getAvailableFunctionMounts': 'getAvailableFunctionMounts',
      }),
      ...mapGetters('integrations', {
        'getProductionIntegrations': 'getProductionIntegrations',
      }),
      handleRouteActionChange(val, old_val) {
        // update the active step from the route
        this.active_step = val

        if (this.active_step == 'template') {
          // the user has already created an integration of this type (crunchbase, etc.)
          // and most likely only has one integration of this type; just take them directly
          // to the template in Google Sheets
          if (this.matching_integrations.length > 0) {
            if (this.template_target == 'gsheets' && this.spreadsheet_id.length > 0) {
              // redirect to Copy Google Sheet page
              this.is_auto_loading_template = true
              setTimeout(() => { this.redirectToGoogleSheets() }, 500)
            } else if (this.template_target == 'excel' && this.spreadsheet_path.length > 0) {
              // show Excel download page (looks like Google Sheets copy sheet page)
              this.is_auto_loading_template = true
              setTimeout(() => { this.is_auto_loading_template = false }, 500)
            } else {
              // show choose Google Sheets or Excel page
            }
          } else {
            this.active_step = 'setup'
          }
        }

        // pre-select integrations
        this.initIntegrationFromRoute()

        if (this.active_step == 'setup') {
          this.fetchIntegrationConfig()
        }
      },
      initIntegrationFromRoute() {
        var cname = this.route_integration
        var route_integration = _.find(this.integrations, f => _.get(f, 'connection.name', '') == cname)
        if (route_integration) {
          this.active_integration = Object.assign({}, route_integration)
        }
      },
      setRoute(action) {
        // update the route
        var current_action = _.get(this.$route, 'params.action', '')
        var new_route = _.pick(this.$route, ['name', 'meta', 'params', 'path', 'query'])
        new_route.params = _.assign({}, new_route.params, { action })
        this.$router[current_action.length == 0 ? 'replace' : 'push'](new_route)
      },
      redirectToGoogleSheets() {
        window.location = 'https://docs.google.com/spreadsheets/d/' + this.spreadsheet_id + '/copy'
      },
      onNextStepClick() {
        // we're on the last step; commit all changes to the backend and take the user to the app
        if (this.active_step_idx == this.step_order.length - 1) {
          this.submitIntegrationConfig()
          return
        }

        this.active_step = this.step_order[this.active_step_idx + 1]

        // make sure the route is in sync
        this.setRoute(this.active_step)
      },
      fetchIntegrationConfig() {
        this.is_fetching_config = true

        var team_name = this.active_team_name
        var url = _.get(this.active_integration, 'connection.connection_info.url', '')

        api.fetchFunctionPackSetupTemplate(team_name, url).then(response => {
          this.setup_template = response.data
          this.tryFillOauthParams()
          this.is_fetching_config = false
        }).catch(error => {
          // TODO: this just skips the rest of the integration setup;
          //       this needs to be thought through more...
          this.setup_template = null
          this.is_fetching_config = false
        })
      },
      tryFillOauthParams() {
        var oauth_params = _.get(this.$route, 'query.oauth_params', '')
        if (oauth_params.length > 0) {
          try {
            var connection = JSON.parse(atobUnicode(oauth_params))

            // hard-code the connection info in the setup template since
            // it has been made that way by the OAuth callback
            var conn =_.get(this.setup_template, 'prompts[' + this.active_step_idx + '].connection', {})
            conn = Object.assign({ eid_type: 'CTN' }, conn, connection)
            var setup_template = _.cloneDeep(this.setup_template)
            _.set(setup_template, 'prompts[' + this.active_step_idx + '].connection', conn)
            this.setup_template = Object.assign({}, setup_template)
          }
          catch (e) {
          }

          // update the route
          var new_route = _.pick(this.$route, ['name', 'meta', 'params', 'path', 'query'])
          new_route.query = _.omit(new_route.query, ['oauth_params'])
          this.$router.replace(new_route)
        }
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
      oauthConnect(redirectTo) {
        redirectTo(window.location.href)
      },
      saveIntegration(setup_config) {
        // if the builder item is an auth builder item, create the connection at this time
        // and then we can move forward to the next step
        this.processSetupConfig(setup_config, undefined, setup_config => {
          var setup_template = this.setup_template
          var mount = _.cloneDeep(_.get(this.active_integration, 'connection', {}))
          mount = _.assign({}, mount, { setup_config, setup_template })

          this.output_mount = Object.assign({}, mount)
          this.onNextStepClick()
        })
      },
      submitIntegrationConfig() {
        this.is_submitting = true

        var team_name = this.active_team_name
        var create_xhrs = []

        var xhr = this.$store.dispatch('connections/create', { team_name, attrs: this.output_mount })
        create_xhrs.push(xhr)

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
            this.processSetupConfig(setup_config, eid, () => {
              var msg_json = { type: 'flexio-integration-setup-complete' }
              window.opener.postMessage(msg_json, "*")

              this.is_submitting = false
              this.$emit('done')
            })
          })
        })
        .catch(error => {

        })
      }
    }
  }
</script>
