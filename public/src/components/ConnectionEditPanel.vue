<template>
  <div>
    <!-- header -->
    <HeaderBar
      class="mb4"
      :title="our_title"
      @close-click="onClose"
      v-show="showHeader"
    />

    <el-steps
      class="pb3 mb3 bb b--black-05"
      align-center
      :active="active_step_idx"
      v-show="showSteps"
    >
      <el-step title="Choose Source" />
      <el-step title="Connect &amp; Authenticate" />
      <el-step title="Edit Properties" />
    </el-steps>

    <!-- step 1: choose source -->
    <IconList
      items="services"
      :filter-by="filterBy"
      @item-click="createPendingConnection"
      v-show="active_step == 'choose-source'"
    />

    <!-- step 2: connect & authenticate -->
    <ConnectionAuthenticationPanel
      :connection.sync="edit_connection"
      :mode="mode"
      v-if="active_step == 'authenticate'"
    />

    <!-- step 2: edit properties -->
    <ConnectionPropertiesPanel
      :connection="edit_connection"
      :mode="mode"
      v-if="active_step == 'edit-properties'"
    />

    <!-- footer -->
    <ButtonBar
      class="mt4"
      @cancel-click="onCancel"
      @submit-click="onSubmit"
      v-bind="$attrs"
      v-show="showFooter && active_step != 'choose-source'"
    />
  </div>
</template>

<script>
  import randomstring from 'randomstring'
  import { mapState } from 'vuex'
  import { OBJECT_STATUS_PENDING } from '@/constants/object-status'
  import { slugify } from '@/utils'
  import HeaderBar from '@/components/HeaderBar'
  import ButtonBar from '@/components/ButtonBar'
  import IconList from '@/components/IconList'
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

  const getDefaultName = (length) => {
    var suffix = getNameSuffix(16)
    return `connection-${suffix}`
  }

  const getDefaultAttrs = () => {
    return {
      eid: null,
      eid_status: OBJECT_STATUS_PENDING,
      name: getDefaultName(),
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
      ConnectionAuthenticationPanel,
      ConnectionPropertiesPanel,
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
        return _.get(this.connection, 'name', '')
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

        return 1
      }
    },
    methods: {
      initSelf() {
        // reset our local component data
        _.assign(this.$data, getDefaultState())

        // reset local objects
        this.edit_connection = _.assign({}, this.edit_connection, _.cloneDeep(this.connection))
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
