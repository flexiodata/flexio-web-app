<template>
  <div>
    <div class="tl pb3">
      <ServiceIcon class="square-4" :type="ctype" v-if="false" />
      <h3 class="fw6 f3 mid-gray mt0 mb2">Connect to {{service_name}}</h3>
    </div>
    <div class="bt b--light-gray" v-show="is_shown">
      <ConnectionChooserList
        class="mb3"
        style="max-height: 24rem"
        layout="list"
        :connection="store_connection"
        :connection-type-filter="ctype"
        :show-selection="true"
        :show-selection-checkmark="true"
        @item-activate="chooseConnection"
        v-if="connections_of_type.length > 0"
      />
      <div class="mt3">
        <el-button
          class="ttu b"
          type="plain"
          @click="createPendingConnection"
        >
          Set up a new connection
        </el-button>
      </div>
    </div>

    <!-- connect to storage dialog -->
    <el-dialog
      custom-class="no-header no-footer"
      width="51rem"
      top="8vh"
      :modal-append-to-body="false"
      :visible.sync="show_connection_dialog"
    >
      <ConnectionEditPanel
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
  import { mapState, mapGetters } from 'vuex'
  import { CONNECTION_STATUS_AVAILABLE } from '../constants/connection-status'
  import { OBJECT_STATUS_AVAILABLE, OBJECT_STATUS_PENDING } from '../constants/object-status'
  import ServiceIcon from './ServiceIcon.vue'
  import ConnectionEditPanel from './ConnectionEditPanel.vue'
  import ConnectionChooserList from './ConnectionChooserList.vue'
  import MixinConnectionInfo from './mixins/connection-info'

  export default {
    props: {
      item: {
        type: Object,
        required: true
      },
      index: {
        type: Number,
        required: true
      }
    },
    mixins: [MixinConnectionInfo],
    components: {
      ServiceIcon,
      ConnectionEditPanel,
      ConnectionChooserList
    },
    data() {
      return {
        edit_connection: null,
        show_connection_dialog: false
      }
    },
    computed: {
      ...mapState({
        active_prompt_idx: state => state.builder.active_prompt_idx
      }),
      ceid() {
        return _.get(this.item, 'connection_eid', null)
      },
      ctype() {
        return _.get(this.item, 'connection_type', '')
      },
      connections() {
        return this.getAllConnections()
      },
      connections_of_type() {
        return _.filter(this.connections, { connection_type: this.ctype })
      },
      store_connection() {
        return _.find(this.connections, { eid: this.ceid }, null)
      },
      store_connection_status_available() {
        return _.get(this.store_connection, 'connection_status', '') == CONNECTION_STATUS_AVAILABLE
      },
      service_name() {
        return this.getConnectionInfo(this.ctype, 'service_name')
      },
      is_shown() {
        return this.index <= this.active_prompt_idx
      }
    },
    methods: {
      ...mapGetters([
        'getAllConnections'
      ]),
      chooseConnection(connection) {
        this.$store.commit('BUILDER__UPDATE_ACTIVE_ITEM', { connection_eid: connection.eid })
      },
      createPendingConnection() {
        var attrs = {
          eid_status: OBJECT_STATUS_PENDING,
          name: this.service_name,
          connection_type: this.ctype
        }

        this.$store.dispatch('createConnection', { attrs }).then(response => {
          var connection = response.body
          this.edit_connection = connection
          this.show_connection_dialog = true
        })
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
    }
  }
</script>
