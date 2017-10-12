<template>
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100">
      <spinner size="large" message="Loading connections..."></spinner>
    </div>
  </div>
  <div v-else>
    <!-- control bar -->
    <div class="pa3 ph4-l bb b--black-05">
      <div class="flex flex-row">
        <div class="flex-fill flex flex-row items-center">
          <div class="f2">Connections</div>
        </div>
        <div class="flex-none flex flex-row items-center">
          <btn btn-md btn-primary class="btn-add ttu b ba" @click="openAddModal">New</btn>
        </div>
      </div>
    </div>

    <div class="flex flex-row h-100" v-if="connections.length > 0">
      <connection-chooser-list
        class="br b--black-05 overflow-y-auto"
        layout="list"
        filter-items="storage"
        item-cls="ma1 pa2 pr5-l br1 darken-05"
        :override-item-cls="true"
        :show-selection="true"
        :auto-select-first-item="true"
        @item-activate="onConnectionActivate"
      />
      <connection-raw-edit-panel
        class="flex-fill"
        :connection="connection"
        @submit="saveConnection"
      />
      <div class="flex-fill" v-if="false">
        <div class="pa3 pa4-l pb3-l " style="max-width: 1152px" v-if="connection">
          <div class="f4 mb3">Headers</div>
          <connection-info-configure-panel :connection="connection" />
        </div>
      </div>
    </div>
    <div class="flex flex-column justify-center h-100" v-else>
      <empty-item>
        <i slot="icon" class="material-icons">repeat</i>
        <span slot="text">No connections to show</span>
      </empty-item>
    </div>

    <!-- props modal (used for both add and edit) -->
    <storage-props-modal
      ref="modal-connection-props"
      @submit="tryUpdateConnection"
      @hide="show_connection_props_modal = false"
      v-if="show_connection_props_modal"
    ></storage-props-modal>
  </div>
</template>

<script>
  import { OBJECT_STATUS_AVAILABLE, OBJECT_STATUS_PENDING } from '../constants/object-status'
  import { mapState, mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import StoragePropsModal from './StoragePropsModal.vue'
  import ConnectionChooserList from './ConnectionChooserList.vue'
  import ConnectionRawEditPanel from './ConnectionRawEditPanel.vue'
  import ConnectionInfoConfigurePanel from './ConnectionInfoConfigurePanel.vue'
  import EmptyItem from './EmptyItem.vue'
  import Btn from './Btn.vue'

  export default {
    components: {
      Spinner,
      StoragePropsModal,
      ConnectionChooserList,
      ConnectionRawEditPanel,
      ConnectionInfoConfigurePanel,
      EmptyItem,
      Btn
    },
    data() {
      return {
        connection: {},
        show_connection_props_modal: false
      }
    },
    computed: {
      // mix this into the outer object with the object spread operator
      ...mapState({
        'is_fetching': 'connections_fetching',
        'is_fetched': 'connections_fetched'
      }),
      connections() {
        return this.getAllConnections()
      },
      ctype() {
        return _.get(this.connection, 'connection_type', '')
      },
      cname() {
        return _.get(this.connection, 'name', '')
      },
      has_connection() {
        return this.ctype.length > 0
      }
    },
    created() {
      this.tryFetchConnections()
    },
    methods: {
      ...mapGetters([
        'getAllConnections'
      ]),
      openAddModal() {
        this.show_connection_props_modal = true
        this.$nextTick(() => { this.$refs['modal-connection-props'].open() })
        analytics.track('Clicked New Connection Button')
      },
      openEditModal(item) {
        this.show_connection_props_modal = true
        this.$nextTick(() => { this.$refs['modal-connection-props'].open(item, 'edit') })
      },
      tryFetchConnections() {
        if (!this.is_fetched && !this.is_fetching)
          this.$store.dispatch('fetchConnections')
      },
      tryUpdateConnection(attrs, modal) {
        var eid = attrs.eid
        var ctype = _.get(attrs, 'connection_type', '')
        var is_pending = _.get(attrs, 'eid_status', '') === OBJECT_STATUS_PENDING

        attrs = _.pick(attrs, ['name', 'ename', 'description', 'connection_info'])
        _.assign(attrs, { eid_status: OBJECT_STATUS_AVAILABLE })

        // update the connection and make it available
        this.$store.dispatch('updateConnection', { eid, attrs }).then(response => {
          if (response.ok)
          {
            if (modal)
              modal.close()

            // try to connect to the connection
            this.$store.dispatch('testConnection', { eid, attrs })

            if (is_pending)
            {
              var analytics_payload = _.pick(attrs, ['name', 'ename', 'description'])
              _.set(analytics_payload, 'eid', eid)
              _.set(analytics_payload, 'connection_type', ctype)
              analytics.track('Created Connection', analytics_payload)
            }
          }
           else
          {
            // TODO: add error handling
          }
        })
      },
      tryDeleteConnection(attrs) {
        this.$store.dispatch('deleteConnection', { attrs })
      },
      onConnectionActivate(item) {
        this.connection = _.assign({}, item)
      },
      saveConnection(connection, info) {
        var conn = _.set(connection, 'connection_info', info)
        this.tryUpdateConnection(conn)
      }
    }
  }
</script>
