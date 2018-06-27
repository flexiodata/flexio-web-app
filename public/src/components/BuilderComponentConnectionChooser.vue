<template>
  <div>
    <div v-if="showResult && ceid.length > 0">
      <ConnectionChooserItem
        class="mb3 bt bb b--black-10"
        selected-cls="bg-white"
        :item="edit_connection"
        :connection-eid="ceid"
      >
        <slot name="buttons" slot="buttons"></slot>
      </ConnectionChooserItem>
    </div>
    <div v-else>
      <p class="ttu fw6 f7 moon-gray" v-if="has_connections">Use an existing connection</p>
      <ConnectionChooserList
        class="mb3 overflow-auto"
        style="max-height: 277px"
        selected-cls="bg-white"
        :connection-eid="connectionEid"
        :connection-type-filter="connectionTypeFilter"
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

    <!-- connect to storage dialog -->
    <el-dialog
      custom-class="el-dialog--no-header el-dialog--no-footer"
      width="51rem"
      top="8vh"
      :modal-append-to-body="false"
      :visible.sync="show_connection_dialog"
    >
      <ConnectionEditPanel
        :mode="edit_mode"
        :connection="edit_connection"
        @close="show_connection_dialog = false"
        @cancel="show_connection_dialog = false"
        @submit="tryUpdateConnection"
        v-if="show_connection_dialog"
      />
    </el-dialog>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import { CONNECTION_STATUS_AVAILABLE } from '../constants/connection-status'
  import { OBJECT_STATUS_AVAILABLE, OBJECT_STATUS_PENDING } from '../constants/object-status'
  import ConnectionEditPanel from './ConnectionEditPanel.vue'
  import ConnectionChooserList from './ConnectionChooserList.vue'
  import ConnectionChooserItem from './ConnectionChooserItem.vue'
  import MixinConnection from './mixins/connection'

  export default {
    props: {
      connectionEid: {
        type: String
      },
      connectionTypeFilter: {
        type: String,
        default: ''
      },
      showResult: {
        type: Boolean,
        default: false
      }
    },
    mixins: [MixinConnection],
    components: {
      ConnectionEditPanel,
      ConnectionChooserList,
      ConnectionChooserItem
    },
    watch: {
      our_connection: {
        handler: 'initSelf',
        immediate: true,
        deep: true
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
      ceid() {
        return _.get(this.edit_connection, 'eid', '')
      },
      connections() {
        return this.getAvailableConnections()
      },
      connections_of_type() {
        var connection_type = this.connectionTypeFilter
        return _.filter(this.connections, { connection_type })
      },
      has_connections() {
        if (this.connectionTypeFilter.length > 0) {
          return this.connections_of_type.length > 0
        }
        return this.connections.length > 0
      },
      has_available_connection() {
        return _.get(this.our_connection, 'connection_status', '') == CONNECTION_STATUS_AVAILABLE
      },
      our_connection() {
        return _.get(this.$store, 'state.objects[' + this.connectionEid + ']', null)
      },
      service_name() {
        return this.$_Connection_getServiceName(this.connectionTypeFilter)
      },
    },
    methods: {
      ...mapGetters([
        'getAvailableConnections'
      ]),
      getOurConnection() {
        return _.get(this.$store, 'state.objects[' + this.connectionEid + ']', null)
      },
      initSelf() {
        var c = this.getOurConnection()
        if (c) {
          c = _.cloneDeep(c)
          this.orig_connection = _.assign({}, c)
          this.edit_connection = _.assign({}, c)
        }
      },
      chooseConnection(connection) {
        if (_.isNil(connection)) {
          this.edit_connection = undefined
          this.$emit('choose-connection', null)
          this.$emit('update:connectionEid', '')
        } else {
          this.edit_connection = connection
          this.$emit('choose-connection', connection)
          this.$emit('update:connectionEid', connection.eid)
        }
      },
      fixConnection(connection) {
        this.chooseConnection(connection)
        this.edit_mode = 'edit'
        this.edit_connection = connection
        this.show_connection_dialog = true
      },
      createPendingConnection() {
        if (this.connectionTypeFilter.length > 0) {
          var attrs = {
            eid_status: OBJECT_STATUS_PENDING,
            name: this.service_name,
            connection_type: this.connectionTypeFilter
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

            /*
            if (is_pending) {
              var analytics_payload = _.pick(attrs, ['eid', 'name', 'alias', 'description', 'connection_type'])
              this.$store.track('Created Connection In Template Builder', analytics_payload)
            }
            */

            this.chooseConnection(connection)
            this.show_connection_dialog = false
          }
           else
          {
            //this.$store.track('Created Connection In Template Builder (Error)')
          }
        })
      }
    }
  }
</script>
