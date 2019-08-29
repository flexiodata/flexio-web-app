<template>
  <!-- fetching -->
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center bg-nearer-white h-100">
      <Spinner size="large" message="Loading functions..." />
    </div>
  </div>

  <!-- fetched -->
  <div class="flex flex-column bg-nearer-white" v-else-if="is_fetched">
    <div class="flex-fill flex flex-row" v-if="pipes.length > 0">
      <template v-if="has_pipe">
        <!-- sidebar -->
        <div class="flex flex-column min-w5 bg-white br b--black-05">
          <!-- control bar -->
          <div class="flex-none pa2 relative bg-white bb b--black-05">
            <div class="flex flex-row">
              <div class="flex-fill flex flex-row items-center">
                <h2 class="mv0 f3 fw6 mr3 lh-1">Functions</h2>
              </div>
              <div class="flex-none flex flex-row items-center ml3">
                <el-dropdown trigger="click" @command="onNewCommand">
                  <el-button
                    size="small"
                    type="primary"
                    class="ttu fw6"

                  >
                    New<i class="el-icon-arrow-down el-icon--right fw6"></i>
                  </el-button>
                  <el-dropdown-menu slot="dropdown">
                    <el-dropdown-item
                      command="local-function"
                      v-require-rights:pipe.create
                    >
                      Local Function
                    </el-dropdown-item>
                    <el-dropdown-item
                      command="function-mount"
                      v-require-rights:connection.create
                    >
                      Function Mount
                    </el-dropdown-item>
                  </el-dropdown-menu>
                </el-dropdown>
              </div>
            </div>
          </div>

          <!-- list -->
          <div style="max-width: 20rem" v-bar>
            <div>
              <div
                :key="group.id"
                v-for="group in grouped_pipes"
              >
                <div class="pa2 mt2 flex flex-row items-center">
                  <div class="flex-fill">{{group.title}}</div>
                  <el-dropdown
                    trigger="click"
                    @command="onFunctionMountCommand"
                    v-show="group.id != 'local'"
                  >
                    <span class="el-dropdown-link pointer mr1">
                      <i class="material-icons md-18">expand_more</i>
                    </span>
                    <el-dropdown-menu slot="dropdown" class="f6">
                      <el-dropdown-item
                        class="flex flex-row items-center item-dropdown-menu-item"
                        :connection-eid="group.id"
                        command="edit"
                      >
                        <i class="material-icons md-18 mr2">edit</i> Edit
                      </el-dropdown-item>
                      <el-dropdown-item
                        class="flex flex-row items-center item-dropdown-menu-item"
                        :connection-eid="group.id"
                        command="refresh"
                      >
                        <i class="material-icons md-18 mr2">refresh</i> Refresh
                      </el-dropdown-item>
                      <el-dropdown-item divided></el-dropdown-item>
                      <el-dropdown-item
                        class="flex flex-row items-center item-dropdown-menu-item"
                        :connection-eid="group.id"
                        command="delete"
                      >
                        <i class="material-icons md-18 mr2">delete</i> Delete
                      </el-dropdown-item>
                    </el-dropdown-menu>
                  </el-dropdown>
                </div>
                <div
                  class="pv1 silver i"
                  style="padding-left: 17px; padding-right: 0px; margin: 0 12px 0 3px; font-size: 12px"
                  v-if="group.pipes.length == 0"
                >
                  There are no functions to show
                </div>
                <PipeList
                  class="mb2"
                  item-style="padding-left: 17px; padding-right: 0px; margin: 0 12px 0 3px"
                  item-size="mini"
                  :items="group.pipes"
                  :selected-item.sync="pipe"
                  :show-delete="true"
                  @item-activate="selectPipe"
                  @item-delete="tryDeletePipe"
                  v-else
                />
              </div>
              <div class="h2"></div>
            </div>
          </div>
        </div>

        <!-- content area -->
        <PipeDocument class="flex-fill" />
      </template>

      <!-- pipe not found -->
      <PageNotFound class="flex-fill bg-nearer-white" v-else />
    </div>
    <EmptyItem class="flex flex-column items-center justify-center h-100" v-else>
      <div class="tc f3" slot="text">
        <p>No functions to show</p>
        <el-button
          size="large"
          type="primary"
          class="ttu fw6"
          @click="show_pipe_dialog = true"
        >
          New Function
        </el-button>
      </div>
    </EmptyItem>

    <!-- pipe edit dialog -->
    <el-dialog
      custom-class="el-dialog--no-header el-dialog--no-footer"
      width="46rem"
      top="4vh"
      :modal-append-to-body="false"
      :close-on-click-modal="false"
      :visible.sync="show_pipe_dialog"
    >
      <PipeEditPanel
        mode="add"
        @close="show_pipe_dialog = false"
        @cancel="show_pipe_dialog = false"
        @submit="tryCreatePipe"
        v-if="show_pipe_dialog"
      />
    </el-dialog>

    <!-- connection edit dialog -->
    <el-dialog
      custom-class="el-dialog--no-header el-dialog--no-footer"
      width="46rem"
      top="4vh"
      :modal-append-to-body="false"
      :close-on-click-modal="false"
      :visible.sync="show_connection_dialog"
    >
      <ConnectionEditPanel
        :title="connection_edit_mode ? 'New Function Mount' : 'Edit Function Mount'"
        :mode="connection_edit_mode"
        :show-steps="connection_edit_mode == 'edit' ? false : true"
        :connection="connection_edit_mode == 'edit' ? edit_connection : new_function_mount_attrs"
        :filter-by="filterByFunctionMount"
        @close="show_connection_dialog = false"
        @cancel="show_connection_dialog = false"
        @update-connection="syncFunctionMount"
        v-if="show_connection_dialog"
      />
    </el-dialog>
  </div>
