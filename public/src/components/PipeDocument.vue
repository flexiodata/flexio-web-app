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
          <p class="mv4 lh-copy center mw7">If you are the owner of this pipe, please turn it on. If this pipe was shared with you, please contact the person who shared it with you to have it turned on.</p>
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
          <PipeDocumentRunPanel
            :eid="eid"
            :is-mode-run.sync="is_pipe_mode_run"
          />
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
        class="pane"
        :style="{ minWidth: '100px', width: '25%', maxWidth: '50%' }"
      >
        <PipeCodeEditor
          class="h-100"
          ref="code-editor"
          type="yaml"
          editor-cls="bg-white h-100"
          :task-only="false"
          :has-errors.sync="has_errors"
          @save="saveChanges"
          v-model="edit_pipe"
        />
      </div>
      <multipane-resizer />
      <div
        class="pane pa4 overflow-y-scroll"
        :style="{ flexGrow: 1 }"
      >
        <div class="mv4 center mw-doc">
          <div class="mv4 pa4 bg-white br2 css-white-box">
            <BuilderList
              builder-mode="wizard"
              :items="edit_ui_list"
              :container-id="doc_id"
              :active-item-idx.sync="active_ui_idx"
              :show-numbers="true"
              :show-icons="false"
              :show-insert-buttons="false"
              :show-edit-buttons="false"
              :show-delete-buttons="false"
              @item-prev="active_ui_idx--"
              @item-next="active_ui_idx++"
            />
          </div>
          <div class="mv4 pa4 bg-white br2 css-white-box">
            <PipeBuilderList
              :container-id="doc_id"
              :has-errors.sync="has_errors"
              :active-item-idx.sync="active_task_idx"
              @save="saveChanges"
              v-model="edit_task_list"
            />
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
  import BuilderList from './BuilderList.vue'
  import PipeBuilderList from './PipeBuilderList.vue'
  import PipeCodeEditor from './PipeCodeEditor.vue'
  import PipeDocumentRunPanel from './PipeDocumentRunPanel.vue'

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
      LabelSwitch,
      BuilderList,
      PipeBuilderList,
      PipeCodeEditor,
      PipeDocumentRunPanel
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
        active_view: _.get(this.$route, 'params.view', PIPEDOC_VIEW_BUILD),
        active_ui_idx: 0,
        active_task_idx: -1,
        has_errors: false
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
      doc_id() {
        return 'pipe-doc-' + this.eid
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
      edit_ui_list: {
        get() {
          var ui = _.get(this.edit_pipe, 'ui.prompts', [])
          return ui
        },
        set(value) {

        }
      },
      edit_task_list: {
        get() {
          var task = _.get(this.edit_pipe, 'task', { op: 'sequence', items: [] })
          return task
        },
        set(value) {
          try {
            var task = _.cloneDeep(value)
            var pipe = _.cloneDeep(this.edit_pipe)
            _.assign(pipe, { task })
            this.$store.commit('pipe/UPDATE_EDIT_PIPE', pipe)
          }
          catch(e)
          {
            // TODO: add error handling
          }
        }
      },
      is_view_runtime() {
        return this.active_view == PIPEDOC_VIEW_RUN
      },
      is_pipe_mode_run: {
        get() {
          return _.get(this.orig_pipe, 'pipe_mode') == PIPE_MODE_RUN ? true : false
        },
        set(value) {
          var doSet = () => {
            var pipe_mode = value === false ? PIPE_MODE_BUILD : PIPE_MODE_RUN
            var pipe = _.cloneDeep(this.edit_pipe)
            _.assign(pipe, { pipe_mode })
            this.$store.commit('pipe/UPDATE_EDIT_PIPE', pipe)
            this.saveChanges()
          }

          if (value === false) {
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
