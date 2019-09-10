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

    <el-collapse
      class="el-collapse--plain el-collapse--arrow-left"
      v-model="expanded_sections"
    >
      <!-- definiton -->
      <el-collapse-item
        name="definiton"
        class="pipe-section pipe-section-editable"
        :class="{
          'o-40 no-pointer-events no-select': is_addon_editing,
          'is-editing': is_task_editing
        }"
      >
        <div
          class="flex flex-row items-center pipe-section-title"
          slot="title"
        >
          <div class="flex-fill f4 fw6 lh-title">Definition</div>
          <div class="flex-none">
            <el-button
              style="padding: 0"
              type="text"
              @click.stop="onEditTaskClick"
              v-show="!is_task_editing"
              v-require-rights:pipe.update
            >
              Edit
            </el-button>
          </div>
        </div>
        <div class="pipe-section-body">
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
      </el-collapse-item>

      <!-- description -->
      <el-collapse-item
        name="documentation"
        class="pipe-section pipe-section-editable"
        :class="{
          'o-40 no-pointer-events no-select': is_task_editing,
          'is-editing': is_addon_editing
        }"
      >
        <div
          class="flex flex-row items-center pipe-section-title"
          slot="title"
        >
          <div class="flex-fill f4 fw6 lh-title">Documentation</div>
          <div class="flex-none">
            <el-button
              style="padding: 0"
              type="text"
              @click.stop="onEditDocumentationClick"
              v-show="!is_addon_editing"
              v-require-rights:pipe.update
            >
              Edit
            </el-button>
          </div>
        </div>
        <div class="pipe-section-body">
          <PipeDocumentAddonEditor
            :pipe-eid="pipeEid"
            :is-editing.sync="is_addon_editing"
            @save-click="updateAddOnSettings"
          />
        </div>
      </el-collapse-item>
    </el-collapse>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import { TASK_OP_EXTRACT, TASK_OP_LOOKUP } from '@/constants/task-op'
  import { afterLast } from '@/utils'
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
        expanded_sections: ['definiton', 'documentation'],
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

        // if our pipe doesn't have any parameters and we're saving a lookup task,
        // save the lookup columns as the params
        if (new_task.op == TASK_OP_LOOKUP && this.pipe.params.length == 0) {
          attrs.params = _.map(new_task.lookup_keys, k => {
            return {
              name: k,
              type: 'string',
              description: `The \`${k}\` column`,
              required: true
            }
          })
        }

        // if our pipe doesn't have a descriptipon yet and we're
        // saving a lookup or extract task, generate a small default description
        if (this.pipe.description.length == 0) {
          if (new_task.op == TASK_OP_LOOKUP) {
            var fields = _.map(new_task.lookup_keys, k => `\`${k}\``)
            fields = fields.join(', ')

            var columns = _.map(new_task.return_columns, k => `\`${k}\``)
            columns = columns.join(', ')

            var filename = afterLast(new_task.path, '/')
            attrs.description = `Using the key fields ${fields}, return ${columns} from "${filename}".`
          } else if (new_task.op == TASK_OP_EXTRACT) {
            var filename = afterLast(new_task.path, '/')
            attrs.description = `Return the contents of "${filename}".`
          }
        }

        this.savePipe(attrs).then(response => {
          this.is_task_editing = false
        })
      },
      updateAddOnSettings(new_attrs, old_pipe) {
        this.savePipe(new_attrs).then(response => {
          this.is_addon_editing = false
        })
      },
      onEditTaskClick() {
        this.is_task_editing = true
        if (this.expanded_sections.indexOf('definiton') == -1) {
          this.expanded_sections.push('definiton')
        }
      },
      onEditDocumentationClick() {
        this.is_addon_editing = true
        if (this.expanded_sections.indexOf('documentation') == -1) {
          this.expanded_sections.push('documentation')
        }
      },
      onEditClick() {
        this.$emit('edit-click', this.pipe)
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .pipe-header
    margin-bottom: 24px

  .pipe-section
    margin-top: 24px
    margin-bottom: 24px
    padding-right: 24px

  .pipe-section-title
    transition: all .3s
    min-height: 48px
    padding-left: 10px
    position: relative
    left: 0

  .pipe-section-body
    transition: all .3s
    padding-left: 24px

  .pipe-section-editable
    transition: all .3s
    &.is-editing
      padding: 12px 24px 0 // compensate for `.el-collapse-item__content` bottom padding
      position: relative
      border-radius: 3px
      box-shadow: 0 0 0 1px rgba(64, 158, 255, 1), 0 0 0 5px rgba(64, 158, 255, 0.4)

      .pipe-section-title
        padding-left: 0
        left: -13px // compensate for hidden `el-collapse-item__arrow`

      .pipe-section-body
        padding-left: 0

</style>
