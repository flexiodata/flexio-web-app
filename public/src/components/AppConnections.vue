<template>
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100">
      <spinner size="large" message="Loading connections..."></spinner>
    </div>
  </div>
  <div class="flex flex-column" v-else>
    <!-- control bar -->
    <div class="flex-none pa3 ph4-l relative bg-white bb b--black-05">
      <div class="flex flex-row">
        <div class="flex-fill flex flex-row items-center">
          <div class="f2">Connections</div>
        </div>
        <div class="flex-none flex flex-row items-center ml3">
          <el-button type="primary" class="ttu b" @click="show_connection_new_dialog = true">New Connection</el-button>
        </div>
      </div>
    </div>

    <div class="flex-fill flex flex-row" v-if="connections.length > 0">
      <AbstractList
        ref="list"
        class="br b--black-05 overflow-y-auto"
        layout="list"
        item-component="AbstractConnectionChooserItem"
        :selected-item.sync="connection"
        :items="connections"
        :item-options="{
          'item-cls': 'min-w5 pa3 pr2 darken-05',
          'item-style': 'margin: 0.125rem',
          'show-dropdown': true,
          'dropdown-items': ['delete']
        }"
        @item-activate="selectConnection"
        @item-delete="tryDeleteConnection"
        v-if="connections.length > 0"
      />
      <div class="flex-fill overflow-y-auto" v-if="connection">
        <ui-alert @dismiss="show_success = false" type="success" :dismissible="false" v-show="show_success">
          The connection was updated successfully.
        </ui-alert>
        <ui-alert @dismiss="show_error = false" type="error" v-show="show_error">
          There was a problem updating the connection.
        </ui-alert>

        <ConnectionEditPanel
          class="pa3 pa4-l"
          style="max-width: 60rem"
          mode="edit"
          :show-title="false"
          :show-steps="false"
          :connection="connection"
          @cancel="cancelChanges"
          @submit="tryUpdateConnection"
        />
      </div>
    </div>
    <EmptyItem class="flex flex-column justify-center h-100" v-else>
      <i slot="icon" class="material-icons">repeat</i>
      <span slot="text">No connections to show</span>
    </EmptyItem>

    <!-- connection create dialog -->
    <el-dialog
      custom-class="no-header no-footer"
      width="51rem"
      top="8vh"
      :modal-append-to-body="false"
      :visible.sync="show_connection_new_dialog"
    >
      <ConnectionEditPanel
        @close="show_connection_new_dialog = false"
        @cancel="show_connection_new_dialog = false"
        @submit="tryUpdateConnection"
        v-if="show_connection_new_dialog"
      />
    </el-dialog>
  </div>
</template>

<script>
  import { CONNECTION_TYPE_HTTP } from '../constants/connection-type'
  import { OBJECT_STATUS_AVAILABLE, OBJECT_STATUS_PENDING } from '../constants/object-status'
  import { mapState, mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import AbstractList from './AbstractList.vue'
  import ConnectionEditPanel from './ConnectionEditPanel.vue'
  import EmptyItem from './EmptyItem.vue'

  export default {
    components: {
      Spinner,
      AbstractList,
      ConnectionEditPanel,
      EmptyItem
    },
    watch: {
      connections(val, old_val) {
        if (!this.has_connection) {
          this.selectConnection(_.first(this.connections))
        }
      }
    },
    data() {
      return {
        connection: {},
        last_selected: {},
        show_success: false,
        show_error: false,
        show_connection_new_dialog: false
      }
    },
    computed: {
      // mix this into the outer object with the object spread operator
      ...mapState({
        'is_fetching': 'connections_fetching',
        'is_fetched': 'connections_fetched'
      }),
      connections() {
        return this.getAvailableConnections()
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
    mounted() {
      this.selectConnection(_.first(this.connections))
    },
    methods: {
      ...mapGetters([
        'getAvailableConnections'
      ]),
      tryFetchConnections() {
        if (!this.is_fetched && !this.is_fetching)
          this.$store.dispatch('fetchConnections')
      },
      tryUpdateConnection(attrs) {
        var eid = attrs.eid
        var ctype = _.get(attrs, 'connection_type', '')
        var is_pending = _.get(attrs, 'eid_status', '') === OBJECT_STATUS_PENDING

        attrs = _.pick(attrs, ['name', 'alias', 'description', 'connection_info'])
        _.assign(attrs, { eid_status: OBJECT_STATUS_AVAILABLE })

        // update the connection and make it available
        this.$store.dispatch('updateConnection', { eid, attrs }).then(response => {
          if (response.ok)
          {
            this.show_success = true
            setTimeout(() => { this.show_success = false }, 4000)

            // try to connect to the connection
            this.$store.dispatch('testConnection', { eid, attrs })

            if (is_pending)
            {
              var analytics_payload = _.pick(attrs, ['eid', 'name', 'alias', 'description', 'connection_type'])
              this.$store.track('Created Connection', analytics_payload)
            }

            this.show_connection_new_dialog = false

            this.selectConnection(_.get(response, 'body', {}))
          }
           else
          {
            this.show_error = true
            this.$store.track('Created Connection (Error)')
          }
        })
      },
      tryDeleteConnection(attrs) {
        var name = _.get(attrs, 'name', 'Connection')

        this.$confirm('Are you sure you want to delete the connection named "'+name+'"?', 'Really delete connection?', {
          confirmButtonText: 'DELETE CONNECTION',
          cancelButtonText: 'CANCEL',
          type: 'warning'
        }).then(() => {
          var idx = _.findIndex(this.connections, this.connection)

          this.$store.dispatch('deleteConnection', { attrs }).then(response => {
            if (response.ok)
            {
              if (idx >= 0)
              {
                if (idx >= this.connections.length)
                  idx--

                this.selectConnection(_.get(this.connections, '['+idx+']', {}))
              }
            }
          })
        }).catch(() => {
          // do nothing
        })
      },
      selectConnection(item) {
        this.connection = _.cloneDeep(item)
        this.last_selected = _.cloneDeep(item)
      },
      cancelChanges(item) {
        this.connection = _.cloneDeep(this.last_selected)
      },
      saveChanges(item) {
        this.tryUpdateConnection(item)
      }
    }
  }
</script>
