<template>
  <div>
    <!-- header -->
    <HeaderBar
      class="mb4"
      :title="our_title"
      @close-click="onClose"
      v-show="showHeader"
    />

    <!-- body -->
    <div>
      <!-- step 1: choose source -->
      <div v-if="active_step == 'choose-source' && !has_mount">
        <IconList
          :items="integrations"
          @item-click="onIntegrationClick"
          v-show="mountType == 'integration'"
        />

        <IconList
          items="services"
          :filter-by="filterByFunctionMount"
          @item-click="onServiceClick"
          v-show="mountType == 'mount'"
        />
      </div>

      <!-- step 2: configure connection -->
      <ConnectionEditPanel
        active-step="authentication"
        last-step="authentication"
        submit-button-text="Create Function Mount"
        :mode="mode"
        :connection="edit_mount"
        :show-header="false"
        :filter-by="filterByFunctionMount"
        @cancel="onCancel"
        @submit="onUpdateConnection"
        v-if="active_step == 'configure-connection' && has_mount"
      />

      <!-- step 3: setup config (optional) -->
      <FunctionMountSetupWizard
        :setup-template="setup_template"
        @submit="saveMountSetup"
        v-if="active_step == 'setup-config' && has_prompts"
      />

      <!-- step 4: show result (success) -->
      <div v-else-if="active_step == 'setup-success'">
        <ServiceIconWrapper :innerSpacing="10">
          <i
            class="el-icon-success bg-white f2 dark-green"
            slot="icon"
          ></i>
          <p class="tc">Your function mount was created successfully. Click <strong>"Done"</strong> to begin importing functions.</p>
        </ServiceIconWrapper>
        <ButtonBar
          class="mt4"
          :cancel-button-visible="false"
          :submit-button-text="'Done'"
          @submit-click="onSubmit"
        />
      </div>

      <!-- step 4: show result (failure) -->
      <div v-else-if="active_step == 'setup-error'">
        <ServiceIconWrapper :innerSpacing="10">
          <i
            class="el-icon-warning bg-white f2 dark-red"
            slot="icon"
          ></i>
          <p class="tc">No <strong>flexio.yml</strong> setup template file could be found at the specified location. Please try again.</p>
        </ServiceIconWrapper>
        <ButtonBar
          class="mt4"
          :cancel-button-visible="false"
          :submit-button-text="'Done'"
          @submit-click="onCancel"
        />
      </div>
    </div>

    <!-- footer -->
    <ButtonBar
      class="mt4"
      :submit-button-text="submit_label"
      @cancel-click="onCancel"
      @submit-click="onSubmit"
      v-show="showFooter && false"
    />
  </div>
</template>

