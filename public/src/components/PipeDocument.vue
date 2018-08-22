<template>
  <!-- pipe fetching -->
  <div class="bg-nearer-white" v-if="is_fetching">
    <div class="h-100 flex flex-row items-center justify-center">
      <Spinner size="large" message="Loading..." />
    </div>
  </div>

  <!-- pipe fetched -->
  <div class="bg-nearer-white" v-else-if="is_fetched">
    <!-- runtime view -->
    <div
      class="h-100 pa4 overflow-y-scroll"
      v-if="is_view_runtime"
    >
      <!-- runtime view; run mode -->
      <div class="mv4 center mw-doc" v-if="is_pipe_mode_run">
        <div>Runtime view</div>
        <el-button @click="active_view = 'build'">Set build view</el-button>
      </div>

      <!-- runtime view; build mode -->
      <div class="mv4 center mw-doc" v-else>
        <div class="pa4 bg-white br2 tc css-white-box">
          <div class="dib mb3 pv1">
            <i class="el-icon-warning v-mid f1" style="color: #ec7713"></i>
          </div>
          <h3 class="fw6 f3 mt0 mb4">This pipe cannot be run in a browser.</h3>
          <p class="center lh-copy mv4 mw7">If you are the owner of this pipe, please turn it on. If this pipe was shared with you, please contact the person who shared it with you to have it turned on.</p>
        </div>
      </div>
    </div>

    <!-- build view; run mode -->
    <div
      class="h-100 pa4 overflow-y-scroll"
      v-else-if="is_pipe_mode_run"
    >
      <div class="mv4 center mw-doc">
        <div class="pa4 pt3 bg-white br2 tc css-white-box">
          <div class="dib mv3 pv1">
            <i class="el-icon-success v-mid f1" style="color: #13ce66"></i>
          </div>
          <h3 class="fw6 f3 mt0 mb4">Your pipe is working!</h3>
          <p class="mv4">Click the button below or use the following link to run your pipe in a browser.</p>
          <div class="mv4 mw7 center">
            <el-input
              :readonly="true"
              v-model="runtime_link"
            >
              <template slot="append">
                <el-button
                  class="hint--top"
                  aria-label="Copy to Clipboard"
                  :data-clipboard-text="runtime_link"
                ><span class="ttu b">Copy</span></el-button>
              </template>
            </el-input>
          </div>
          <div class="mv4">
            <el-button
              size="large"
              type="primary"
              class="ttu b"
              @click="openPipeInNewWindow"
            >
              Run pipe
            </el-button>
          </div>
          <div class="bb b--black-05 mv4"></div>
          <div class="flex flex-row items-center justify-center">
            <span class="ttu f6 fw6">Your pipe is</span>
            <LabelSwitch
              class="dib ml2"
              active-color="#13ce66"
              v-model="is_pipe_mode_run"
            />
          </div>
          <p class="moon-gray f8 i">(you may turn the pipe off to edit it)</p>
        </div>
      </div>
    </div>

      <!-- build view; build mode -->
    <multipane
      class="vertical-panes"
      layout="vertical"
      v-else
    >
      <div
        class="pane overflow-y-auto"
        :style="{ minWidth: '100px', width: '22%', maxWidth: '40%' }"
      >
        <div>Code</div>
        <el-button @click="is_pipe_mode_run = 'R'">Set pipe run mode</el-button>
        <el-button @click="active_view = 'run'">Set runtime view</el-button>
      </div>
      <multipane-resizer />
      <div
        class="pane pa4 overflow-y-scroll"
        :style="{ flexGrow: 1 }"
      >
        <div class="mv4 center mw-doc">
          <div class="mv4 pv4 bg-white br2 css-white-box">
            UI Builder
          </div>
          <div class="mv4 pv4 bg-white br2 css-white-box">
            Pipe Builder
          </div>
        </div>
      </div>
    </multipane>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import { Multipane, MultipaneResizer } from 'vue-multipane'
  import Spinner from 'vue-simple-spinner'
  import LabelSwitch from './LabelSwitch.vue'

  const PIPE_MODE_UNDEFINED = ''
  const PIPE_MODE_BUILD     = 'B'
  const PIPE_MODE_RUN       = 'R'

  const PIPEDOC_VIEW_BUILD  = 'build'
  const PIPEDOC_VIEW_RUN    = 'run'

  export default {
    components: {
      Multipane,
      MultipaneResizer,
      Spinner,
      LabelSwitch
    },
    watch: {
      eid: {
        handler: 'loadPipe',
        immediate: true
      },
      active_view: {
        handler: 'updateRoute',
        immediate: true
      }
    },
    data() {
      return {
        active_view: _.get(this.$route, 'params.view', PIPEDOC_VIEW_BUILD)
      }
    },
    computed: {
      ...mapState({
        orig_pipe: state => state.pipe.orig_pipe,
        edit_keys: state => state.pipe.edit_keys,
        is_fetching: state => state.pipe.fetching,
        is_fetched: state => state.pipe.fetched
      }),
      eid() {
        return _.get(this.$route, 'params.eid', undefined)
      },
      runtime_link() {
        return 'https://' + window.location.hostname + '/app/pipes/' + this.eid + '/run'
      },
      edit_pipe: {
        get() {
          var pipe = _.get(this.$store.state.pipe, 'edit_pipe', {})
          return pipe
        },
        set(value) {
          try {
            var pipe = _.cloneDeep(value)
            this.$store.commit('pipe/UPDATE_EDIT_PIPE', pipe)
          }
          catch(e)
          {
            // TODO: add error handling
          }
        }
      },
      // if we're in runtime mode or not...
      is_view_runtime() {
        return this.active_view == PIPEDOC_VIEW_RUN
      },
      // if we're in build mode, but `pipe_mode == 'R'`
      is_pipe_mode_run: {
        get() {
          return _.get(this.orig_pipe, 'pipe_mode') == PIPE_MODE_RUN ? true : false
        },
        set() {
          var doSet = () => {
            var pipe_mode = this.is_pipe_mode_run ? PIPE_MODE_BUILD : PIPE_MODE_RUN
            var pipe = _.cloneDeep(this.edit_pipe)
            _.assign(pipe, { pipe_mode })
            this.$store.commit('pipe/UPDATE_EDIT_PIPE', pipe)
            this.saveChanges()
          }

          if (this.is_pipe_mode_run) {
            this.$confirm('This pipe is turned on and is possibly being used in a production environment. Are you sure you want to continue?', 'Really turn pipe off?', {
              confirmButtonText: 'TURN PIPE OFF',
              cancelButtonText: 'CANCEL',
              type: 'warning'
            }).then(() => {
              doSet()
            }).catch(() => {
              // do nothing
            })
          } else {
            doSet()
          }
        }
      }
    },
    methods: {
      loadPipe() {
        this.$store.commit('pipe/FETCHING_PIPE', true)

        this.$store.dispatch('fetchPipe', { eid: this.eid }).then(response => {
          if (response.ok) {
            var pipe = response.data
            this.$store.commit('pipe/INIT_PIPE', pipe)
            this.$store.commit('pipe/FETCHING_PIPE', false)
          } else {
            this.$store.commit('pipe/FETCHING_PIPE', false)
          }
        })
      },
      saveChanges() {
        var eid = this.eid
        var attrs = _.pick(this.edit_pipe, this.edit_keys)

        // don't POST null values
        attrs = _.omitBy(attrs, (val, key) => { return _.isNil(val) })

        return this.$store.dispatch('updatePipe', { eid, attrs }).then(response => {
          if (response.ok) {
            this.$message({
              message: 'The pipe was updated successfully.',
              type: 'success'
            })

            this.$store.commit('pipe/INIT_PIPE', response.body)
          } else {
            this.$message({
              message: 'There was a problem updating the pipe.',
              type: 'error'
            })
          }
        })
      },
      updateRoute() {
        // update the route
        var new_route = _.pick(this.$route, ['name', 'meta', 'params', 'path'])
        var view = this.active_view
        _.set(new_route, 'params.view', view)
        this.$router.replace(new_route)
      },
      openPipeInNewWindow() {
        window.open(this.runtime_link, '_blank')
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .vertical-panes
    width: 100%
    height: 100%

  .vertical-panes > .pane ~ .pane
    border-left: 1px solid #ddd
</style>
