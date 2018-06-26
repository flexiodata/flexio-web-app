<template>
  <div>
    <div
      class="tl pb3"
      v-if="showTitle"
    >
      <h3 class="fw6 f3 mid-gray mt0 mb2" v-if="title.length > 0">{{title}}</h3>
      <h3 class="fw6 f3 mid-gray mt0 mb2" v-else-if="ctype.length > 0">Connect to {{service_name}}</h3>
      <h3 class="fw6 f3 mid-gray mt0 mb2" v-else>Choose a connection</h3>
    </div>
    <div
      class="pb3 mid-gray marked"
      v-html="description"
      v-show="show_description"
    >
    </div>

    <div v-show="show_controls && !show_summary">
      <p class="ttu fw6 f7 moon-gray" v-if="has_connections">Use an existing connection</p>
      <ConnectionChooserList
        class="mb3 overflow-auto"
        style="max-height: 277px"
        selected-cls="bg-white"
        :connection="store_connection"
        :connection-type-filter="ctype"
        :show-selection-checkmark="true"
        @item-activate="chooseConnection"
        @item-fix="fixConnection"
        v-if="has_connections"
      />
      <p class="ttu fw6 f7 moon-gray" v-if="has_connections">&mdash; or &mdash;</p>
      <div class="mt3">
        <el-button
          class="ttu b"
          @click="createPendingConnection"
        >
          Set up a new connection
        </el-button>
      </div>
    </div>

    <div v-if="show_summary && has_available_connection">
      <ConnectionChooserItem
        class="mb3 bt bb b--black-10"
        selected-cls="bg-white"
        :item="store_connection"
        :connection-eid="ceid"
      >
        <el-button
          slot="buttons"
          plain
          size="tiny"
          class="ttu b"
          @click="chooseConnection(null)"
        >
          Use Different Connection
        </el-button>
      </ConnectionChooserItem>
    </div>

    <!-- connect to storage dialog -->
    <el-dialog
      custom-class="el-dialog--no-header el-dialog--no-footer"
      width="51rem"
      top="8vh"
      :modal-append-to-body="false"
      :visible.sync="show_connection_dialog"
    >
      <ConnectionEditPanel
        :connection="edit_connection"
        :mode="edit_mode"
        @close="show_connection_dialog = false"
        @cancel="show_connection_dialog = false"
        @submit="tryUpdateConnection"
        v-if="show_connection_dialog"
      />
    </el-dialog>
  </div>
</template>

