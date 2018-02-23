<template>
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100">
      <spinner size="large" message="Loading connections..."></spinner>
    </div>
  </div>
  <div class="flex flex-column" v-else>
    <!-- control bar -->
    <div class="pa3 ph4-l relative bg-white bb b--black-05">
      <div class="flex flex-row">
        <div class="flex-fill flex flex-row items-center">
          <div class="f2">Connections</div>
        </div>
        <div class="flex-none flex flex-row items-center ml3">
          <el-button
            type="primary"
            class="ttu"
            :disabled="is_new"
            @click="openNewConnectionModal"
          >New Connection</el-button>
          <el-button
            type="primary"
            class="ttu"
            :disabled="is_new"
            @click="createPendingConnection"
            v-if="false"
          >New Connection</el-button>
          <el-button
            type="primary"
            class="ttu"
            :disabled="is_new"
            @click="openAddConnectionModal"
          >New Storage</el-button>
        </div>
      </div>
    </div>

    <div class="flex flex-row h-100" v-if="is_new || connections.length > 0">
      <abstract-list
        ref="list"
        class="br b--black-05 overflow-y-auto"
        layout="list"
        item-component="AbstractConnectionChooserItem"
        :auto-select-item="true"
        :disabled="is_new"
        :items="connections"
        :item-options="{
          'item-cls': 'min-w5 pa3 pr2 darken-05',
          'item-style': 'margin: 0.125rem',
          'show-dropdown': true,
          'dropdown-items': ['delete']
        }"
        @item-activate="onConnectionActivate"
        @item-delete="tryDeleteConnection"
        v-if="connections.length > 0"
      />
      <div class="flex-fill overflow-y-auto" v-if="connection">
        <connection-edit-panel
          class="pa3 pa4-l"
          style="max-width: 60rem"
          :show-header="false"
          :show-steps="false"
          :connection="connection"
          @close="cancelChanges"
          @submit="tryUpdateConnection"
        />
      </div>

      <!-- connection props modal (used for both add and edit) -->
      <storage-props-modal
        ref="modal-connection-props"
        @submit="tryUpdateConnection"
        @hide="show_connection_props_modal = false"
        v-if="show_connection_props_modal"
      ></storage-props-modal>

      <connection-dialog
        custom-class="no-header no-footer"
        width="51rem"
        top="8vh"
        :modal-append-to-body="false"
        :visible.sync="show_new_connection_modal"
        :show-close="false"
        @submit="tryUpdateConnection"
        v-if="show_new_connection_modal"
      />
    </div>
    <div class="flex flex-column justify-center h-100" v-else>
      <empty-item>
        <i slot="icon" class="material-icons">repeat</i>
        <span slot="text">No connections to show</span>
      </empty-item>
    </div>
  </div>
</template>

<script>
  import { CONNECTION_TYPE_HTTP } from '../constants/connection-type'
  import { OBJECT_STATUS_AVAILABLE, OBJECT_STATUS_PENDING } from '../constants/object-status'
  import { mapState, mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import AbstractList from './AbstractList.vue'
  import ConnectionEditPanel from './ConnectionEditPanel.vue'
  import StoragePropsModal from './StoragePropsModal.vue'
  import ConnectionDialog from './ConnectionDialog.vue'
  import EmptyItem from './EmptyItem.vue'
  import Btn from './Btn.vue'

  export default {
    components: {
      Spinner,
      AbstractList,
      ConnectionEditPanel,
      StoragePropsModal,
      ConnectionDialog,
      EmptyItem,
      Btn
    },
    data() {
      return {
        connection: {},
        last_selected: {},
        is_new: false,
        show_new_connection_modal: false,
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
      openAddConnectionModal() {
        this.show_connection_props_modal = true
        this.$nextTick(() => { this.$refs['modal-connection-props'].open() })
        analytics.track('Clicked New Connection Button')
      },
      openNewConnectionModal() {
        this.show_new_connection_modal = true
      },
      createPendingConnection(item) {
        this.is_new = true
        this.connection = false

        var attrs = {
          name: 'My Connection',
          connection_type: CONNECTION_TYPE_HTTP,
          eid_status: OBJECT_STATUS_PENDING
        }

        this.$store.dispatch('createConnection', { attrs }).then(response => {
          if (response.ok)
          {
            this.connection = _.get(response, 'body', {})
          }
           else
          {
            // TODO: add error handling
          }
        })
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

            this.connection = _.assign({}, _.get(response, 'body', {}))

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
        this.$store.dispatch('deleteConnection', { attrs }).then(response => {
          if (response.ok)
            this.$nextTick(() => { this.connection = this.$refs['list'].getSelectedItem() })
        })
      },
      onConnectionActivate(item) {
        this.connection = false
        this.$nextTick(() => {
          this.connection = _.assign({}, item)
          this.last_selected = _.assign({}, item)
        })
      },
      cancelChanges(item) {
        var tmp = this.last_selected
        this.is_new = false
        this.connection = false
        this.$nextTick(() => { this.connection = _.assign({}, tmp) })
      },
      saveChanges(item) {
        this.is_new = false
        this.tryUpdateConnection(item)
      }
    }
  }
</script>
