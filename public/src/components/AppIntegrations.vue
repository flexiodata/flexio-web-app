<template>
  <!-- malformed URL; bail out -->
  <PageNotFound
    class="flex-fill bg-nearer-white"
    v-if="route_integration.length == 0 || route_action.length == 0"
  />

  <main
    class="overflow-y-scroll"
    :class="use_white_box ? 'pv4 pv5-ns ph4 ph5-l bg-nearer-white' : 'bg-white'"
    v-else
  >
    <div class="h2" v-if="use_white_box"></div>

    <div
      class="w-100 center mw-doc bg-white pt4 pb5 ph4 ph5-l"
      :class="use_white_box ? 'br2 css-white-box' : ''"
      style="margin-bottom: 15rem"
    >
      <!-- excel template download page -->
      <TemplateExcelDownloadPanel
        :title="template_title"
        :url="excel_spreadsheet_path"
        v-if="is_show_excel_template_download_page"
      />

      <!-- integration setup page -->
      <template v-else>
        <div
          class="tc"
          :style="use_white_box ? 'margin-top: -76px' : ''"
        >
          <img
            src="../assets/logo-square-80x80.png"
            alt="Flex.io"
            class="br-100 ba bw1 b--white"
            style="width: 84px; box-shadow: 0 0 3px rgba(0,0,0,0.4)"
          >
        </div>

        <h1 class="fw6 f2 tc pb2">{{title}}</h1>
        <h2 class="center tc nt2 mb4 f4 fw4 bb-0 silver" style="padding: 0; line-height: 1.7em" v-if="description.length > 0">{{description}}</h2>

        <!-- step heading -->
        <div
          class="dn db-ns mv4 pv2"
          v-if="!is_coming_from_addon"
        >
          <el-steps
            align-center
            finish-status="success"
            :active="step_heading_idx"
          >
            <el-step :title="is_quick_start ? 'Sign Up' : 'Set Up Integration'" />
            <el-step title="Get Add-on" />
            <el-step title="Get Started" />
          </el-steps>
        </div>

        <!-- step: template target chooser -->
        <div v-if="active_step == 'template' && (existing_integrations.length > 0 || is_quick_start)">
          <div
            class="mv4 mh3 br3 ba b--black-10 pv5 ph4"
            v-if="is_show_loading_template"
           >
            <Spinner size="large" message="Loading template..." />
          </div>
          <TemplateTargetChooserPanel
            class="mv4 pa4 bg-nearer-white br3"
            :gsheets-spreadsheet-id="gsheets_spreadsheet_id"
            :excel-download-url="excel_spreadsheet_path"
            @target-click="onTemplateTargetClick"
            v-else
          />
        </div>

        <!-- step: set up integrations -->
        <div v-if="active_step == 'setup'">
          <div
            class="br2 ba b--black-10 pv5 ph4"
            v-if="is_fetching_config"
          >
            <Spinner size="large" message="Loading configuration..." />
          </div>
          <FunctionMountSetupWizard
            :connection="active_connection"
            :setup-template="setup_template"
            @submit="saveIntegration"
            @oauth-connect="oauthConnect"
            v-else
          >
            <div slot="no-prompts">
              <div class="mv2 tc f6 fw4 lh-copy moon-gray"><em>No configuration is required for this integration.</em></div>
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

        <!-- step: show submitting -->
        <div v-if="active_step == 'submitting'">
          <ServiceIconWrapper :innerSpacing="10">
            <i
              class="el-icon-loading bg-white f2 silver"
              slot="icon"
            ></i>
            <p class="tc f3 lh-title">We're setting up your integration...</p>
          </ServiceIconWrapper>
        </div>

        <!-- step: show result (success) -->
        <div v-if="active_step == 'success'">
          <ServiceIconWrapper :innerSpacing="10">
            <i
              class="el-icon-success bg-white f2 dark-green"
              slot="icon"
            ></i>
            <p class="tc f3 lh-title">Your integration was created successfully!</p>
            <div class="bg-nearer-white pa4 br2 tc">
              <p class="mb0 f4 fw6 flex flex-row items-center justify-center">
                <Spinner class="mr2" />
                <span>Importing functions...</span>
              </p>
              <p class="center mw6 mb0 f7 i light-silver lh-copy">NOTE: This process may take a couple of minutes to complete. You're free to continue with the setup &mdash; the import will continue in the background.</p>
            </div>
            <div class="mt4" v-if="is_coming_from_addon">
              <p>Click <strong>Done</strong> below to close this window and start using this integration in your spreadsheet.</p>
            </div>
            <div class="mt4 tr">
              <el-button
                type="primary"
                class="ttu fw6"
                @click="onIntegrationSetupDoneClick"
              >
                <div class="ph2" v-if="is_coming_from_addon">Done</div>
                <div v-else>Continue</div>
              </el-button>
            </div>
          </ServiceIconWrapper>
        </div>

        <!-- step: show result (failure) -->
        <div v-if="active_step == 'failure'">
          <ServiceIconWrapper :innerSpacing="10">
            <i
              class="el-icon-error bg-white f2 dark-red"
              slot="icon"
            ></i>
            <p class="tc f3 lh-title mb0">Your integration couldn't be created</p>
            <p class="tc">We encountered some problems while trying to create your integration. Please try again.</p>
          </ServiceIconWrapper>
        </div>

        <!-- step: install add-ons -->
        <div v-if="active_step == 'addons'">
          <p class="center mw7">You're almost done! If you haven't done so already, please install the Flex.io Add-on for <span class="nowrap">Google Sheets</span> or <span class="nowrap">Microsoft Excel 365</span>. Once you've installed the add-on, you'll see your functions in the Flex.io sidebar and will be able to use them in your spreadsheet.</p>
          <AddonDownloadButtonPanel
            class="pv2-l"
            :open-links-in-new-window="true"
          />
          <p class="center mw7 f8 silver">* The Microsoft Excel 365 add-in will only function with an Excel for Office 365 subscription</p>
          <div class="mt4 tr">
            <el-button
              type="primary"
              class="ttu fw6"
              @click="onAddonStepDoneClick"
            >
              Continue
            </el-button>
          </div>
        </div>

        <!-- step: integration setup complete -->
        <IntegrationSetupCompletePanel
          :integration-info="integration_info"
          @template-click="onTemplateClick"
          @open-app-click="onOpenAppClick"
          v-if="active_step == 'complete'"
        />
      </template>
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
  import AddonDownloadButtonPanel from '@/components/AddonDownloadButtonPanel'
  import IntegrationSetupCompletePanel from '@/components/IntegrationSetupCompletePanel'
  import TemplateTargetChooserPanel from '@/components/TemplateTargetChooserPanel'
  import TemplateExcelDownloadPanel from '@/components/TemplateExcelDownloadPanel'
  import PageNotFound from '@/components/PageNotFound'

  const getNameSuffix = (length) => {
    return randomstring.generate({
      length,
      charset: 'alphabetic',
      capitalization: 'lowercase'
    })
  }

  const getDefaultState = () => {
    return {
      is_fetching_config: true,
      is_started_on_template: false,
      is_show_loading_template: false,
      is_show_excel_template_download_page: false,
      step_order: ['setup', 'submitting'],
      active_step: '', // 'setup', 'template', 'submitting', 'success' or 'failure'
      active_integration: {},
      setup_template: null,
      output_mount: {}
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
      AddonDownloadButtonPanel,
      IntegrationSetupCompletePanel,
      TemplateTargetChooserPanel,
      TemplateExcelDownloadPanel,
      PageNotFound,
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
      existing_integrations() {
        return _.filter(this.function_mounts, f => {
          var manifest_url = _.get(f, 'connection_info.url', '')
          return manifest_url.indexOf('functions-' + this.route_integration) >= 0 ? true : false
        })
      },
      integrations() {
        return this.getProductionIntegrations()
      },
      active_connection() {
        return _.get(this.active_integration, 'connection', {})
      },
      active_step_idx() {
        return _.indexOf(this.step_order, this.active_step)
      },
      // this is just for display purposes
      step_heading_idx() {
        switch (this.active_step) {
          case 'setup':      return 0
          case 'submitting': return 0
          case 'success':    return 0
          case 'failure':    return 0
          case 'addons':     return 1
          case 'complete':   return 2
          case 'template':   return 2
        }
        return 0
      },
      has_setup_template() {
        return !_.isNil(this.setup_template)
      },
      setup_template_image_url() {
        return _.get(this.setup_template, 'image.src', '')
      },
      query_context() {
        return _.get(this.$route, 'query.context', '')
      },
      is_coming_from_addon() {
        switch (this.query_context) {
          case 'gsheets':
          case 'googlesheets':
          case 'excel':
          case 'excel-web':
          case 'excel-desktop':
            return true
        }

        return false
      },
      use_white_box() {
        return this.is_coming_from_addon || this.is_show_excel_template_download_page ? false : true
      },
      route_integration() {
        // NOTE: this value is required to make this page backward compatible
        // for older add-ons and should be removed at some point (the integration name
        // used to be specified as a query param with key value `integration`, but is
        // now the first URL param `integration_name`)
        var backward_compatible = _.get(this.$route, 'query.integration', '')

        if (_.get(this.$route, 'params.action', '').length == 0) {
          return backward_compatible
        }

        return _.get(this.$route, 'params.integration_name', backward_compatible)
      },
      route_action() {
        // NOTE: this value is required to make this page backward compatible
        // for older add-ons and should be removed at some point (the action used to be
        // specified as the first URL params, but now the integration name is the first
        // URL parameter and the action is the second URL parameter
        // is now the first URL param, but `action` used to be the first URL param)
        var backward_compatible = _.get(this.$route, 'params.integration_name', '')
        return _.get(this.$route, 'params.action', backward_compatible)
      },
      is_quick_start() {
        return this.route_integration == 'quick-start'
      },
      gsheets_spreadsheet_id() {
        // NOTE: this value is required to make this page backward compatible
        // for older add-ons and should be removed at some point: we renamed the
        // query parameter `spreadsheet_id` => `gsheets_spreadsheet_id`
        var backward_compatible = _.get(this.$route, 'query.spreadsheet_id', '')
        return _.get(this.$route, 'query.gsheets_spreadsheet_id', backward_compatible)
      },
      excel_spreadsheet_path() {
        // NOTE: this value is required to make this page backward compatible
        // for older add-ons and should be removed at some point: we renamed the
        // query parameter `spreadsheet_path` => `excel_spreadsheet_path`
        var backward_compatible = _.get(this.$route, 'query.spreadsheet_path', '')
        return _.get(this.$route, 'query.excel_spreadsheet_path', backward_compatible)
      },
      templates() {
        return _.get(this.setup_template, 'templates', [])
      },
      integration_icon() {
        return _.get(this.setup_template, 'image.src', '')
      },
      integration_title() {
        return _.get(this.setup_template, 'title', '')
      },
      integration_info() {
        return [{
          icon: this.integration_icon,
          templates: this.templates
        }]
      },
      found_template() {
        var find_by_gsheets = _.find(this.templates, t => t.gsheets_spreadsheet_id == this.gsheets_spreadsheet_id)
        var find_by_excel = _.find(this.templates, t => t.excel_spreadsheet_path == this.excel_spreadsheet_path)
        return find_by_gsheets || find_by_excel
      },
      template_title() {
        return _.get(this.found_template, 'title', 'this spreadsheet')
      },
      template_description() {
        return _.get(this.found_template, 'description', '')
      },
      template_target() {
        return _.get(this.$route, 'query.target', '')
      },
      route_title() {
        return _.get(this.$route, 'query.title', '')
      },
      route_description() {
        return _.get(this.$route, 'query.description', '')
      },
      title() {
        if (this.route_action == 'template' && this.template_title != 'this spreadsheet') {
          return this.template_title
        } else {
          return this.route_title.length > 0 ? this.route_title :
                 this.integration_title.length > 0 ? 'Connect to ' + this.integration_title :
                 'Integration Setup'
        }
      },
      description() {
        if (this.route_action == 'template') {
          return this.template_description
        } else {
          return this.route_description.length > 0 ? this.route_description : ''
        }
      },
    },
    mounted() {
      var team_name = this.getActiveUsername()
      this.$store.dispatch('teams/changeActiveTeam', { team_name })
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
        // the URL is malformed; bail out
        if (val == '') {
          return
        }

        // update the active step from the route
        this.active_step = val

        if (this.active_step == 'template') {
          if (this.is_quick_start) {
            // nothing to do; we're done
            return
          } else if (this.existing_integrations.length > 0) {
            // the user has already created an integration of this type (crunchbase, etc.)
            // and most likely only has one integration of this type; just take them directly
            // to the template in Google Sheets
            if (this.template_target == 'gsheets' && this.gsheets_spreadsheet_id.length > 0) {
              // redirect to Copy Google Sheet page
              this.is_show_loading_template = true
              setTimeout(() => { this.redirectToGoogleSheets() }, 500)
            } else if (this.template_target == 'excel' && this.excel_spreadsheet_path.length > 0) {
              // show Excel download page (looks like Google Sheets copy sheet page)
              this.is_show_loading_template = true
              setTimeout(() => {
                this.is_show_loading_template = false
                this.is_show_excel_template_download_page = true
              }, 500)
            } else {
              // show choose Google Sheets or Excel page
            }
          } else {
            // set this flag so we know to come back to the template stuff
            // after configuring the integration
            this.is_started_on_template = true
            this.active_step = 'setup'
          }
        }

        // pre-select integrations
        this.initActiveIntegrationFromRoute()

        // fetch the integration config (we need this for things like
        // the template names as well as the function pack setup)
        this.fetchIntegrationConfig()
      },
      initActiveIntegrationFromRoute() {
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
        window.location = 'https://docs.google.com/spreadsheets/d/' + this.gsheets_spreadsheet_id + '/copy'
      },
      onTemplateTargetClick(target, path) {
        if (target == 'gsheets') {
          this.redirectToGoogleSheets()
        } else if (target == 'excel') {
          this.is_show_excel_template_download_page = true
        }
      },
      onNextStepClick() {
        this.active_step = this.step_order[this.active_step_idx + 1]

        // we're on the last step; commit all changes to the backend
        // and take the user to wherever they're headed next
        if (this.active_step == 'submitting') {
          this.submitIntegrationConfig()
        }
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
              this.active_step = 'success'

              if (window.opener) {
                var msg_json = { type: 'flexio-integration-setup-complete' }
                window.opener.postMessage(msg_json, "*")
              }
            })
          })
        })
        .catch(error => {
          this.active_step = 'failure'
        })
      },
      onIntegrationSetupDoneClick() {
        if (this.is_coming_from_addon) {
          window.close()
        } else {
          this.active_step = 'addons'
        }
      },
      onAddonStepDoneClick() {
        if (this.is_started_on_template === true) {
          // go back to setting up a template
          this.active_step = 'template'
          this.setRoute('template')
        } else {
          this.active_step = 'complete'
        }
      },
      onTemplateClick(template) {
        this.active_step = 'template'

        var gsheets_spreadsheet_id = _.get(template, 'gsheets_spreadsheet_id', undefined)
        var excel_spreadsheet_path = _.get(template, 'excel_spreadsheet_path', undefined)

        // update the route
        var new_route = _.pick(this.$route, ['name', 'meta', 'params', 'path', 'query'])
        new_route.params = _.assign({}, new_route.params, { action: 'template' })
        new_route.query = _.assign({}, new_route.query, { gsheets_spreadsheet_id, excel_spreadsheet_path })
        this.$router.replace(new_route)
      },
      onOpenAppClick() {
        var team_name = this.active_team_name
        var path = `/${team_name}/functions`

        this.$store.dispatch('teams/changeActiveTeam', { team_name }).then(response => {
          this.$router.push({ path })
        })
      }
    }
  }
</script>