<script>
  import marked from 'marked'
  import { mapGetters } from 'vuex'
  import { CONNECTION_STATUS_AVAILABLE } from '../constants/connection-status'
  import { OBJECT_STATUS_AVAILABLE, OBJECT_STATUS_PENDING } from '../constants/object-status'
  import ConnectionEditPanel from './ConnectionEditPanel.vue'
  import ConnectionChooserList from './ConnectionChooserList.vue'
  import ConnectionChooserItem from './ConnectionChooserItem.vue'
  import MixinConnection from './mixins/connection'

  export default {
    props: {
      item: {
        type: Object,
        required: true
      },
      index: {
        type: Number,
        required: true
      },
      activeItemIdx: {
        type: Number,
        required: true
      },
      isNextAllowed: {
        type: Boolean,
        required: false
      },
      showTitle: {
        type: Boolean,
        default: true
      },
      showSummary: {
        type: Boolean,
        default: false
      },
      builderMode: {
        type: String
      },
      connectionEid: {
        type: String
      }
    },
    mixins: [MixinConnection],
    components: {
      ConnectionEditPanel,
      ConnectionChooserList,
      ConnectionChooserItem
    },
    watch: {
      is_changed: {
        handler: 'onChange'
      },
      is_active: {
        handler: 'updateAllowNext',
        immediate: true
      },
      edit_connection: {
        handler: 'updateAllowNext',
        deep: true
      },
      connectionEid: {
        handler: 'initSelf',
        immediate: true
      }
    },
    data() {
      return {
        edit_mode: 'add',
        orig_connection: undefined,
        edit_connection: undefined,
        show_connection_dialog: false
      }
    },
    computed: {
      builder__is_wizard() {
        return this.builderMode == 'wizard' ? true : false
      },
      is_active() {
        return this.index == this.activeItemIdx
      },
      is_before_active() {
        return this.index < this.activeItemIdx
      },
      is_changed() {
        return !_.isEqual(this.edit_connection, this.orig_connection)
      },
      show_controls() {
        return !this.builder__is_wizard || this.is_active
      },
      show_description() {
        return this.show_controls && this.description.length > 0
      },
      show_summary() {
        return (this.builder__is_wizard && this.is_before_active) || this.showSummary
      },
      title() {
        return _.get(this.item, 'title', '')
      },
      description() {
        return marked(_.get(this.item, 'description', ''))
      },
      ceid() {
        return _.get(this.edit_connection, 'eid', null)
      },
      ctype() {
        return _.get(this.item, 'connection_type', '')
      },
      connections() {
        return this.getAvailableConnections()
      },
      connections_of_type() {
        return _.filter(this.connections, { connection_type: this.ctype })
      },
      has_connections() {
        if (this.ctype.length > 0)
          return this.connections_of_type.length > 0

        return this.connections.length > 0
      },
      store_connection() {
        return _.find(this.connections, { eid: this.ceid }, null)
      },
      has_available_connection() {
        return _.get(this.store_connection, 'connection_status', '') == CONNECTION_STATUS_AVAILABLE
      },
      service_name() {
        return this.$_Connection_getServiceName(this.ctype)
      },
    },
    methods: {
      ...mapGetters([
        'getAvailableConnections'
      ]),
      initSelf() {
        var c = _.get(this.$store, 'state.objects[' + this.connectionEid + ']', null)
        if (c) {
          c = _.cloneDeep(c)
          this.orig_connection = c
          this.edit_connection = c
        }
      },
      chooseConnection(connection) {
        var key = _.get(this.item, 'variable', 'connection_eid')
        var form_values = _.get(this.item, 'extra_values', {})

        if (_.isNil(connection)) {
          form_values[key] = ''
          this.edit_connection = undefined
          this.$emit('item-change', form_values, this.index)
          this.$emit('update:connectionEid', '')
        } else {
          form_values[key] = connection.eid
          this.edit_connection = connection
          this.$emit('item-change', form_values, this.index)
          this.$emit('update:connectionEid', connection.eid)
        }

        if (!this.builder__is_wizard) {
          this.$emit('active-item-change', this.index)
        }
      },
      fixConnection(connection) {
        this.chooseConnection(connection)
        this.edit_mode = 'edit'
        this.edit_connection = connection
        this.show_connection_dialog = true
      },
      createPendingConnection() {
        if (this.ctype.length > 0) {
          var attrs = {
            eid_status: OBJECT_STATUS_PENDING,
            name: this.service_name,
            connection_type: this.ctype
          }

          this.$store.dispatch('createConnection', { attrs }).then(response => {
            var connection = response.body
            this.edit_mode = 'add'
            this.edit_connection = connection
            this.show_connection_dialog = true
          })
        } else {
          this.edit_mode = 'add'
          this.edit_connection = undefined
          this.show_connection_dialog = true
        }
      },
      tryUpdateConnection(attrs) {
        var eid = attrs.eid
        var is_pending = attrs.eid_status === OBJECT_STATUS_PENDING

        attrs = _.pick(attrs, ['name', 'alias', 'description', 'connection_info'])
        _.assign(attrs, { eid_status: OBJECT_STATUS_AVAILABLE })

        // update the connection and make it available
        this.$store.dispatch('updateConnection', { eid, attrs }).then(response => {
          var connection = response.body

          if (response.ok)
          {
            // TODO: shouldn't we do this in the ConnectionEditPanel?
            // try to connect to the connection
            this.$store.dispatch('testConnection', { eid, attrs })

            if (is_pending)
            {
              var analytics_payload = _.pick(attrs, ['eid', 'name', 'alias', 'description', 'connection_type'])
              this.$store.track('Created Connection In Template Builder', analytics_payload)
            }

            this.chooseConnection(connection)
            this.show_connection_dialog = false
          }
           else
          {
            this.$store.track('Created Connection In Template Builder (Error)')
          }
        })
      },
      onChange(val) {
        if (!this.builder__is_wizard && val === true) {
          this.$emit('active-item-change', this.index)
        }
      },
      updateAllowNext() {
        this.$emit('update:isNextAllowed', this.has_available_connection)
      }
    }
  }
</script>
