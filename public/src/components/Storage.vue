<template>
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100">
      <spinner size="large" message="Loading storage items..."></spinner>
    </div>
  </div>
  <div v-else>
    <!-- control bar -->
    <div class="pa3 ph4-l bb b--black-05">
      <div class="flex flex-row">
        <div class="flex-fill flex flex-row items-center">
          <div class="f2">Storage</div>
        </div>
        <div class="flex-none flex flex-row items-center">
          <btn btn-md btn-primary class="btn-add ttu b ba" @click="openAddModal">New</btn>
        </div>
      </div>
    </div>

    <div class="flex flex-row h-100" v-if="connections.length > 0">
      <abstract-list
        class="br b--black-05 overflow-y-auto"
        layout="list"
        item-component="AbstractConnectionChooserItem"
        item-cls="pa2 pr4-l darken-05"
        item-style="margin: 0.125rem"
        :show-selection="true"
        :items="connections"
        @item-activate="onConnectionActivate"
      />
      <div class="flex-fill">
        <file-chooser
          class="pa2"
          :connection="connection"
          v-if="has_connection"
        />
      </div>
    </div>
    <div class="flex flex-column justify-center h-100" v-else>
      <empty-item>
        <i slot="icon" class="material-icons">repeat</i>
        <span slot="text">No storage items to show</span>
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
  import AbstractList from './AbstractList.vue'
  import FileChooser from './FileChooser.vue'
  import EmptyItem from './EmptyItem.vue'
  import Btn from './Btn.vue'
  import ConnectionInfoMixin from './mixins/connection-info'

  export default {
    mixins: [ConnectionInfoMixin],
    components: {
      Spinner,
      StoragePropsModal,
      AbstractList,
      FileChooser,
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
        return _.filter(this.getAllConnections(), this.isStorageConnection)
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
      }
    }
  }
</script>
