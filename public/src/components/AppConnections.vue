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
          <div class="flex-none pa2 relative bg-white">
            <div class="flex flex-row items-center">
              <el-input
                class="w-100 mr2"
                size="small"
                placeholder="Search..."
                clearable
                prefix-icon="el-icon-search"
                @keydown.esc.native="connection_list_filter = ''"
                v-model="connection_list_filter"
              />
              <el-button
                size="small"
                type="primary"
                class="ttu fw6"
                @click="onNewConnection"
                v-require-rights:connection.create
              >
                New
              </el-button>
            </div>
          </div>

          <!-- list -->
          <div style="max-width: 20rem" v-bar>
            <div>
              <div
                class="pt1 pb3 ph3 lh-title silver i"
                style="font-size: 13px"
                v-if="filtered_connections.length == 0"
              >
                <span v-if="connection_list_filter.length > 0">There are no connections that match the search criteria</span>
                <span v-else>There are no connections to show</span>
              </div>
              <ConnectionList
                class="bt b--black-05"
                :items="filtered_connections"
                :selected-item.sync="connection"
                :show-delete="true"
                @item-activate="selectConnection"
                @item-delete="tryDeleteConnection"
                v-else
              />
            </div>
          </div>
        </div>

        <!-- content area -->
        <div
          class="flex-fill ph4 pv5 overflow-y-scroll"
          :id="scrollbar_container_id"
        >
          <ConnectionDocument
            class="w-100 center mw-doc pa4 bg-white br2 css-white-box"
            style="min-height: 20rem; margin-bottom: 15rem"
            :connection-eid="connection.eid"
            @edit-click="onEditConnection"
          />
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
      width="48rem"
      top="4vh"
      :modal-append-to-body="false"
      :close-on-click-modal="false"
      :visible.sync="show_connection_dialog"
    >
      <ConnectionEditPanel
        :mode="edit_mode"
        :active-step="edit_active_step"
        :connection="edit_mode == 'edit' ? connection : undefined"
        @close="cancelChanges"
        @cancel="cancelChanges"
        @submit="onUpdateConnection"
        v-if="show_connection_dialog"
      />
    </el-dialog>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import stickybits from 'stickybits'
  import Spinner from 'vue-simple-spinner'
  import ConnectionList from '@/components/ConnectionList'
  import ConnectionEditPanel from '@/components/ConnectionEditPanel'
  import ConnectionDocument from '@/components/ConnectionDocument'
  import EmptyItem from '@/components/EmptyItem'
  import PageNotFound from '@/components/PageNotFound'
  import MixinFilter from '@/components/mixins/filter'

  export default {
    metaInfo() {
      return {
        title: _.get(this.connection, 'name', 'Connections'),
        titleTemplate: (chunk) => {
          return chunk ? `${chunk} | ${this.getActiveTeamLabel()}` : 'Flex.io'
        }
      }
    },
    mixins: [MixinFilter],
    components: {
      Spinner,
      ConnectionList,
      ConnectionEditPanel,
      ConnectionDocument,
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
        connection_list_filter: '',
        connection: {},
        last_selected: {},
        edit_mode: 'add',
        edit_active_step: 'choose-source',
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
      sorted_connections() {
        return _.sortBy(this.connections, ['name'])
      },
      filtered_connections() {
        return this.$_Filter_filter(this.sorted_connections, this.connection_list_filter, ['name'])
      },
      route_object_name() {
        return _.get(this.$route, 'params.object_name', undefined)
      },
      has_connection() {
        return _.get(this.connection, 'eid', '').length > 0
      },
    },
    created() {
      this.tryFetchConnections()
    },
    mounted() {
      this.$store.track('Visited Connections Page')
    },
    methods: {
      ...mapGetters('teams', {
        'getActiveTeamLabel': 'getActiveTeamLabel'
      }),
      ...mapGetters('connections', {
        'getAvailableConnections': 'getAvailableConnections'
      }),
      tryFetchConnections() {
        var team_name = this.active_team_name

        if (!this.is_fetched && !this.is_fetching) {
          this.$store.dispatch('connections/fetch', { team_name })
        }
      },
      onUpdateConnection(connection) {
        var object_name = connection.name
        var current_object_name = _.get(this.$route, 'params.object_name')

        if (object_name != current_object_name) {
          var new_route = _.pick(this.$route, ['name', 'meta', 'params', 'path'])
          new_route.params = _.assign({}, new_route.params, { object_name })
          this.$router.replace(new_route)
        } else {
          // force re-render the content components
          this.selectConnection(connection)
        }

        this.show_connection_dialog = false
      },
      tryDeleteConnection(attrs) {
        var eid = _.get(attrs, 'eid', '')
        var cname = _.get(attrs, 'name', 'Connection')
        var team_name = this.active_team_name

        var msg = `Are you sure you want to delete the connection named <strong>"${cname}"</strong>?`
        var title = `Really delete connection?`

        this.$confirm(msg, title, {
          type: 'warning',
          confirmButtonClass: 'ttu fw6',
          cancelButtonClass: 'ttu fw6',
          confirmButtonText: 'Delete connection',
          cancelButtonText: 'Cancel',
          dangerouslyUseHTMLString: true,
        }).then(() => {
          var selected_idx = _.findIndex(this.sorted_connections, { eid: this.connection.eid })
          var deleting_idx = _.findIndex(this.sorted_connections, { eid: attrs.eid })

          this.$store.dispatch('connections/delete', { team_name, eid }).then(response => {
            if (deleting_idx >= 0 && deleting_idx == selected_idx) {
              if (deleting_idx >= this.sorted_connections.length) {
                deleting_idx--
              }

              var connection = _.get(this.sorted_connections, '['+deleting_idx+']', {})
              this.selectConnection(connection)
            }
          })
        })
      },
      loadConnection(identifier) {
        var conn

        if (identifier) {
          conn = _.find(this.sorted_connections, c => c.eid == identifier || c.name == identifier)
        } else {
          conn = _.first(this.sorted_connections)
        }

        this.selectConnection(conn)
        this.initSticky()
      },
      selectConnection(item) {
        if (this.sorted_connections.length == 0) {
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
        this.edit_active_step = 'choose-source'
        this.show_connection_dialog = true
      },
      onEditConnection() {
        this.edit_mode = 'edit'
        this.edit_active_step = 'authentication'
        this.show_connection_dialog = true
      },
      cancelChanges(item) {
        this.show_connection_dialog = false
        this.connection = _.cloneDeep(this.last_selected)
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
