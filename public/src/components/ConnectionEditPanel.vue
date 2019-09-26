<template>
  <div>
    <!-- header -->
    <HeaderBar
      class="mb4"
      :title="our_title"
      @close-click="onClose"
      v-show="showHeader"
    />

    <!-- steps -->
    <el-steps
      class="mb4"
      align-center
      finish-status="success"
      :active="active_step_idx"
      v-show="showSteps && mode == 'add'"
    >
      <el-step :title="service_name.length == 0 ? 'Choose Source' : 'Source: ' + service_name" />
      <el-step title="Authentication" />
      <el-step title="Properties" />
    </el-steps>

    <!-- step 1: choose source -->
    <IconList
      items="services"
      :filter-by="filterBy"
      @item-click="createPendingConnection"
      v-if="active_step == 'choose-source'"
    />

    <!-- step 2: connect & authenticate -->
    <div class="h1" v-if="showFormLogo && active_step == 'authentication'"></div>
    <div
      class="flex flex-column br2 ba b--black-10 pa4"
      v-if="active_step == 'authentication'"
    >
      <div
        class="flex flex-column justify-center form-logo"
        v-if="showFormLogo"
      >
        <ServiceIcon
          class="form-logo-icon"
          :type="edit_connection.connection_type"
        />
      </div>
      <div class="mt3" v-if="showSteps && mode == 'add'"></div>
      <div class="tc ttu fw6 f4 form-title" v-else>Authentication</div>
      <ConnectionAuthenticationPanel
        :connection.sync="edit_connection"
      />
    </div>

    <!-- step 3: edit properties -->
    <div class="h1" v-if="showFormLogo && active_step == 'properties'"></div>
    <div
      class="flex flex-column br2 ba b--black-10 pa4"
      v-if="active_step == 'properties'"
    >
      <div
        class="form-logo"
        v-if="showFormLogo"
      >
        <ServiceIcon
          class="form-logo-icon"
          :type="edit_connection.connection_type"
        />
      </div>
      <div class="mt3" v-if="showSteps && mode == 'add'"></div>
      <div class="tc ttu fw6 f4 form-title" v-else>Properties</div>
      <ConnectionPropertiesPanel
        :connection.sync="edit_connection"
        :show-header="false"
        :show-footer="false"
        :mode="mode"
      />
    </div>

    <!-- footer -->
    <ButtonBar
      class="mt4"
      @cancel-click="onCancel"
      @submit-click="onSubmit"
      v-bind="button_bar_attrs"
      v-show="showFooter && active_step != 'choose-source'"
    />
  </div>
</template>

