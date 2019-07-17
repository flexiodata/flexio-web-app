<template>
  <!-- fetching -->
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100">
      <Spinner size="large" message="Loading pipes..." />
    </div>
  </div>

  <!-- fetched -->
  <div class="flex flex-column" v-else-if="is_fetched">
    <div class="flex-fill flex flex-row" v-if="pipes.length > 0">
      <template v-if="has_pipe">
        <!-- list -->
        <div
          class="flex flex-column min-w5 br b--black-05"
          :class="mode == 'edit' ? 'o-40 no-pointer-events': ''"
        >
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
                  @click="onNewPipeClick"
                >
                  New
                </el-button>
              </div>
            </div>
          </div>

          <div class="flex-fill overflow-y-auto" style="max-width: 20rem">
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
                  <div class="flex flex-row items-center" v-if="pipe.short_description.length > 0">
                    <div class="mr2" style="width: 8px; height: 8px"></div>
                    <div class="light-silver f8 lh-copy" style="margin-top: 3px">{{pipe.short_description}}</div>
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
  </div>
</template>

<script>
  import { ROUTE_APP_PIPES } from '../constants/route'
  import { OBJECT_STATUS_AVAILABLE } from '../constants/object-status'
  import { mapState, mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import PipeDocument from '@comp/PipeDocument'
  import PageNotFound from '@comp/PageNotFound'

  const DEPLOY_MODE_RUN       = 'R'

  export default {
    metaInfo() {
      return {
        title: _.get(this.pipe, 'name', 'Pipes')
      }
    },
    components: {
      Spinner,
      PipeDocument,
      PageNotFound
    },
    watch: {
      route_identifier: {
        handler: 'loadPipe',
        immediate: true
      },
      pipes(val, old_val) {
        if (!this.has_pipe) {
          this.loadPipe(this.route_identifier)
        }
      }
    },
    data() {
      return {
        mode: 'static',
        is_selecting: false,
        pipe: {},
        last_selected: {}
      }
    },
    computed: {
      // mix this into the outer object with the object spread operator
      ...mapState({
        'is_fetching': 'pipes_fetching',
        'is_fetched': 'pipes_fetched'
      }),
      routed_user() {
        return this.$store.state.routed_user
      },
      route_identifier() {
        return _.get(this.$route, 'params.identifier', undefined)
      },
      pipes() {
        return this.getAllPipes()
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
      ...mapGetters([
        'getAllPipes'
      ]),
      openPipe(eid) {
        // TODO: this component shouldn't have anything to do with the route or store state
        var ru = this.routed_user
        var user_identifier = ru && ru.length > 0 ? ru : null
        var identifier = eid
        this.$router.push({ name: ROUTE_APP_PIPES, params: { user_identifier, identifier } })
      },
      duplicatePipe(item) {
        var attrs = {
          copy_eid: item.eid,
          short_description: item.short_description
        }

        this.$store.dispatch('v2_action_createPipe', { attrs }).catch(error => {
          // TODO: add error handling?
        })
      },
      tryFetchPipes() {
        if (!this.is_fetched && !this.is_fetching) {
          this.$store.dispatch('v2_action_fetchPipes', {}).catch(error => {
            // TODO: add error handling?
          })
        }
      },
      tryCreatePipe(attrs) {
        if (!_.isObject(attrs))
          attrs = { short_description: 'Untitled Pipe' }

        this.$store.dispatch('v2_action_createPipe', { attrs }).then(response => {
          var pipe = response.data
          var analytics_payload = _.pick(pipe, ['eid', 'name', 'short_description', 'created'])
          this.$store.track('Created Pipe', analytics_payload)
          this.openPipe(pipe.eid)
        }).catch(error => {
          this.$store.track('Created Pipe (Error)')
        })
      },
      tryDeletePipe(attrs) {
        var eid = _.get(attrs, 'eid', '')
        var pname = _.get(attrs, 'name', 'Pipe')

        this.$confirm('Are you sure you want to delete the pipe named "' + pname + '"?', 'Really delete pipe?', {
          confirmButtonClass: 'ttu fw6',
          cancelButtonClass: 'ttu fw6',
          confirmButtonText: 'Delete pipe',
          cancelButtonText: 'Cancel',
          type: 'warning'
        }).then(() => {
          var idx = _.findIndex(this.pipes, this.pipe)

          this.$store.dispatch('v2_action_deletePipe', { eid }).then(response => {
            if (idx >= 0) {
              if (idx >= this.pipes.length) {
                idx--
              }

              var pipe = _.get(this.pipes, '['+idx+']', {})
              this.selectPipe(pipe)
            }
          }).catch(error => {
            // TODO: add error handling?
          })
        }).catch(() => {
          // do nothing
        })
      },
      loadPipe(identifier) {
        var pipe

        if (identifier) {
          pipe = _.find(this.pipes, { eid: identifier })
          if (!pipe) {
            pipe = _.find(this.pipes, { name: identifier })
          }
        }

        this.selectPipe(pipe)
      },
      selectPipe(item) {
        if (this.pipes.length == 0) {
          return
        }

        var pipe = item
        if (!pipe) {
          pipe = _.first(this.pipes)
        }

        this.pipe = _.cloneDeep(pipe)
        this.last_selected = _.cloneDeep(pipe)

        if (!this.is_selecting) {
          // update the route
          var name = _.get(pipe, 'name', '')
          var identifier = name.length > 0 ? name : _.get(pipe, 'eid', '')

          var new_route = _.pick(this.$route, ['name', 'meta', 'params', 'path'])
          _.set(new_route, 'params.identifier', identifier)
          this.$router[!this.route_identifier?'replace':'push'](new_route)

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
      },
      onNewPipeClick() {
        // when creating a new pipe, start out with a basic Python 'Hello World' script
        var attrs = {
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

        this.tryCreatePipe(attrs)
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .sticky
    transition: all 0.15s ease

  .sticky.js-is-sticky
  .sticky.js-is-stuck
    border-bottom: 1px solid rgba(0,0,0,0.1)
    box-shadow: 0 4px 16px -6px rgba(0,0,0,0.2)
</style>
