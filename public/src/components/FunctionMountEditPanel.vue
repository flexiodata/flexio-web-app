<template>
  <div>
    <!-- header -->
    <HeaderBar
      class="mb4"
      :title="our_title"
      @close-click="onClose"
      v-show="showHeader"
    />

    <!-- step 1: choose source -->
    <div v-if="active_step == 'choose-source' && !has_mount">
      <IconList
        items="mounts"
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
      :mode="mode"
      :active-step="'authentication'"
      :connection="edit_mount"
      :show-header="false"
      :filter-by="filterByFunctionMount"
      @cancel="onCancel"
      @submit="onUpdateConnection"
      v-if="active_step == 'configure-connection' && has_mount"
    />

    <!-- step 3: setup config -->
    <FunctionMountConfigWizard
      :definition="manifest"
      @done-click="saveMountSetupConfig"
      v-if="active_step == 'setup-config' && has_manifest"
    />
    <div

      v-else-if="active_step == 'setup-config' && !has_manifest"
    >
      <p>Your function mount was created successfully. Click <strong>"Done"</strong> to begin importing functions.</p>
      <ButtonBar
        class="mt4"
        :cancel-button-visible="false"
        :submit-button-text="'Done'"
        @submit-click="onSubmit"
      />
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
  import { mapState } from 'vuex'
  import { OBJECT_STATUS_AVAILABLE, OBJECT_STATUS_PENDING } from '@/constants/object-status'
  import api from '@/api'
  import { slugify } from '@/utils'
  import HeaderBar from '@/components/HeaderBar'
  import ButtonBar from '@/components/ButtonBar'
  import IconList from '@/components/IconList'
  import ConnectionEditPanel from '@/components/ConnectionEditPanel'
  import FunctionMountConfigWizard from '@/components/FunctionMountConfigWizard'
  import MixinConnection from '@/components/mixins/connection'

  const CONNECTION_MODE_RESOURCE = 'R'
  const CONNECTION_MODE_FUNCTION = 'F'

  const defaultAttrs = () => {
    var suffix = getNameSuffix()

    return {
      eid: null,
      eid_status: OBJECT_STATUS_PENDING,
      name: `connection-${suffix}`,
      title: '',
      description: '',
      connection_type: '',
      connection_mode: CONNECTION_MODE_RESOURCE,
      connection_info: {}
    }
  }

  const getNameSuffix = (length) => {
    return randomstring.generate({
      length,
      charset: 'alphabetic',
      capitalization: 'lowercase'
    })
  }

  const getDefaultState = (component) => {
    return {
      edit_mount: {},
      manifest: {},
      active_step: 'choose-source'
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
      }
    },
    mixins: [MixinConnection],
    components: {
      HeaderBar,
      ButtonBar,
      IconList,
      ConnectionEditPanel,
      FunctionMountConfigWizard,
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
        active_team_name: state => state.teams.active_team_name
      }),
      cname() {
        return _.get(this.mount, 'name', '')
      },
      has_mount() {
        var eid = _.get(this.edit_mount, 'eid', '')
        return eid.length > 0
      },
      has_manifest() {
        return _.get(this.manifest, 'prompts', []).length > 0
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
      initSelf() {
        // reset our local component data
        _.assign(this.$data, getDefaultState())

        // reset local objects
        this.edit_mount = _.assign({}, this.edit_mount, _.cloneDeep(this.mount))
      },
      filterByFunctionMount(connection) {
        return this.$_Connection_isFunctionMount(connection)
      },
      createIntegrationConnection(item) {
        var team_name = this.active_team_name
        var attrs = _.get(item, 'connection', {})
        attrs.name = attrs.name + '-' + getNameSuffix(4)
        attrs.eid_status = OBJECT_STATUS_PENDING

        return this.$store.dispatch('connections/create', { team_name, attrs }).then(response => {
          this.edit_mount = _.assign({}, this.edit_mount, response.data)
          this.fetchFunctionPackConfig()
        })
      },
      createPendingFunctionMount(item) {
        var ctitle = item.title || item.service_name
        var service_slug = slugify(ctitle)
        var team_name = this.active_team_name

        // when using this panel in 'add' mode, if the panel
        // is created with a `mount` prop, use the attributes of
        // this prop when creating the pending connection -- we use this
        // for creating 'function mount' connections
        var prop_mount_attrs = _.cloneDeep(this.mount)

        var attrs = _.assign({}, defaultAttrs(), prop_mount_attrs, {
          eid_status: OBJECT_STATUS_PENDING,
          name: `${service_slug}-` + getNameSuffix(16),
          title: ctitle,
          connection_type: item.connection_type
        })

        return this.$store.dispatch('connections/create', { team_name, attrs }).then(response => {
          var mount = _.cloneDeep(response.data)
          mount.name = `${service_slug}-` + getNameSuffix(4)
          this.omitMaskedValues(mount)
        })
      },
      fetchFunctionPackConfig() {
        var path = this.edit_mount.name + ':/flexio.yml'
        api.fetchFunctionPackConfig(this.active_team_name, path).then(response => {
          this.active_step = 'setup-config'
          this.manifest = _.assign({}, response.data)
        })
      },
      saveMountSetupConfig(setup_config) {
        var team_name = this.active_team_name
        var eid = this.edit_mount.eid
        var eid_status = OBJECT_STATUS_AVAILABLE
        var attrs = { eid_status, setup_config }

        return this.$store.dispatch('connections/update', { team_name, eid, attrs }).then(response => {
          this.edit_mount = _.assign({}, this.edit_mount, _.cloneDeep(response.data))
          this.$emit('submit', this.edit_mount)
        })
      },
      omitMaskedValues(attrs) {
        var connection_info = _.get(attrs, 'connection_info', {})
        connection_info = _.omitBy(connection_info, (val, key) => { return val == '*****' })

        var update_attrs = _.assign({}, attrs, { connection_info })
        this.edit_mount = _.assign({}, this.edit_mount, update_attrs)
      },
      onIntegrationClick(item) {
        this.createIntegrationConnection(item)
      },
      onServiceClick(item) {
        this.createPendingFunctionMount(item).then(response => {
          this.active_step = 'configure-connection'
        })
      },
      onUpdateConnection(connection) {
        this.edit_mount = _.cloneDeep(connection)
        this.fetchFunctionPackConfig()
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