<script>
  import randomstring from 'randomstring'
  import { mapState } from 'vuex'
  import { OBJECT_STATUS_AVAILABLE, OBJECT_STATUS_PENDING } from '@/constants/object-status'
  import * as cinfos from '@/constants/connection-info'
  import { slugify } from '@/utils'
  import HeaderBar from '@/components/HeaderBar'
  import ButtonBar from '@/components/ButtonBar'
  import IconList from '@/components/IconList'
  import ServiceIcon from '@/components/ServiceIcon'
  import ConnectionAuthenticationPanel from '@/components/ConnectionAuthenticationPanel'
  import ConnectionPropertiesPanel from '@/components/ConnectionPropertiesPanel'

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
      connection_mode: CONNECTION_MODE_RESOURCE,
      connection_info: {}
    }
  }

  const getDefaultState = () => {
    return {
      edit_connection: {},
      active_step: 'choose-source'
    }
  }

  export default {
    inheritAttrs: false,
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
      showSteps: {
        type: Boolean,
        default: false
      },
      showFormLogo: {
        type: Boolean,
        default: true
      },
      connection: {
        type: Object,
        default: () => {}
      },
      filterBy: {
        type: Function
      },
      mode: {
        type: String,
        default: 'add' // 'add' or 'edit'
      },
      activeStep: {
        type: String,
        default: 'choose-source'
      },
      lastStep: {
        type: String,
        default: 'properties'
      }
    },
    components: {
      HeaderBar,
      ButtonBar,
      IconList,
      ServiceIcon,
      ConnectionAuthenticationPanel,
      ConnectionPropertiesPanel,
    },
    watch: {
      connection: {
        handler: 'initSelf',
        immediate: true,
        deep: true
      },
      edit_connection: {
        handler: 'emitChange',
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
        return _.get(this.connection, 'name', '')
      },
      service_name() {
        return _.result(this, 'cinfo.service_name', '')
      },
      our_title() {
        if (this.title.length > 0) {
          return this.title
        }

        return this.mode == 'edit' ? `Edit "${this.cname}" Connection` : 'New Connection'
      },
      active_step_idx() {
        switch (this.active_step) {
          case 'choose-source': return 0
          case 'authentication': return 1
          case 'properties': return 2
        }

        return 0
      },
      submit_label() {
        if (this.active_step == this.lastStep) {
          return this.mode == 'edit' ? 'Save changes' : 'Create connection'
        }

        return 'Continue'
      },
      button_bar_attrs() {
        // we must use kebab-case here since that is how
        // the attribute will be passed to this component
        return _.assign({}, {
          'submit-button-text': this.submit_label
        }, this.$attrs)
      }
    },
    methods: {
      initSelf() {
        // reset our local component data
        _.assign(this.$data, getDefaultState())

        // reset local objects
        this.edit_connection = _.assign({}, this.edit_connection, _.cloneDeep(this.connection))
        this.active_step = this.activeStep
      },
      emitChange() {
        this.$emit('connection-change', this.edit_connection)
      },
      cinfo() {
        var ctype = _.get(this.edit_connection, 'connection_type', '')
        return _.find(cinfos, { connection_type: ctype })
      },
      createPendingConnection(item) {
        var ctype = item.connection_type
        var ctitle = item.service_name
        var service_slug = slugify(item.service_name)
        var team_name = this.active_team_name

        // when using the connection edit panel in 'add' mode, if the panel
        // is created with a `connection` prop, use the attributes of
        // this prop when creating the pending connection -- we use this
        // for creating 'function mount' connections
        var prop_connection_attrs = _.cloneDeep(this.connection)

        var attrs = _.assign({}, getDefaultAttrs(), prop_connection_attrs, {
          eid_status: OBJECT_STATUS_PENDING,
          name: `${service_slug}-` + getNameSuffix(16),
          title: ctitle,
          connection_type: ctype
        })

        this.$store.dispatch('connections/create', { team_name, attrs }).then(response => {
          var connection = _.cloneDeep(response.data)
          connection.name = this.$store.getUniqueName(service_slug, 'connections')
          this.omitMaskedValues(connection)
          this.active_step = 'authentication'
        })
      },
      omitMaskedValues(attrs) {
        var connection_info = _.get(attrs, 'connection_info', {})
        connection_info = _.omitBy(connection_info, (val, key) => { return val == '*****' })

        var update_attrs = _.assign({}, attrs, { connection_info })
        this.edit_connection = _.assign({}, this.edit_connection, update_attrs)
      },
      doSubmit() {
        var team_name = this.active_team_name
        var eid = this.edit_connection.eid
        var attrs = _.pick(this.edit_connection, ['name', 'connection_info'])
        attrs.eid_status = OBJECT_STATUS_AVAILABLE

        return this.$store.dispatch('connections/update', { team_name, eid, attrs }).then(response => {
          this.edit_connection = _.assign({}, this.edit_connection, _.cloneDeep(response.data))
          this.$emit('submit', this.edit_connection)
        })
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
        if (this.active_step == this.lastStep) {
          this.doSubmit()
          return
        }

        switch (this.active_step) {
          case 'authentication':
            this.active_step = 'properties'
            return

          case 'properties':
            this.doSubmit()
            return
        }
      },
    }
  }
</script>

<style lang="stylus" scoped>
  .form-logo
    background: #fff
    height: 48px
    margin: -56px auto 24px
    padding: 0 8px

  .form-logo-icon
    border-radius: 4px
    max-height: 48px

  .form-title
    margin-bottom: 24px
</style>
