<template>
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100">
      <spinner size="large" message="Loading storage items..."></spinner>
    </div>
  </div>
  <div class="flex flex-column" v-else>
    <!-- control bar -->
    <div class="pa3 ph4-l relative bg-white bb b--black-05">
      <div class="flex flex-row">
        <div class="flex-fill flex flex-row items-center">
          <div class="f2">Storage</div>
        </div>
        <div class="flex-none flex flex-row items-center" v-if="false">
          <el-button type="primary" class="ttu b" @click="openAddModal">New storage</el-button>
        </div>
      </div>
    </div>

    <div class="flex flex-row h-100" v-if="connections.length > 0">
      <abstract-list
        ref="list"
        class="br b--black-05 overflow-y-auto"
        layout="list"
        item-component="AbstractConnectionChooserItem"
        :auto-select-item="true"
        :items="connections"
        :item-options="{
          'item-cls': 'min-w5 pa3 pr2 darken-05',
          'item-style': 'margin: 0.125rem',
          'show-dropdown': false,
          'show-identifier': true,
          'show-url': false
        }"
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
  </div>
</template>

<script>
  import { CONNECTION_TYPE_HOME } from '../constants/connection-type'
  import { OBJECT_STATUS_AVAILABLE, OBJECT_STATUS_PENDING } from '../constants/object-status'
  import { mapState, mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import AbstractList from './AbstractList.vue'
  import FileChooser from './FileChooser.vue'
  import EmptyItem from './EmptyItem.vue'
  import ConnectionInfoMixin from './mixins/connection-info'

  const LOCAL_STORAGE_ITEM = {
    connection_type: CONNECTION_TYPE_HOME,
    eid: 'home',
    name: 'Local Storage'
  }

  export default {
    mixins: [ConnectionInfoMixin],
    components: {
      Spinner,
      AbstractList,
      FileChooser,
      EmptyItem
    },
    data() {
      return {
        connection: LOCAL_STORAGE_ITEM,
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
        var items = _.filter(this.getAvailableConnections(), this.isStorageConnection)
        return [LOCAL_STORAGE_ITEM].concat(items)
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
        'getAvailableConnections'
      ]),
      openAddModal() {
        this.show_connection_props_modal = true
        this.$nextTick(() => { this.$refs['modal-connection-props'].open() })
      },
      tryFetchConnections() {
        if (!this.is_fetched && !this.is_fetching)
          this.$store.dispatch('fetchConnections')
      },
      tryUpdateConnection(attrs, modal) {
        var eid = attrs.eid
        var ctype = _.get(attrs, 'connection_type', '')
        var is_pending = _.get(attrs, 'eid_status', '') === OBJECT_STATUS_PENDING

        attrs = _.pick(attrs, ['name', 'alias', 'description', 'connection_info'])
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
              var analytics_payload = _.pick(attrs, ['eid', 'name', 'alias', 'description', 'connection_type'])
              this.$store.track('Created Connection', analytics_payload)
            }
          }
           else
          {
            // TODO: add error handling
          }
        })
      },
      onConnectionActivate(item) {
        this.connection = _.assign({}, item)
      }
    }
  }
</script>
