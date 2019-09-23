<template>
  <div>
    <!-- header -->
    <HeaderBar
      class="mb4"
      :title="our_title"
      @close-click="onClose"
      v-show="showHeader"
    />

    <!-- title -->
    <div
      class="flex flex-column items-center justify-center mb4"
      v-show="showTitle && active_step != 'choose-source'"
    >
      <ServiceIcon
        class="br2"
        style="background: #fff; width: 64px; height: 64px"
        :type="edit_connection.connection_type"
      />
      <div class="mt2 f4 fw6 lh-title">{{service_name}}</div>
    </div>

    <!-- steps -->
    <el-steps
      class="mb4"
      align-center
      finish-status="success"
      :active="active_step_idx"
      v-show="showSteps && active_step != 'choose-source'"
    >
      <el-step :title="'Source: ' + service_name" />
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

    <div class="h1" v-if="showFormLogo && active_step != 'choose-source'"></div>

    <!-- step 2: connect & authenticate -->
    <div
      class="flex flex-column br2 ba b--black-10 pa4"
      v-if="active_step == 'authenticate'"
    >
      <div
        class="center relative"
        style="top: -56px; margin-bottom: -2.5rem; background: #fff; padding: 0 8px"
        v-if="showFormLogo"
      >
        <ServiceIcon
          class="br2"
          style="width: 48px; height: 48px"
          :type="edit_connection.connection_type"
        />
      </div>
      <div class="tc ttu fw6 f4" style="margin-bottom: 24px">Authentication</div>
      <ConnectionAuthenticationPanel
        :connection.sync="edit_connection"
      />
    </div>

    <!-- step 3: edit properties -->
    <div
      class="flex flex-column br2 ba b--black-10 pa4"
      v-if="active_step == 'edit-properties'"
    >
      <div
        class="center relative"
        style="top: -56px; margin-bottom: -2.5rem; background: #fff; padding: 0 8px"
        v-if="showFormLogo"
      >
        <ServiceIcon
          class="br2"
          style="width: 48px; height: 48px"
          :type="edit_connection.connection_type"
        />
      </div>
      <div class="tc ttu fw6 f4" style="margin-bottom: 24px">Properties</div>
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
    var suffix = getNameSuffix(16)

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

  const getDefaultState = () => {
    return {
      edit_connection: getDefaultAttrs(),
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
      showTitle: {
        type: Boolean,
        default: false
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
          case 'authenticate': return 1
          case 'edit-properties': return 2
        }

        return 0
      },
      submit_label() {
        return this.mode == 'edit' ? 'Save changes'
          : this.active_step == 'authenticate' ? 'Continue'
          : this.active_step == 'edit-properties' ? 'Create connection'
          : 'Submit'
      },
      button_bar_attrs() {
        return _.assign({
          submitButtonText: this.submit_label
        }, this.$attrs)
      }
    },
    methods: {
      initSelf() {
        // reset our local component data
        _.assign(this.$data, getDefaultState())

        // reset local objects
        this.edit_connection = _.assign({}, this.edit_connection, _.cloneDeep(this.connection))
      },
      cinfo() {
        var ctype = _.get(this.edit_connection, 'connection_type', '')
        return _.find(cinfos, { connection_type: ctype })
      },
      createPendingConnection(item) {
        var ctype = item.connection_type
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
          title: item.service_name,
          connection_type: ctype
        })

        this.$store.dispatch('connections/create', { team_name, attrs }).then(response => {
          var connection = _.cloneDeep(response.data)
          connection.name = `${service_slug}-` + getNameSuffix(4)
          this.edit_connection = connection
          this.active_step = 'authenticate'
        })
      },
      doSubmit() {
        if (_.get(this.edit_connection, 'eid_status') == OBJECT_STATUS_PENDING) {
          var team_name = this.active_team_name
          var eid = this.edit_connection.eid
          var attrs = _.pick(this.edit_connection, ['name', 'connection_info'])
          attrs.eid_status = OBJECT_STATUS_AVAILABLE

          this.$store.dispatch('connections/update', { team_name, eid, attrs }).then(response => {
            this.edit_connection = _.assign({}, this.edit_connection, _.cloneDeep(response.data))
            this.$emit('submit', this.edit_connection)
          })
        } else {
          this.$emit('submit', this.edit_connection)
        }
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
        switch (this.active_step) {
          case 'authenticate':
            if (this.mode == 'edit') {
              this.doSubmit()
            } else {
              this.active_step = 'edit-properties'
            }
            return

          case 'edit-properties':
            this.doSubmit()
            return
        }
      },
    }
  }
</script>
