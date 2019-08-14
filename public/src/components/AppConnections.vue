<template>
  <!-- fetching -->
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center bg-nearer-white h-100">
      <Spinner size="large" message="Loading connections..." />
    </div>
  </div>

  <!-- fetched -->
  <div class="flex flex-column bg-nearer-white" v-else-if="is_fetched">
    <div class="flex-fill flex flex-row" v-if="connections.length > 0">
      <template v-if="has_connection">
        <!-- sidebar -->
        <div class="flex flex-column min-w5 bg-white br b--black-05">
          <!-- control bar -->
          <div class="flex-none ph3 pv2 relative bb b--black-05">
            <div class="flex flex-row">
              <div class="flex-fill flex flex-row items-center">
                <h2 class="mv0 f3 fw6 mr3">Connections</h2>
              </div>
              <div class="flex-none flex flex-row items-center ml3">
                <el-button
                  size="small"
                  type="primary"
                  class="ttu fw6"
                  @click="onNewConnection"
                  v-require-rights:connection.update
                >
                  New
                </el-button>
              </div>
            </div>
          </div>

          <!-- list -->
          <div style="max-width: 20rem" v-bar>
            <AbstractList
              ref="list"
              layout="list"
              item-component="AbstractConnectionChooserItem"
              :selected-item.sync="connection"
              :items="connections"
              :item-options="{
                itemCls: 'min-w5 pa3 bb b--black-05 bg-white hover-bg-nearer-white',
                selectedCls: 'relative bg-nearer-white',
                showDropdown: true,
                dropdownItems: ['delete']
              }"
              @item-activate="selectConnection"
              @item-delete="tryDeleteConnection"
            />
          </div>
        </div>

        <!-- content area -->
        <div
          class="flex-fill pa4 pt0 overflow-y-scroll"
          :id="scrollbar_container_id"
        >
          <div class="h2"></div>
          <div class="relative z-7 bg-nearer-white sticky">
            <ConnectionStaticPanel
              class="w-100 center mw-doc"
              :connection="connection"
              @edit-click="onEditConnection"
            />
          </div>

          <div class="w-100 center mw-doc mt1 pa4 bg-white br2 css-white-box" style="min-height: 20rem; margin-bottom: 10rem">
            <template v-if="is_keyring_connection">
              <div class="mb2 lh-copy ttu fw6 f6">Keypair Values</div>
              <JsonDetailsPanel
                :json="connection.connection_info"
              />
            </template>
            <FileChooser
              :connection="connection"
              v-if="is_storage_connection"
            />
          </div>
        </div>
      </template>

      <!-- connection not found -->
      <PageNotFound class="flex-fill" v-else />
    </div>
    <EmptyItem class="flex flex-column items-center justify-center h-100" v-else>
      <div class="tc f3" slot="text">
        <p>No connections to show</p>
        <el-button
          size="large"
          type="primary"
          class="ttu fw6"
          @click="onNewConnection"
        >
          New Connection
        </el-button>
      </div>
    </EmptyItem>

    <!-- connection dialog -->
    <el-dialog
      custom-class="el-dialog--no-header el-dialog--no-footer"
      width="46rem"
      top="4vh"
      :modal-append-to-body="false"
      :close-on-click-modal="false"
      :visible.sync="show_connection_dialog"
    >
      <ConnectionEditPanel
        :mode="edit_mode"
        :show-steps="edit_mode == 'edit' ? false : true"
        :connection="edit_mode == 'edit' ? connection : undefined"
        @close="cancelChanges"
        @cancel="cancelChanges"
        @submit="tryUpdateConnection"
        v-if="show_connection_dialog"
      />
    </el-dialog>
  </div>
</template>

