<template>
  <!-- fetching -->
  <div v-if="is_fetching">
    <div class="flex flex-row items-center">
      <Spinner :size="22" :line-size="2" />
      <span class="ml2 f6">Loading...</span>
    </div>
  </div>

  <!-- fetched -->
  <div v-else-if="is_fetched">
    <!-- header -->
    <div class="pipe-header flex flex-row">
      <div class="flex-fill flex flex-row items-center">
        <div class="f3 fw6 lh-title">{{pipe.name}}</div>
        <LabelSwitch
          class="ml3 hint--bottom"
          active-color="#13ce66"
          :aria-label="is_deployed ? 'Turn function off' : 'Turn function on'"
          :width="58"
          v-model="is_deployed"
          v-require-rights:pipe.update
        />
      </div>
      <div class="flex-none">
        <el-button
          class="ttu fw6"
          style="min-width: 5rem"
          size="small"
          type="primary"
          v-require-rights:pipe.update
        >
          Rename
        </el-button>
      </div>
    </div>

    <!-- description -->
    <div class="pipe-section">
      <div class="flex flex-row items-center pb2 bb b--black-10">
        <div class="flex-fill f4 fw6 lh-title">Description</div>
        <div class="flex-none">
          <el-button
            style="padding: 0"
            type="text"
            @click="is_description_editing = true"
            v-show="!is_description_editing"
            v-require-rights:pipe.update
          >
            Edit
          </el-button>
        </div>
      </div>
      <PipeDocumentMarkdownEditor
        class="pipe-editable"
        :class="is_description_editing ? 'is-editing' : ''"
        :value="pipe_description"
        :is-editing.sync="is_description_editing"
        @save-click="updateDescription"
      >
        <span slot="empty"><em>(No description)</em></span>
      </PipeDocumentMarkdownEditor>
    </div>

    <!-- syntax -->
    <div class="pipe-section">
      <div class="flex flex-row items-center pb2 bb b--black-10">
        <div class="flex-fill f4 fw6 lh-title">Syntax</div>
        <div class="flex-none">
          <el-button
            style="padding: 0"
            type="text"
            @click="is_syntax_editing = true"
            v-show="!is_syntax_editing"
            v-require-rights:pipe.update
          >
            Edit
          </el-button>
        </div>
      </div>
      <PipeDocumentMarkdownEditor
        class="pipe-editable"
        :class="is_syntax_editing ? 'is-editing' : ''"
        :value="pipe_syntax"
        :is-editing.sync="is_syntax_editing"
        @save-click="updateSyntax"
      >
        <span slot="empty"><em>(No syntax)</em></span>
      </PipeDocumentMarkdownEditor>
    </div>

    <!-- configuration -->
    <div class="pipe-section">
      <div class="flex flex-row items-center pb2 bb b--black-10">
        <div class="flex-fill f4 fw6 lh-title">Configuration</div>
        <div class="flex-none">
          <el-button
            style="padding: 0"
            type="text"
            v-require-rights:pipe.update
          >
            Edit
          </el-button>
        </div>
      </div>
      <PipeDocumentTaskExtract
        class="pipe-editable"
        :class="is_task_editing ? 'is-editing' : ''"
        :item="pipe_task"
        :is-editing.sync="is_task_editing"
        :is-save-allowed.sync="is_task_save_allowed"
        v-if="pipe_task_type == 'extract'"
      />
      <PipeDocumentTaskLookup
        class="pipe-editable"
        :class="is_task_editing ? 'is-editing' : ''"
        :item="pipe_task"
        :is-editing.sync="is_task_editing"
        :is-save-allowed.sync="is_task_save_allowed"
        v-else-if="pipe_task_type == 'lookup'"
      />
      <PipeDocumentTaskExecute
        class="pipe-editable"
        :class="is_task_editing ? 'is-editing' : ''"
        :task="pipe_task"
        :is-editing.sync="is_task_editing"
        :is-save-allowed.sync="is_task_save_allowed"
        @save-click="updateTask"
        v-else-if="pipe_task_type == 'execute'"
      />
      <div
        v-else
      >
        Unknown Task
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import LabelSwitch from '@/components/LabelSwitch'
  import PipeDocumentMarkdownEditor from '@/components/PipeDocumentMarkdownEditor'
  import PipeDocumentTaskExtract from '@/components/PipeDocumentTaskExtract'
  import PipeDocumentTaskLookup from '@/components/PipeDocumentTaskLookup'
  import PipeDocumentTaskExecute from '@/components/PipeDocumentTaskExecute'

  const DEPLOY_MODE_UNDEFINED = ''
  const DEPLOY_MODE_BUILD     = 'B'
  const DEPLOY_MODE_RUN       = 'R'

  export default {
    props: {
      pipeEid: {
        type: String,
        required: true
      }
    },
    components: {
      Spinner,
      LabelSwitch,
      PipeDocumentMarkdownEditor,
      PipeDocumentTaskExtract,
      PipeDocumentTaskLookup,
      PipeDocumentTaskExecute,
    },
    watch: {
      pipeEid: {
        handler: 'tryFetchPipe',
        immediate: true,
      }
    },
    data() {
      return {
        is_local_fetching: false,
        is_task_save_allowed: false,
        is_task_editing: false,
        is_description_editing: false,
        is_syntax_editing: false,
      }
    },
    computed: {
      ...mapState({
        active_team_name: state => state.teams.active_team_name
      }),
      pipe() {
        return _.get(this.$store.state.pipes, `items.${this.pipeEid}`, {})
      },
      is_fetched() {
        return _.get(this.pipe, 'vuex_meta.is_fetched', false)
      },
      is_fetching() {
        return _.get(this.pipe, 'vuex_meta.is_fetching', false) || this.is_local_fetching /* Vuex pipe `is_fetching` isn't yet implemented */
      },
      pipe_description() {
        return _.get(this.pipe, 'description', '')
      },
      pipe_syntax() {
        return _.get(this.pipe, 'syntax', '')
      },
      pipe_task() {
        return _.get(this.pipe, 'task.items[0]', {})
      },
      pipe_task_type() {
        return _.get(this.pipe_task, 'op', '')
      },
      is_deployed: {
        get() {
          return _.get(this.pipe, 'deploy_mode') == DEPLOY_MODE_RUN ? true : false
        },
        set(value) {
          if (value === false) {
            this.$confirm('This function is turned on and is possibly being used in a production environment. Are you sure you want to continue?', 'Really turn function off?', {
              confirmButtonClass: 'ttu fw6',
              cancelButtonClass: 'ttu fw6',
              confirmButtonText: 'Turn function off',
              cancelButtonText: 'Cancel',
              type: 'warning'
            }).then(() => {
              this.deployPipe(value)
            })
          } else {
            this.deployPipe(value)
          }
        }
      },
    },
    methods: {
      tryFetchPipe() {
        var team_name = this.active_team_name
        var name = this.pipe.name

        if (!this.is_fetched && !this.is_fetching) {
          this.is_local_fetching = true
          this.$store.dispatch('pipes/fetch', { team_name, name }).finally(() => {
            this.is_local_fetching = false
          })
        }
      },
      savePipe(attrs) {
        var team_name = this.active_team_name
        var eid = this.pipe.eid

        return this.$store.dispatch('pipes/update', { team_name, eid, attrs }).then(response => {
          var updated_pipe = response.data

          this.$message({
            message: 'The function was updated successfully.',
            type: 'success'
          })

          // change the object name in the route
          if (updated_pipe.name != this.pipe.name) {
            var object_name = updated_pipe.name

            var new_route = _.pick(this.$route, ['name', 'meta', 'params', 'path'])
            new_route.params = _.assign({}, new_route.params, { object_name })
            this.$router.replace(new_route)
          }
        })
      },
      deployPipe(is_deployed) {
        var deploy_mode = is_deployed === false ? DEPLOY_MODE_BUILD : DEPLOY_MODE_RUN
        var attrs = { deploy_mode }
        this.savePipe(attrs)
      },
      updateDescription(obj) {
        var attrs = { description: obj.new_value }
        this.savePipe(attrs)
        this.is_description_editing = false
      },
      updateSyntax(obj) {
        var attrs = { syntax: obj.new_value }
        this.savePipe(attrs)
        this.is_syntax_editing = false
      },
      updateTask(new_task, old_task) {
        var attrs = {
          task: {
            op: 'sequence',
            items: [new_task]
          }
        }
        this.savePipe(attrs)
      },
    }
  }
</script>

<style lang="stylus" scoped>
  .pipe-header
    margin-bottom: 32px

  .pipe-section
    margin-top: 8px
    margin-bottom: 8px
    &:last-child
      margin-bottom: 0

  .pipe-editable
    padding: 16px
    transition: all 0.15s ease
    &.is-editing
      border-radius: 3px
      box-shadow: 0 0 0 1px rgba(64, 158, 255, 1), 0 0 0 5px rgba(64, 158, 255, 0.4)
</style>