<script>
  import randomstring from 'randomstring'
  import { mapState, mapGetters } from 'vuex'
  import { OBJECT_STATUS_AVAILABLE, OBJECT_STATUS_PENDING } from '@/constants/object-status'
  import api from '@/api'
  import { slugify } from '@/utils'
  import HeaderBar from '@/components/HeaderBar'
  import ButtonBar from '@/components/ButtonBar'
  import IconList from '@/components/IconList'
  import ServiceIconWrapper from '@/components/ServiceIconWrapper'
  import ConnectionEditPanel from '@/components/ConnectionEditPanel'
  import FunctionMountSetupWizard from '@/components/FunctionMountSetupWizard'
  import MixinConnection from '@/components/mixins/connection'

  const CONNECTION_MODE_RESOURCE = 'R'
  const CONNECTION_MODE_FUNCTION = 'F'

  const getNameSuffix = (length) => {
    return randomstring.generate({
      length,
      charset: 'alphabetic',
      capitalization: 'lowercase'
    })
  }

  const getDefaultAttrs = () => {
    return {
      eid: null,
      eid_status: OBJECT_STATUS_PENDING,
      name: '',
      title: '',
      description: '',
      connection_type: '',
      connection_mode: CONNECTION_MODE_FUNCTION,
      connection_info: {}
    }
  }

  const getDefaultState = () => {
    return {
      edit_mount: {},
      setup_template: {},
      active_step: 'choose-source',
      error_msg: ''
    }
  }

  export default {
    props: {
      title: {
        type: String,
        default: ''
      },
      showHeader: {
        type: Boolean,
        default: true
      },
      showFooter: {
        type: Boolean,
        default: true
      },
      mount: {
        type: Object,
        default: () => {}
      },
      mode: {
        type: String,
        default: 'add' // 'add' or 'edit'
      },
      mountType: {
        type: String,
        default: 'integration' // 'integration' or 'mount'
      },
      activeStep: {
        type: String,
        default: 'choose-source'
      },
    },
    mixins: [MixinConnection],
    components: {
      HeaderBar,
      ButtonBar,
      IconList,
      ServiceIconWrapper,
      ConnectionEditPanel,
      FunctionMountSetupWizard,
    },
    watch: {
      mount: {
        handler: 'initSelf',
        immediate: true,
        deep: true
      }
    },
    data() {
      return getDefaultState()
    },
    computed: {
      ...mapState({
        active_team_name: state => state.teams.active_team_name,
      }),
      cname() {
        return _.get(this.mount, 'name', '')
      },
      integrations() {
        return this.getProductionIntegrations()
      },
      has_mount() {
        var eid = _.get(this.edit_mount, 'eid', '')
        return eid.length > 0
      },
      has_prompts() {
        return _.get(this.setup_template, 'prompts', []).length > 0
      },
      our_title() {
        if (this.title.length > 0) {
          return this.title
        }

        switch (this.mountType) {
          case 'integration':
            return this.mode == 'edit' ? `Edit "${this.cname}" Integration` : 'New Integration'
          case 'mount':
            return this.mode == 'edit' ? `Edit "${this.cname}" Function Mount` : 'New Function Mount'
        }
      },
      submit_label() {
        return this.mode == 'edit' ? 'Save changes' : 'Create function mount'
      },
    },
    methods: {
      ...mapGetters('integrations', {
        'getProductionIntegrations': 'getProductionIntegrations',
      }),
      initSelf() {
        // reset our local component data
        _.assign(this.$data, getDefaultState())

        // reset local objects
        this.edit_mount = _.assign({}, this.edit_mount, _.cloneDeep(this.mount))
        this.active_step = this.activeStep
      },
      filterByFunctionMount(connection) {
        return this.$_Connection_isFunctionMount(connection)
      },
      createPendingIntegrationConnection(item) {
        var team_name = this.active_team_name
        var integration_attrs = _.cloneDeep(_.get(item, 'connection', {}))
        var slug = integration_attrs.name

        var attrs = _.assign({}, getDefaultAttrs(), integration_attrs, {
          eid_status: OBJECT_STATUS_PENDING,
          name: `${slug}-` + getNameSuffix(16)
        })

        return this.$store.dispatch('connections/create', { team_name, attrs }).then(response => {
          var mount = _.cloneDeep(response.data)
          mount.name = this.$store.getUniqueName(slug, 'connections')
          this.omitMaskedValues(mount)
          this.fetchFunctionPackSetupTemplate()
        })
      },
      createPendingFunctionMount(item) {
        var ctype = item.connection_type
        var ctitle = item.title || item.service_name
        var service_slug = slugify(ctitle)
        var team_name = this.active_team_name

        // when using this panel in 'add' mode, if the panel
        // is created with a `mount` prop, use the attributes of
        // this prop when creating the pending connection -- we use this
        // for creating 'function mount' connections
        var mount_attrs = _.cloneDeep(this.mount)

        var attrs = _.assign({}, getDefaultAttrs(), mount_attrs, {
          eid_status: OBJECT_STATUS_PENDING,
          name: `${service_slug}-` + getNameSuffix(16),
          title: ctitle,
          connection_type: ctype
        })

        return this.$store.dispatch('connections/create', { team_name, attrs }).then(response => {
          var mount = _.cloneDeep(response.data)
          mount.name = this.$store.getUniqueName(service_slug, 'connections')
          this.omitMaskedValues(mount)
        })
      },
      omitMaskedValues(attrs) {
        var connection_info = _.get(attrs, 'connection_info', {})
        connection_info = _.omitBy(connection_info, (val, key) => { return val == '*****' })

        var update_attrs = _.assign({}, attrs, { connection_info })
        this.edit_mount = _.assign({}, this.edit_mount, update_attrs)
      },
      fetchFunctionPackSetupTemplate() {
        var team_name = this.active_team_name
        var eid = this.edit_mount.eid
        var url = _.get(this.edit_mount, 'connection_info.url', '')
        var path = url.length > 0 ? url : this.edit_mount.name + ':/flexio.yml'

        api.fetchFunctionPackSetupTemplate(team_name, path).then(response => {
          var setup_template = response.data
          var prompts = _.get(setup_template, 'prompts', [])
          this.setup_template = _.assign({}, setup_template)
          this.edit_mount = _.assign({}, this.edit_mount, { setup_template })
          this.active_step = prompts.length > 0 ? 'setup-config' : 'setup-success'
        }).catch(error => {
          this.$store.dispatch('connections/delete', { team_name, eid }).then(response => {
            this.active_step = 'setup-error'
            this.error_msg = _.get(error, 'response.data.error.message', '')
          })
        })
      },
      saveMountSetup(setup_config) {
        var team_name = this.active_team_name
        var eid = this.edit_mount.eid
        var eid_status = OBJECT_STATUS_AVAILABLE
        var name = this.edit_mount.name
        var setup_template = this.edit_mount.setup_template
        var attrs = { eid_status, name, setup_template, setup_config }

        return this.$store.dispatch('connections/update', { team_name, eid, attrs }).then(response => {
          this.edit_mount = _.assign({}, this.edit_mount, _.cloneDeep(response.data))
          this.active_step = 'setup-success'
        })
      },
      onIntegrationClick(item) {
        this.createPendingIntegrationConnection(item)
      },
      onServiceClick(item) {
        this.createPendingFunctionMount(item).then(response => {
          this.active_step = 'configure-connection'
        })
      },
      onUpdateConnection(connection) {
        this.edit_mount = _.cloneDeep(connection)
        this.fetchFunctionPackSetupTemplate()
      },
      onClose() {
        this.initSelf()
        this.$emit('close')
      },
      onCancel() {
        this.initSelf()
        this.$emit('cancel')
      },
      onSubmit() {
        if (_.get(this.edit_mount, 'eid_status') == OBJECT_STATUS_PENDING) {
          var team_name = this.active_team_name
          var eid = this.edit_mount.eid
          var eid_status = OBJECT_STATUS_AVAILABLE
          var attrs = { eid_status }

          this.$store.dispatch('connections/update', { team_name, eid, attrs }).then(response => {
            this.edit_mount = _.assign({}, this.edit_mount, _.cloneDeep(response.data))
            this.$emit('submit', this.edit_mount)
          })
        } else {
          this.$emit('submit', this.edit_mount)
        }
      },
    }
  }
</script>