<script>
  import { CONNECTION_TYPE_HTTP, CONNECTION_TYPE_KEYRING } from '@/constants/connection-type'
  import { OBJECT_STATUS_AVAILABLE, OBJECT_STATUS_PENDING } from '@/constants/object-status'
  import { mapState, mapGetters } from 'vuex'
  import stickybits from 'stickybits'
  import Spinner from 'vue-simple-spinner'
  import AbstractList from '@/components/AbstractList'
  import ConnectionEditPanel from '@/components/ConnectionEditPanel'
  import ConnectionStaticPanel from '@/components/ConnectionStaticPanel'
  import FileChooser from '@/components/FileChooser'
  import JsonDetailsPanel from '@/components/JsonDetailsPanel'
  import EmptyItem from '@/components/EmptyItem'
  import PageNotFound from '@/components/PageNotFound'
  import MixinConnection from '@/components/mixins/connection'

  export default {
    metaInfo() {
      return {
        title: _.get(this.connection, 'name', 'Connections'),
        titleTemplate: (chunk) => {
          return chunk ? `${chunk} | ${this.getActiveTeamLabel()}` : 'Flex.io'
        }
      }
    },
    mixins: [MixinConnection],
    components: {
      Spinner,
      AbstractList,
      ConnectionEditPanel,
      ConnectionStaticPanel,
      FileChooser,
      JsonDetailsPanel,
      EmptyItem,
      PageNotFound
    },
    watch: {
      route_object_name: {
        handler: 'loadConnection',
        immediate: true
      },
      connections(val, old_val) {
        if (!this.has_connection) {
          this.loadConnection(this.route_object_name)
        }
      }
    },
    data() {
      return {
        is_selecting: false,
        show_connection_dialog: false,
        connection: {},
        last_selected: {},
        edit_mode: 'add',
        scrollbar_container_id: _.uniqueId('content-'),
      }
    },
    computed: {
      // mix this into the outer object with the object spread operator
      ...mapState({
        is_fetching: state => state.connections.is_fetching,
        is_fetched: state => state.connections.is_fetched,
        active_team_name: state => state.teams.active_team_name
      }),
      connections() {
        return this.getAvailableConnections()
      },
      route_object_name() {
        return _.get(this.$route, 'params.object_name', undefined)
      },
      ctype() {
        return _.get(this.connection, 'connection_type', '')
      },
      has_connection() {
        return this.ctype.length > 0
      },
      is_storage_connection() {
        return this.$_Connection_isStorage(this.connection)
      },
      is_keyring_connection() {
        return this.ctype == CONNECTION_TYPE_KEYRING
      }
    },
    created() {
      this.tryFetchConnections()
    },
    mounted() {
      this.$store.track('Visited Connections Page')
    },
    methods: {
      ...mapGetters('connections', {
        'getAvailableConnections': 'getAvailableConnections'
      }),
      ...mapGetters('teams', {
        'getActiveTeamLabel': 'getActiveTeamLabel'
      }),
      tryFetchConnections() {
        var team_name = this.active_team_name

        if (!this.is_fetched && !this.is_fetching) {
          this.$store.dispatch('connections/fetch', { team_name })
        }
      },
      tryUpdateConnection(attrs) {
        var eid = attrs.eid
        var ctype = _.get(attrs, 'connection_type', '')
        var is_pending = _.get(attrs, 'eid_status') == OBJECT_STATUS_PENDING
        var orig_name = _.get(this.connection, 'name')
        var team_name = this.active_team_name

        attrs = _.pick(attrs, ['name', 'title', 'description', 'connection_info'])
        _.assign(attrs, { eid_status: OBJECT_STATUS_AVAILABLE })

        // TODO: backend should probably handle this for us
        // keyring connections don't need to connect, so they are always available
        if (ctype == CONNECTION_TYPE_KEYRING) {
          _.assign(attrs, { connection_status: OBJECT_STATUS_AVAILABLE })
        }

        // update the connection and make it available
        this.$store.dispatch('connections/update', { team_name, eid, attrs }).then(response => {
          var connection = response.data

          this.$message({
            message: is_pending ? 'The connection was created successfully.' : 'The connection was updated successfully.',
            type: 'success'
          })

          if (ctype != CONNECTION_TYPE_KEYRING) {
            // try to connect to the connection
            this.$store.dispatch('connections/test', { team_name, eid, attrs })
          }

          if (is_pending) {
            var analytics_payload = _.pick(attrs, ['eid', 'name', 'title', 'description', 'connection_type'])
            this.$store.track('Created Connection', analytics_payload)
          }

          // change the object name in the route
          if (connection.name != orig_name) {
            var object_name = connection.name

            var new_route = _.pick(this.$route, ['name', 'meta', 'params', 'path'])
            new_route.params = _.assign({}, new_route.params, { object_name })
            this.$router.replace(new_route)
          }

          this.selectConnection(connection)
          this.show_connection_dialog = false
        }).catch(error => {
          this.$message({
            message: is_pending ? 'There was a problem creating the connection.' : 'There was a problem updating the connection.',
            type: 'error'
          })

          this.$store.track('Created Connection (Error)')
        })
      },
      tryDeleteConnection(attrs) {
        var eid = _.get(attrs, 'eid', '')
        var cname = _.get(attrs, 'name', 'Connection')
        var team_name = this.active_team_name

        this.$confirm('Are you sure you want to delete the connection named "' + cname + '"?', 'Really delete connection?', {
          confirmButtonClass: 'ttu fw6',
          cancelButtonClass: 'ttu fw6',
          confirmButtonText: 'Delete connection',
          cancelButtonText: 'Cancel',
          type: 'warning'
        }).then(() => {
          var selected_idx = _.findIndex(this.connections, { eid: this.connection.eid })
          var deleting_idx = _.findIndex(this.connections, { eid: attrs.eid })

          this.$store.dispatch('connections/delete', { team_name, eid }).then(response => {
            if (deleting_idx >= 0 && deleting_idx == selected_idx) {
              if (deleting_idx >= this.connections.length) {
                deleting_idx--
              }

              var connection = _.get(this.connections, '['+deleting_idx+']', {})
              this.selectConnection(connection)
            }
          })
        })
      },
      loadConnection(identifier) {
        var conn

        if (identifier) {
          conn = _.find(this.connections, c => c.eid == identifier || c.name == identifier)
        } else {
          conn = _.first(this.connections)
        }

        this.selectConnection(conn)
        this.initSticky()
      },
      selectConnection(item) {
        if (this.connections.length == 0) {
          return
        }

        // we couldn't find the item; show a 404
        if (_.isNil(item)) {
          this.connection = {}
          this.last_selected = {}
          return
        }

        this.connection = _.cloneDeep(item)
        this.last_selected = _.cloneDeep(item)

        if (!this.is_selecting) {
          // update the route
          var name = _.get(item, 'name', '')
          var object_name = name.length > 0 ? name : _.get(item, 'eid', '')
          var team_name = this.active_team_name

          var new_route = _.pick(this.$route, ['name', 'meta', 'params', 'path'])
          new_route.params = _.assign({}, new_route.params, { team_name, object_name })
          this.$router[!this.route_object_name?'replace':'push'](new_route)

          this.is_selecting = true
          this.$nextTick(() => { this.is_selecting = false })
        }
      },
      onNewConnection() {
        this.edit_mode = 'add'
        this.show_connection_dialog = true
      },
      onEditConnection() {
        this.edit_mode = 'edit'
        this.show_connection_dialog = true
      },
      cancelChanges(item) {
        this.show_connection_dialog = false
        this.connection = _.cloneDeep(this.last_selected)
      },
      saveChanges(item) {
        this.tryUpdateConnection(item)
      },
      initSticky() {
        setTimeout(() => {
          stickybits('.sticky', {
            scrollEl: '#' + this.scrollbar_container_id,
            useStickyClasses: true,
            stickyBitStickyOffset: 0
          })
        }, 500)
      }
    }
  }
</script>


<style lang="stylus" scoped>
  .sticky
    margin: 0 -2rem
    padding: 1rem 2rem /* match container horizontal padding */
    border-bottom: 1px solid transparent
    transition: all 0.15s ease

  .sticky.js-is-sticky
  .sticky.js-is-stuck
    border-bottom: 1px solid rgba(0,0,0,0.1)
    box-shadow: 0 4px 16px -6px rgba(0,0,0,0.2)
</style>