</template>

<script>
  import { ROUTE_APP_PIPES } from '@/constants/route'
  import { OBJECT_STATUS_AVAILABLE } from '@/constants/object-status'
  import { mapState, mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import PipeList from '@/components/PipeList'
  import PipeDocument from '@/components/PipeDocument'
  import PipeEditPanel from '@/components/PipeEditPanel'
  import ConnectionEditPanel from '@/components/ConnectionEditPanel'
  import EmptyItem from '@/components/EmptyItem'
  import PageNotFound from '@/components/PageNotFound'
  import MixinConnection from '@/components/mixins/connection'

  const CONNECTION_MODE_RESOURCE = 'R'
  const CONNECTION_MODE_FUNCTION = 'F'

  const defaultAttrs = () => {
    // when creating a new function, start out with a basic Python 'Hello World' script
    return {
      deploy_mode: 'R',
      deploy_api: 'A',
      deploy_ui: 'A',
      task: {
        op: 'sequence',
        items: []
      }
    }
  }

  export default {
    metaInfo() {
      return {
        title: _.get(this.pipe, 'name', 'Functions'),
        titleTemplate: (chunk) => {
          return chunk ? `${chunk} | ${this.getActiveTeamLabel()}` : 'Flex.io'
        }
      }
    },
    mixins: [MixinConnection],
    components: {
      Spinner,
      PipeList,
      PipeDocument,
      PipeEditPanel,
      ConnectionEditPanel,
      EmptyItem,
      PageNotFound
    },
    watch: {
      route_object_name: {
        handler: 'loadPipe',
        immediate: true
      },
      pipes(val, old_val) {
        if (!this.has_pipe) {
          this.loadPipe(this.route_object_name)
        }
      }
    },
    data() {
      return {
        is_selecting: false,
        show_pipe_dialog: false,
        pipe: {},
        last_selected: {},
        show_connection_dialog: false,
        connection_edit_mode: 'add',
        edit_connection: null,
        new_function_mount_attrs: {
          connection_mode: CONNECTION_MODE_FUNCTION
        },
      }
    },
    computed: {
      // mix this into the outer object with the object spread operator
      ...mapState({
        is_fetching: state => state.pipes.is_fetching,
        is_fetched: state => state.pipes.is_fetched,
        active_team_name: state => state.teams.active_team_name
      }),
      pipes() {
        return _.sortBy(this.getAllPipes(), ['name'])
      },
      function_mounts() {
        return this.getAvailableFunctionMounts()
      },
      grouped_pipes() {
        // start with an object with connection eids for keys and empty arrays for values
        var all_mounts = _.keyBy(this.function_mounts, 'eid')
        all_mounts = _.mapValues(all_mounts, m => [])

        // group all pipes by their parent connection (this will also result in
        // an object with connection eids as the key values)
        var mounts_with_pipes = _.groupBy(this.pipes, p => _.get(p, 'parent.eid', 'local'))

        // overwrite keys in 'all_mounts' with keys in 'mounts_with_pipes'
        var groups = _.assign({}, all_mounts, mounts_with_pipes)

        // return the following object for each function mount
        groups = _.map(groups, (val, key) => {
          var connection = _.find(this.function_mounts, { eid: key })

          return {
            id: key.length == 0 ? 'local' : key,
            title: key.length == 0 ? 'Local' : _.get(connection, 'name', `Not found (${key})`),
            pipes: _.sortBy(val, ['name'])
          }
        })

        // sort the groups (local functions first, then alphabetically)
        return _.sortBy(groups, [
          group => group.id != 'local',
          group => group.title.indexOf('Not found') != -1,
          group => group.title.toLowerCase()
        ])
      },
      route_object_name() {
        return _.get(this.$route, 'params.object_name', undefined)
      },
      pname() {
        return _.get(this.pipe, 'name', '')
      },
      has_pipe() {
        return this.pname.length > 0
      }
    },
    created() {
      this.tryFetchPipes()
    },
    mounted() {
      this.$store.track('Visited Functions Page')
    },
    methods: {
      ...mapGetters('teams', {
        'getActiveTeamLabel': 'getActiveTeamLabel'
      }),
      ...mapGetters('pipes', {
        'getAllPipes': 'getAllPipes'
      }),
      ...mapGetters('connections', {
        'getAvailableFunctionMounts': 'getAvailableFunctionMounts'
      }),
      tryFetchPipes() {
        var team_name = this.active_team_name

        if (!this.is_fetched && !this.is_fetching) {
          this.$store.dispatch('pipes/fetch', { team_name })
        }
      },
      tryCreatePipe(attrs) {
        var team_name = this.active_team_name

        var attrs = _.cloneDeep(attrs)
        attrs = _.assign({}, defaultAttrs(), attrs)

        this.$store.dispatch('pipes/create', { team_name, attrs }).then(response => {
          var pipe = response.data

          this.$message({
            message: 'The function was created successfully.',
            type: 'success'
          })

          var analytics_payload = _.pick(pipe, ['eid', 'name', 'title', 'description', 'created'])
          this.$store.track('Created Function', analytics_payload)
          this.selectPipe(pipe)
          this.show_pipe_dialog = false
        })
      },
      tryDeletePipe(attrs) {
        var eid = _.get(attrs, 'eid', '')
        var pname = _.get(attrs, 'name', 'Function')
        var team_name = this.active_team_name

        var msg = `Are you sure you want to delete the function named <strong>"${pname}"</strong>?`
        var title = `Really delete function?`

        this.$confirm(msg, title, {
          type: 'warning',
          confirmButtonClass: 'ttu fw6',
          cancelButtonClass: 'ttu fw6',
          confirmButtonText: 'Delete function',
          cancelButtonText: 'Cancel',
          dangerouslyUseHTMLString: true
        }).then(() => {
          var selected_idx = _.findIndex(this.pipes, { eid: this.pipe.eid })
          var deleting_idx = _.findIndex(this.pipes, { eid: attrs.eid })

          this.$store.dispatch('pipes/delete', { team_name, eid }).then(response => {
            if (deleting_idx >= 0 && deleting_idx == selected_idx) {
              if (deleting_idx >= this.pipes.length) {
                deleting_idx--
              }

              var pipe = _.get(this.pipes, '['+deleting_idx+']', {})
              this.selectPipe(pipe)
            }
          })
        }).catch(error => {
          // do nothing
        })
      },
      tryDeleteFunctionMount(attrs) {
        var eid = _.get(attrs, 'eid', '')
        var cname = _.get(attrs, 'name', 'Connection')
        var team_name = this.active_team_name

        var msg = `Are you sure you want to delete the function mount named <strong>"${cname}"</strong>?`
        var title = `Really delete function mount?`

        this.$confirm(msg, title, {
          type: 'warning',
          confirmButtonClass: 'ttu fw6',
          cancelButtonClass: 'ttu fw6',
          confirmButtonText: 'Delete function mount',
          cancelButtonText: 'Cancel',
          dangerouslyUseHTMLString: true
        }).then(() => {
          this.$store.dispatch('connections/delete', { team_name, eid })
        })
      },
      loadPipe(identifier) {
        var pipe

        if (identifier) {
          pipe = _.find(this.pipes, p => p.eid == identifier || p.name == identifier)
        } else {
          pipe = _.first(this.pipes)
        }

        this.selectPipe(pipe)
      },
      selectPipe(item) {
        if (this.pipes.length == 0) {
          return
        }

        // we couldn't find the item; show a 404
        if (_.isNil(item)) {
          this.pipe = {}
          this.last_selected = {}
          return
        }
        this.pipe = _.cloneDeep(item)
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
      syncFunctionMount(connection) {
        var team_name = this.active_team_name
        var eid = _.get(connection, 'eid', '')

        this.$store.dispatch('connections/sync', { team_name, eid })
        this.show_connection_dialog = false
      },
      filterByFunctionMount(connection) {
        return this.$_Connection_isFunctionMount(connection)
      },
      onNewLocalFunction() {
        this.show_pipe_dialog = true
      },
      onNewFunctionMount() {
        this.connection_edit_mode = 'add'
        this.show_connection_dialog = true
      },
      onEditFunctionMount(connection) {
        this.edit_connection = connection
        this.connection_edit_mode = 'edit'
        this.show_connection_dialog = true
      },
      onNewCommand(cmd) {
        switch (cmd) {
          case 'local-function': this.onNewLocalFunction(); return
          case 'function-mount': this.onNewFunctionMount(); return
        }
      },
      onFunctionMountCommand(cmd, dropdown_component) {
        var eid = _.get(dropdown_component.$attrs, 'connection-eid')
        var connection = _.find(this.function_mounts, { eid })

        switch (cmd) {
          case 'edit': this.onEditFunctionMount(connection); return
          case 'refresh': this.syncFunctionMount(connection); return
          case 'delete': this.tryDeleteFunctionMount(connection); return
        }
      }
    }
  }
</script>
