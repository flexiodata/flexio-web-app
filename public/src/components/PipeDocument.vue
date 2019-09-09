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
    <div
      class="pipe-header flex flex-row"
      :class="is_task_editing || is_addon_editing ? 'o-40 no-pointer-events no-select' : ''"
    >
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
          size="small"
          class="ttu fw6"
          style="min-width: 5rem"
          @click="$emit('test-click')"
          v-show="showTestButton"
        >
          Test
        </el-button>
        <el-button
          size="small"
          type="primary"
          class="ttu fw6"
          style="min-width: 5rem"
          @click="onEditClick"
          v-require-rights:pipe.update
        >
          Edit
        </el-button>
      </div>
    </div>


    <!-- configuration -->
    <div
      class="pipe-section pipe-editable"
      :class="{
        'o-40 no-pointer-events no-select': is_addon_editing,
        'is-editing': is_task_editing
      }"
    >
      <div class="flex flex-row items-center pb2 bb b--black-10 pipe-section-title">
        <div class="flex-fill f4 fw6 lh-title">Configuration</div>
        <div class="flex-none">
          <el-button
            style="padding: 0"
            type="text"
            @click="is_task_editing = true"
            v-show="!is_task_editing"
            v-require-rights:pipe.update
          >
            Edit
          </el-button>
        </div>
      </div>
      <PipeDocumentTaskExtract
        :task="pipe_task"
        :is-editing.sync="is_task_editing"
        :is-save-allowed.sync="is_task_save_allowed"
        @save-click="updateTask"
        v-if="pipe_task_type == 'extract'"
      />
      <PipeDocumentTaskLookup
        :task="pipe_task"
        :is-editing.sync="is_task_editing"
        :is-save-allowed.sync="is_task_save_allowed"
        @save-click="updateTask"
        v-else-if="pipe_task_type == 'lookup'"
      />
      <PipeDocumentTaskExecute
        :task="pipe_task"
        :is-editing.sync="is_task_editing"
        :is-save-allowed.sync="is_task_save_allowed"
        @save-click="updateTask"
        v-else-if="pipe_task_type == 'execute'"
      />
      <div
        class="f6 fw4 lh-copy moon-gray"
        v-else
      >
        <em>(Unknown task)</em>
      </div>
    </div>

    <!-- description -->
    <div
      class="pipe-section pipe-editable"
      :class="{
        'o-40 no-pointer-events no-select': is_task_editing,
        'is-editing': is_addon_editing
      }"
    >
      <div class="flex flex-row items-center pb2 bb b--black-10 pipe-section-title">
        <div class="flex-fill f4 fw6 lh-title">Documentation</div>
        <div class="flex-none">
          <el-button
            style="padding: 0"
            type="text"
            @click="is_addon_editing = true"
            v-show="!is_addon_editing"
            v-require-rights:pipe.update
          >
            Edit
          </el-button>
        </div>
      </div>
      <PipeDocumentAddonEditor
        :pipe-eid="pipeEid"
        :is-editing.sync="is_addon_editing"
        @edit-click="is_addon_editing = true"
        @save-click="updateAddOnSettings"
      >
        <h3 slot="title">Description</h3>
        <span slot="empty"><em>(No syntax)</em></span>
      </PipeDocumentAddonEditor>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import LabelSwitch from '@/components/LabelSwitch'
  import PipeDocumentAddonEditor from '@/components/PipeDocumentAddonEditor'
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
      },
      showTestButton: {
        type: Boolean,
        default: true
      }
    },
    components: {
      Spinner,
      LabelSwitch,
      PipeDocumentAddonEditor,
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
        is_task_save_allowed: true, // we removed validation for now, so always allow tasks to be saved
        is_task_editing: false,
        is_addon_editing: false,
        is_notes_editing: false,
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
      updateTask(new_task, old_task) {
        var attrs = {
          task: {
            op: 'sequence',
            items: [new_task]
          }
        }
        this.savePipe(attrs)
      },
      updateAddOnSettings(new_attrs, old_pipe) {
        this.savePipe(new_attrs)
        this.is_addon_editing = false
      },
      onEditClick() {
        this.$emit('edit-click', this.pipe)
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .pipe-header
    margin-bottom: 48px

  .pipe-section
    margin-top: 48px
    margin-bottom: 24px

  .pipe-section-title
    margin-bottom: 20px

  .pipe-editable
    padding: 0
    transition: all 0.15s ease
    &.is-editing
      padding: 24px
      position: relative
      border-radius: 3px
      box-shadow: 0 0 0 1px rgba(64, 158, 255, 1), 0 0 0 5px rgba(64, 158, 255, 0.4)
</style>
