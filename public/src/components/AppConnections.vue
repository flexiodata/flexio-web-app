<template>
  <!-- fetching -->
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100">
      <Spinner size="large" message="Loading connections..." />
    </div>
  </div>

  <!-- fetched -->
  <div class="flex flex-column" v-else-if="is_fetched">
    <!-- control bar -->
    <div class="flex-none pa3 relative bg-white bb b--black-05">
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
          'item-cls': 'min-w5 pa3 pr2 ba b--white bg-white hover-bg-nearer-white',
          'item-style': 'margin: 3px',
          'selected-cls': 'relative b--black-10 bg-nearer-white',
          'show-dropdown': true,
          'dropdown-items': ['delete']
        }"
        @item-activate="selectConnection"
        @item-delete="tryDeleteConnection"
        v-if="connections.length > 0"
      />
      <div class="flex-fill" v-if="connection">
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
      custom-class="el-dialog--no-header el-dialog--no-footer"
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
      this.$store.track('Visited Connections Page')
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
            this.$message({
              message: is_pending ? 'The connection was created successfully.' : 'The connection was updated successfully.',
              type: 'success'
            })

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
            this.$message({
              message: is_pending ? 'There was a problem creating the connection.' : 'There was a problem updating the connection.',
              type: 'error'
            })

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
