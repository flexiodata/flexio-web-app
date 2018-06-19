<template>
  <div :value="value">
    <BuilderList
      :items="prompts"
      :container-id="containerId"
      :active-item-idx.sync="active_prompt_idx"
      :show-insert-buttons="true"
      @task-chooser-item-click="selectTask"
      @insert-step="insertStep"
      @item-change="itemChange"
      @item-cancel="itemCancel"
      @item-save="itemSave"
      v-if="prompts.length > 0"
    />
    <div
      v-else
    >
      <p class="ttu fw6 f7 moon-gray">Choose a starting connection</p>
      <ConnectionChooserList
        class="mb3 overflow-auto"
        style="max-height: 277px"
        :show-selection-checkmark="true"
      />
      <div class="mt3">
        <el-button
          class="ttu b"
        >
          Set up a new connection
        </el-button>
      </div>
      <p class="mv4 ttu fw6 f7 moon-gray">&mdash; or start with one of the following tasks &mdash;</p>
      <BuilderItemTaskChooser title="Choose a task" />
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import builder_defs from '../data/builder'
  import BuilderList from './BuilderList.vue'
  import BuilderItemTaskChooser from './BuilderItemTaskChooser.vue'
  import ConnectionChooserList from './ConnectionChooserList.vue'

  export default {
    props: {
      value: {
        type: Object,
        required: true
      },
      containerId: {
        type: String
      }
    },
    components: {
      BuilderList,
      BuilderItemTaskChooser,
      ConnectionChooserList
    },
    watch: {
      value: {
        handler: 'initFromPipeTask',
        immediate: true
      }
    },
    data() {
      return {
        is_editing: false,
        task_items: [],
        prompts: [],
        active_prompt_idx: -1
      }
    },
    computed: {
      ...mapState({
        orig_pipe: state => state.pipe.orig_pipe
      })
    },
    methods: {
      initFromPipeTask(task) {
        var tasks = []
        var prompts = []

        if (this.is_editing) {
          return
        }

        // map existing tasks in model to prompts
        _.each(task.items, (t, task_idx) => {
          var prompt

          if (t.op == '') {
            prompt = {
              element: 'task-chooser'
            }
          } else {
            var def = _.find(builder_defs, (bi) => {
              return _.get(bi, 'task.op') == t.op
            })
            prompt = _.get(def, 'prompt', null)
          }

          // if we couldn't find a matching task builder definition
          // show a basic JSON task editor
          if (_.isNil(prompt)) {
            var task = _.cloneDeep(_.omit(t, ['eid']))
            prompt = {
              element: 'task-json-editor',
              value: JSON.stringify(task, null, 2)
            }
          }

          // for form builder items, assign the form item value by finding it in the task object
          if (prompt.element == 'form') {
            prompt.form_items = _.map(prompt.form_items, fi => {
              if (!fi.variable) {
                return fi
              }

              return _.assign(fi, {
                value: _.get(t, fi.variable, '')
              })
            })
          }

          // now set the form values from the form items
          if (prompt.element == 'form') {
            var form_values = {}
            _.each(prompt.form_items, fi => {
              if (fi.variable) {
                _.set(form_values, fi.variable, fi.value)
              }
            })

            prompt = _.assign({}, prompt, { form_values })
          }

          // associate prompt with task
          prompt = _.assign({}, prompt, { task_idx })
          prompt = _.cloneDeep(prompt)

          prompts.push(prompt)

          // store task internally
          tasks.push(_.cloneDeep(t))
        })

        // make sure we're not mutating anything in the Vuex store
        prompts = _.cloneDeep(prompts)

        this.task_items = [].concat(tasks)
        this.prompts = [].concat(prompts)
        this.is_editing = false
      },
      selectTask(item) {
        this.$message({
          message: '"' + item.op + '" clicked!',
          type: 'info'
        })
      },
      insertStep(idx) {
        var items = _.cloneDeep(this.value.items)
        items.splice(idx, 0, { op: '' })
        this.is_editing = false
        this.$emit('input', { op: 'sequence', items })
        this.$nextTick(() => {
          this.active_prompt_idx = idx
        })
      },
      itemChange(values, index) {
        var p = this.prompts[index]
        if (p) {
          var t = _.get(this.task_items, '['+p.task_idx+ ']', null)
          if (t) {
            t = _.assign(t, values)
            var items = _.cloneDeep(this.task_items)
            _.set(items, '['+p.task_idx+ ']', t)
            this.task_items = [].concat(items)
            this.is_editing = true
            this.$emit('input', { op: 'sequence', items })
          }
        }
      },
      itemCancel() {
        var value = _.get(this.orig_pipe, 'task', { op: 'sequence', items: [] })
        this.is_editing = false
        this.active_prompt_idx = -1
        this.$emit('input', value)
      },
      itemSave() {
        this.is_editing = false
        this.active_prompt_idx = -1
        this.$emit('save')
      }
    }
  }
</script>
