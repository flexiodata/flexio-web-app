<template>
  <!-- fetching -->
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center bg-nearer-white h-100">
      <Spinner size="large" message="Loading pipes..." />
    </div>
  </div>

  <!-- fetched -->
  <div class="flex flex-column bg-nearer-white" v-else-if="is_fetched">
    <div class="flex-fill flex flex-row" v-if="pipes.length > 0">
      <template v-if="has_pipe">
        <!-- sidebar -->
        <div class="flex flex-column min-w5 br b--black-05">
          <!-- control bar -->
          <div class="flex-none ph3 pv2 relative bg-white bb b--black-05">
            <div class="flex flex-row">
              <div class="flex-fill flex flex-row items-center">
                <h2 class="mv0 f3 mr3">Pipes</h2>
              </div>
              <div class="flex-none flex flex-row items-center ml3">
                <el-button
                  size="small"
                  type="primary"
                  class="ttu fw6"
                  @click="show_pipe_dialog = true"
                >
                  New
                </el-button>
              </div>
            </div>
          </div>

          <!-- list -->
          <div class="flex-fill bg-white overflow-y-auto" style="max-width: 20rem">
            <article
              class="min-w5 pa3 bb b--black-05 bg-white hover-bg-nearer-white"
              :title="pipe.name"
              :class="isPipeSelected(pipe) ? 'relative bg-nearer-white' : ''"
              @click="selectPipe(pipe)"
              v-for="pipe in pipes"
            >
              <div class="flex flex-row items-center cursor-default">
                <div class="flex-fill">
                  <div class="flex flex-row items-center">
                    <div
                      class="br-100 mr2"
                      style="width: 8px; height: 8px"
                      :style="isPipeDeployed(pipe) ? 'background-color: #13ce66' : 'background-color: #dcdfe6'"
                    ></div>
                    <div class="flex-fill f5 fw6 cursor-default mr1 lh-title truncate">{{pipe.name}}</div>
                  </div>
                </div>
                <div class="flex-none ml2" @click.stop>
                    <el-dropdown trigger="click" @command="onCommand">
                      <span class="el-dropdown-link dib pointer pa1 black-30 hover-black">
                        <i class="material-icons v-mid">expand_more</i>
                      </span>
                      <el-dropdown-menu style="min-width: 10rem" slot="dropdown">
                        <el-dropdown-item class="flex flex-row items-center ph2" command="delete" :item="pipe"><i class="material-icons mr3">delete</i> Delete</el-dropdown-item>
                      </el-dropdown-menu>
                    </el-dropdown>
                </div>
              </div>
            </article>
          </div>
        </div>

        <!-- content area -->
        <PipeDocument class="flex-fill" />
      </template>

      <!-- pipe not found -->
      <PageNotFound class="flex-fill bg-nearer-white" v-else />
    </div>

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
  import PipeDocument from '@/components/PipeDocument'
  import PipeEditPanel from '@/components/PipeEditPanel'
  import PageNotFound from '@/components/PageNotFound'

  const DEPLOY_MODE_RUN = 'R'

  const defaultAttrs = () => {
    // when creating a new pipe, start out with a basic Python 'Hello World' script
    return {
      deploy_mode: 'R',
      deploy_api: 'A',
      deploy_ui: 'A',
      task: {
        op: 'sequence',
        items: [{
          op: 'execute',
          lang: 'python',
          code: 'IyBiYXNpYyBoZWxsbyB3b3JsZCBleGFtcGxlCmRlZiBmbGV4X2hhbmRsZXIoZmxleCk6CiAgICBmbGV4LmVuZChbWyJIIiwiZSIsImwiLCJsIiwibyJdLFsiVyIsIm8iLCJyIiwibCIsImQiXV0pCg=='
        }]
      }
    }
  }

  export default {
    metaInfo() {
      return {
        title: _.get(this.pipe, 'name', 'Pipes'),
        titleTemplate: (chunk) => {
          // if undefined or blank then we don't need the pipe
          return chunk ? `${chunk} | ${this.getActiveTeamLabel()}` : 'Flex.io';
        }
      }
    },
    components: {
      Spinner,
      PipeDocument,
      PipeEditPanel,
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
        pipe: {},
        last_selected: {},
        show_pipe_dialog: false
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
      this.$store.track('Visited Pipes Page')
    },
    methods: {
      ...mapGetters('teams', {
        'getActiveTeamLabel': 'getActiveTeamLabel'
      }),
      ...mapGetters('pipes', {
        'getAllPipes': 'getAllPipes'
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
            message: 'The pipe was created successfully.',
            type: 'success'
          })

          var analytics_payload = _.pick(pipe, ['eid', 'name', 'short_description', 'description', 'created'])
          this.$store.track('Created Pipe', analytics_payload)
          this.selectPipe(pipe)
          this.show_pipe_dialog = false
        })
      },
      tryDeletePipe(attrs) {
        var eid = _.get(attrs, 'eid', '')
        var pname = _.get(attrs, 'name', 'Pipe')
        var team_name = this.active_team_name

        this.$confirm('Are you sure you want to delete the pipe named "' + pname + '"?', 'Really delete pipe?', {
          confirmButtonClass: 'ttu fw6',
          cancelButtonClass: 'ttu fw6',
          confirmButtonText: 'Delete pipe',
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
        }).catch(() => {
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
      isPipeSelected(pipe) {
        return _.get(this.pipe, 'eid') === pipe.eid
      },
      isPipeDeployed(pipe) {
        return _.get(pipe, 'deploy_mode') == DEPLOY_MODE_RUN ? true : false
      },
      onCommand(cmd, menu_item) {
        switch (cmd) {
          case 'delete': return this.tryDeletePipe(menu_item.$attrs.item)
        }
      }
    }
  }
</script>
