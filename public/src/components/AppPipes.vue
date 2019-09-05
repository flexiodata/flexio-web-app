<template>
  <!-- fetching -->
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center bg-nearer-white h-100">
      <Spinner size="large" message="Loading functions..." />
    </div>
  </div>

  <!-- fetched -->
  <div class="flex flex-column bg-nearer-white" v-else-if="is_fetched">
    <div class="flex-fill flex flex-row relative" v-if="pipes.length > 0">
      <template v-if="has_pipe">
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
                @keydown.esc.native="pipe_list_filter = ''"
                v-model="pipe_list_filter"
              />
              <el-dropdown trigger="click">
                <el-button
                  size="small"
                  type="primary"
                  class="ttu fw6"
                  v-require-rights:pipe.create
                >
                  New<i class="el-icon-arrow-down el-icon--right fw6" style="margin-right: -2px"></i>
                </el-button>
                  <el-dropdown-menu style="min-width: 10rem" slot="dropdown">
                  <el-dropdown-item @click.native="onNewPipe('extract')">Extract</el-dropdown-item>
                  <el-dropdown-item @click.native="onNewPipe('lookup')">Lookup</el-dropdown-item>
                  <el-dropdown-item @click.native="onNewPipe('execute')">Execute</el-dropdown-item>
                  <el-dropdown-item divided></el-dropdown-item>
                  <el-dropdown-item @click.native="onNewFunctionMount">Function Mount</el-dropdown-item>
                </el-dropdown-menu>
              </el-dropdown>
            </div>
          </div>

          <!-- list -->
          <div style="max-width: 20rem" v-bar>
            <div>
              <div
                :key="group.id"
                v-for="group in grouped_pipes"
              >
                <div class="pa2 flex flex-row items-center">
                  <ServiceIcon
                    class="br1 square-2"
                    style="margin-right: 8px"
                    :empty-cls="''"
                    :type="group.ctype"
                    v-if="group.ctype.length > 0"
                  />
                  <i
                    class="material-icons"
                    style="margin-right: 6px"
                    v-else
                  >
                    home
                  </i>
                  <div class="flex-fill fw6">{{group.title}}</div>
                  <el-dropdown
                    trigger="click"
                    v-show="group.id != 'local'"
                  >
                    <span class="el-dropdown-link pointer mr2">
                      <svg class="octicon octicon-gear" viewBox="0 0 14 16" version="1.1" width="14" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M14 8.77v-1.6l-1.94-.64-.45-1.09.88-1.84-1.13-1.13-1.81.91-1.09-.45-.69-1.92h-1.6l-.63 1.94-1.11.45-1.84-.88-1.13 1.13.91 1.81-.45 1.09L0 7.23v1.59l1.94.64.45 1.09-.88 1.84 1.13 1.13 1.81-.91 1.09.45.69 1.92h1.59l.63-1.94 1.11-.45 1.84.88 1.13-1.13-.92-1.81.47-1.09L14 8.75v.02zM7 11c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3z"></path></svg>
                    </span>
                    <el-dropdown-menu style="min-width: 10rem" slot="dropdown">
                      <el-dropdown-item
                        class="flex flex-row items-center"
                        @click.native="editFunctionMount(group.connection)"
                      >
                        <i class="material-icons mr2">edit</i> Edit
                      </el-dropdown-item>
                      <el-dropdown-item
                        class="flex flex-row items-center"
                        @click.native="syncFunctionMount(group.connection)"
                      >
                        <i class="material-icons mr2">refresh</i> Refresh
                      </el-dropdown-item>
                      <el-dropdown-item divided></el-dropdown-item>
                      <el-dropdown-item
                        class="flex flex-row items-center"
                        @click.native="tryDeleteFunctionMount(group.connection)"
                      >
                        <i class="material-icons mr2">delete</i> Remove
                      </el-dropdown-item>
                    </el-dropdown-menu>
                  </el-dropdown>
                </div>
                <div
                  class="pt1 pb3"
                  style="padding-left: 17px; padding-right: 0px; margin: 0 12px 0 3px; font-size: 13px"
                  v-if="isFunctionMountSyncing(group.connection)"
                >
                  <div class="flex flex-row items-center">
                    <Spinner size="small" />
                    <span class="ml2">Importing...</span>
                  </div>
                  <div class="pt2 lh-title silver i">NOTE: Importing from a function mount may take awhile to complete</div>
                </div>
                <div
                  class="pt1 pb3 lh-title silver i"
                  style="padding-left: 17px; padding-right: 0px; margin: 0 12px 0 3px; font-size: 13px"
                  v-else-if="group.pipes.length == 0"
                >
                  <span v-if="pipe_list_filter.length > 0">There are no functions that match the search criteria</span>
                  <span v-else>There are no functions to show</span>
                </div>
                <PipeList
                  class="mb3"
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
            </div>
          </div>
        </div>

        <!-- content area -->
        <div class="flex-fill ph4 pv5 overflow-y-scroll">
          <PipeDocument
            class="w-100 center mw-doc pa4 bg-white br2 css-white-box"
            style="min-height: 20rem"
            :pipe-eid="pipe.eid"
            @edit-click="onEditPipe"
          />
        </div>

        <PipeDocumentTestPanel
          class="min-w5 bg-white bl b--black-05"
          style="width: 25rem"
          :pipe-eid="pipe.eid"
          :visible.sync="show_test_panel"
        />

        <el-button
          size="small"
          type="text"
          class="absolute right-2 top-0"
          @click="show_test_panel = true"
          v-show="!show_test_panel && false"
        >
          Test Function
        </el-button>
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
        :mode="pipe_edit_mode"
        :pipe="pipe_edit_mode == 'edit' ? edit_pipe : new_pipe_attrs"
        @close="show_pipe_dialog = false"
        @cancel="show_pipe_dialog = false"
        @update-pipe="onPipeAdded"
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
        :mode="connection_edit_mode"
        :show-steps="connection_edit_mode == 'edit' ? false : true"
        :connection="connection_edit_mode == 'edit' ? edit_connection : new_connection_attrs"
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
  import randomstring from 'randomstring'
  import { ROUTE_APP_PIPES } from '@/constants/route'
  import { OBJECT_STATUS_AVAILABLE } from '@/constants/object-status'
  import { mapState, mapGetters } from 'vuex'
  import { btoaUnicode } from '@/utils'
  import Spinner from 'vue-simple-spinner'
  import PipeList from '@/components/PipeList'
  import PipeDocument from '@/components/PipeDocument'
  import PipeDocumentTestPanel from '@/components/PipeDocumentTestPanel'
  import PipeEditPanel from '@/components/PipeEditPanel'
  import ConnectionEditPanel from '@/components/ConnectionEditPanel'
  import ServiceIcon from '@/components/ServiceIcon'
  import EmptyItem from '@/components/EmptyItem'
  import PageNotFound from '@/components/PageNotFound'
  import MixinConnection from '@/components/mixins/connection'
  import MixinFilter from '@/components/mixins/filter'

  const CONNECTION_MODE_RESOURCE = 'R'
  const CONNECTION_MODE_FUNCTION = 'F'

  const code_python = btoaUnicode(`# basic hello world example
def flex_handler(flex):
    flex.end([["H","e","l","l","o"],["W","o","r","l","d"]])
`)

  const getNameSuffix = (length) => {
    return randomstring.generate({
      length,
      charset: 'alphabetic',
      capitalization: 'lowercase'
    })
  }

  const defaultAttrs = () => {
    // when creating a new function, start out with a basic Python 'Hello World' script
    return {
      deploy_mode: 'R',
      deploy_api: 'A',
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
    mixins: [MixinConnection, MixinFilter],
    components: {
      Spinner,
      PipeList,
      PipeDocument,
      PipeDocumentTestPanel,
      PipeEditPanel,
      ConnectionEditPanel,
      ServiceIcon,
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
        pipe_list_filter: '',
        pipe: {},
        last_selected: {},
        show_pipe_dialog: false,
        pipe_edit_mode: 'add',
        edit_connection: null,
        new_pipe_attrs: {},
        show_connection_dialog: false,
        connection_edit_mode: 'add',
        edit_connection: null,
        new_connection_attrs: {
          connection_mode: CONNECTION_MODE_FUNCTION
        },
        show_test_panel: false
      }
    },
    computed: {
      // mix this into the outer object with the object spread operator
      ...mapState({
        is_fetching: state => state.pipes.is_fetching,
        is_fetched: state => state.pipes.is_fetched,
        active_team_name: state => state.teams.active_team_name
      }),
      function_mounts() {
        return this.getAvailableFunctionMounts()
      },
      pipes() {
        return this.getAllPipes()
      },
      sorted_pipes() {
        return _.sortBy(this.pipes, ['name'])
      },
      filtered_pipes() {
        return this.$_Filter_filter(this.sorted_pipes, this.pipe_list_filter, ['name'])
      },
      grouped_pipes() {
        // start with an object with connection eids for keys and empty arrays for values
        var all_mounts = _.keyBy(this.function_mounts, 'eid')
        all_mounts = _.mapValues(all_mounts, m => [])
        all_mounts[''] = [] // include 'Local'

        // group all pipes by their parent connection (this will also result in
        // an object with connection eids as the key values)
        var mounts_with_pipes = _.groupBy(this.filtered_pipes, p => _.get(p, 'parent.eid', 'local'))

        // overwrite keys in 'all_mounts' with keys in 'mounts_with_pipes'
        var groups = _.assign({}, all_mounts, mounts_with_pipes)

        // return the following object for each function mount
        groups = _.map(groups, (val, key) => {
          var connection = _.find(this.function_mounts, { eid: key })

          return {
            id: key.length == 0 ? 'local' : key,
            ctype: _.get(connection, 'connection_type', ''),
            title: key.length == 0 ? 'Local' : _.get(connection, 'name', `Not found (${key})`),
            pipes: _.sortBy(val, ['name']),
            connection,
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
          var selected_idx = _.findIndex(this.sorted_pipes, { eid: this.pipe.eid })
          var deleting_idx = _.findIndex(this.sorted_pipes, { eid: attrs.eid })

          this.$store.dispatch('pipes/delete', { team_name, eid }).then(response => {
            if (deleting_idx >= 0 && deleting_idx == selected_idx) {
              if (deleting_idx >= this.sorted_pipes.length) {
                deleting_idx--
              }

              var pipe = _.get(this.sorted_pipes, '['+deleting_idx+']', {})
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

        var msg = `<p>Removing a function mount will also remove all functions associated with the function mount.</p><div class="h1"></div><p>Are you sure you want to remove the function mount named <strong>"${cname}"</strong>?</p>`
        var title = `Really remove function mount?`

        this.$confirm(msg, title, {
          type: 'warning',
          confirmButtonClass: 'ttu fw6',
          cancelButtonClass: 'ttu fw6',
          confirmButtonText: 'Remove function mount',
          cancelButtonText: 'Cancel',
          dangerouslyUseHTMLString: true
        }).then(() => {
          this.$store.dispatch('connections/delete', { team_name, eid })
        })
      },
      syncFunctionMount(connection) {
        var team_name = this.active_team_name
        var eid = _.get(connection, 'eid', '')

        this.$store.dispatch('connections/sync', { team_name, eid })
        this.show_connection_dialog = false
      },
      editFunctionMount(connection) {
        this.edit_connection = connection
        this.connection_edit_mode = 'edit'
        this.show_connection_dialog = true
      },
      isFunctionMountSyncing(connection) {
        return _.get(connection, 'vuex_meta.is_syncing', false)
      },
      loadPipe(identifier) {
        var pipe

        if (identifier) {
          pipe = _.find(this.sorted_pipes, p => p.eid == identifier || p.name == identifier)
        } else {
          pipe = _.first(this.sorted_pipes)
        }

        this.selectPipe(pipe)
      },
      selectPipe(item) {
        if (this.sorted_pipes.length == 0) {
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
      filterByFunctionMount(connection) {
        return this.$_Connection_isFunctionMount(connection)
      },
      getNewPipeAttributes(op) {
        var step = { op }

        switch (op) {
          case 'execute':
            _.assign(step, { lang: 'python', code: code_python })
            break
          case 'extract':
            _.assign(step, { path: '' })
            break
          case 'lookup':
            _.assign(step, { path: '', lookup_keys: [], return_columns: [] })
            break
        }

        var name = op + '-' + getNameSuffix(4)
        var task = {
          op: 'sequence',
          items: [step]
        }

        return _.assign({}, defaultAttrs(), { name, task })
      },
      onPipeAdded(pipe) {
        this.selectPipe(pipe)
        this.show_pipe_dialog = false
      },
      onNewPipe(op) {
        this.new_pipe_attrs = this.getNewPipeAttributes(op)
        this.pipe_edit_mode = 'add'
        this.show_pipe_dialog = true
      },
      onEditPipe(pipe) {
        this.edit_pipe = pipe
        this.pipe_edit_mode = 'edit'
        this.show_pipe_dialog = true
      },
      onNewFunctionMount() {
        this.connection_edit_mode = 'add'
        this.show_connection_dialog = true
      },
    }
  }
</script>

<style lang="stylus" scoped>
  .octicon-gear
    fill: rgba(0,0,0,0.3)
    &:hover
      fill: #000
</style>
