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
                  @click="show_pipe_new_dialog = true"
                >
                  New
                </el-button>
              </div>
            </div>
          </div>

          <div class="flex-fill overflow-y-auto">
            <article
              class="min-w5 pa3 bb b--black-05 bg-white hover-bg-nearer-white"
              :class="isPipeSelected(pipe) ? 'relative bg-nearer-white' : ''"
              @click="selectPipe(pipe)"
              v-for="pipe in pipes"
            >
              <div class="f5 fw6 cursor-default mr1">{{pipe.name}}</div>
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
        pipe: {},
        last_selected: {},
        show_pipe_new_dialog: false
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
        var short_description = _.get(attrs, 'short_description', 'Pipe')

        this.$confirm('Are you sure you want to delete the pipe named "' + short_description + '"?', 'Really delete pipe?', {
          confirmButtonClass: 'ttu fw6',
          cancelButtonClass: 'ttu fw6',
          confirmButtonText: 'Delete pipe',
          cancelButtonText: 'Cancel',
          type: 'warning'
        }).then(() => {
          this.$store.dispatch('v2_action_deletePipe', { eid }).catch(error => {
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

        this.selectPipe(pipe, false)
      },
      selectPipe(item, push_route) {
        var pipe = item

        if (!pipe) {
          pipe = _.first(this.pipes)
        }

        this.pipe = _.cloneDeep(pipe)
        this.last_selected = _.cloneDeep(pipe)

        // update the route
        if (push_route !== false) {
          // update the route
          var name = _.get(pipe, 'name', '')
          var identifier = name.length > 0 ? name : _.get(pipe, 'eid', '')

          var new_route = _.pick(this.$route, ['name', 'meta', 'params', 'path'])
          _.set(new_route, 'params.identifier', identifier)
          this.$router.push(new_route)
        }
      },
      isPipeSelected(pipe) {
        return _.get(this.pipe, 'eid') === pipe.eid
      },
      onNewPipeClick() {
        // when creating a new pipe, start out with a basic Python 'Hello World' script
        var attrs = {
          short_description: 'Untitled Pipe',
          task: {
            op: 'sequence',
            items: [{
              op: 'execute',
              lang: 'python',
              code: 'IyBiYXNpYyBoZWxsbyB3b3JsZCBleGFtcGxlCmRlZiBmbGV4X2hhbmRsZXIoZmxleCk6CiAgICBmbGV4LmVuZCgiSGVsbG8sIFdvcmxkLiIpCg=='
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
