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
                <el-dropdown trigger="click" @command="onCommand">
                  <el-button
                    size="small"
                    type="primary"
                    class="ttu fw6"

                  >
                    New<i class="el-icon-arrow-down el-icon--right"></i>
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
                <div class="pa2 mt2">
                  {{group.title}}
                </div>
                <PipeList
                  class="mb2"
                  item-style="padding-left: 22px"
                  item-size="mini"
                  :items="group.pipes"
                  :selected-item.sync="pipe"
                  :show-delete="group.id == 'local'"
                  @item-activate="selectPipe"
                  @item-delete="tryDeletePipe"
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
  import EmptyItem from '@/components/EmptyItem'
  import PageNotFound from '@/components/PageNotFound'

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
    components: {
      Spinner,
      PipeList,
      PipeDocument,
      PipeEditPanel,
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
        return this.getAllPipes()
      },
      connections() {
        return this.getAllConnections()
      },
      grouped_pipes() {
        var groups = _.groupBy(this.pipes, p => _.get(p, 'parent.eid', 'local'))
        return _.map(groups, (val, key) => {
          var connection = _.find(this.connections, { eid: key })
          return {
            id: key.length == 0 ? 'local' : key,
            title: key.length == 0 ? 'Local' : _.get(connection, 'name', 'Connection'),
            pipes: val
          }
        })
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
        'getAllConnections': 'getAllConnections'
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

        this.$confirm('Are you sure you want to delete the function named "' + pname + '"?', 'Really delete function?', {
          confirmButtonClass: 'ttu fw6',
          cancelButtonClass: 'ttu fw6',
          confirmButtonText: 'Delete function',
          cancelButtonText: 'Cancel',
          type: 'warning'
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
      onCommand(cmd) {
        switch (cmd)
        {
          case 'local-function': this.show_pipe_dialog = true; return
          case 'function-mount': alert('TODO: Add function mount'); return
        }
      }
    }
  }
</script>
