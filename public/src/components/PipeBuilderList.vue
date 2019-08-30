<template>
  <div>
    <BuilderList
      :items="prompts"
      :container-id="containerId"
      :active-item-idx.sync="active_prompt_idx"
      :active-item-cls="'css-active-blue'"
      :show-numbers="false"
      :show-icons="false"
      :show-line="false"
      :show-edit-buttons="false"
      :show-delete-buttons="false"
      :show-insert-buttons="false"
      :show-content-border="false"
      @task-chooser-select-task="selectNewTask"
      @insert-step="insertStep"
      @delete-step="deleteStep"
      @item-error-change="itemErrorChange"
      @item-change="itemChange"
      @item-cancel="itemCancel"
      @item-save="itemSave"
      v-bind="$attrs"
      v-on="$listeners"
      v-if="prompts.length > 0"
    />
    <PipeBuilderEmptyItem
      @insert-step="insertStep"
      v-else
    />
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import * as ops from '@/constants/task-op'
  import * as task_info from '@/constants/task-info'
  import builder_defs from '@/data/builder'
  import BuilderList from '@/components/BuilderList'
  import PipeBuilderEmptyItem from '@/components/PipeBuilderEmptyItem'

  export default {
    inheritAttrs: false,
    props: {
      value: {
        type: Object,
        required: true
      },
      containerId: {
        type: String
      },
      activeItemIdx: {
        type: Number
      },
      hasErrors: {
        type: Boolean,
        required: true
      }
    },
    components: {
      BuilderList,
      PipeBuilderEmptyItem
    },
    watch: {
      value: {
        handler: 'initFromPipeTask',
        immediate: true,
        deep: true
      },
      has_errors: {
        handler: 'onErrorChange'
      },
      active_prompt_idx: {
        handler: 'onActivePromptIdxChange'
      },
      activeItemIdx(val) {
        this.active_prompt_idx = val
      }
    },
    data() {
      return {
        is_inited: false,
        is_editing: false,
        is_inserting: false,
        has_errors: false,
        task_info,
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
      revert() {
        this.is_editing = false
        this.initFromPipeTask(this.value)
      },
      getIconFromTask(task) {
        var found_task = _.find(this.task_info, { op: _.get(task, 'op') })
        if (found_task) {
          return { name: found_task.icon, bg_color_cls: found_task.bg_color }
        }
        return {}
      },
      promptFromTask(task, task_idx) {
        var prompt

        if (task.op == '') {
          prompt = {
            element: 'task-chooser'
          }
        } else {
          var def = _.find(builder_defs, d => {
            return _.get(d, 'task.op') == task.op
          })
          prompt = _.get(def, 'prompt', null)

          /*
          // This was a clever way to allow custom tasks via Python -- keep it around for reference
          //
          // TODO: use an execute step under the hood, but allow for a custom UI for the lookup step
          if (task.op == 'lookup' || (task.op == 'execute' && task.real_op == 'lookup')) {
            prompt = {
              element: 'task-lookup',
              title: 'Lookup'
            }

            task = _.cloneDeep(task)
            task.op = 'execute'
            task.real_op = 'lookup'
            task.lang = 'python'
          }
          */
        }

        // make sure we don't overwrite any objects
        prompt = _.cloneDeep(prompt)
        task = _.cloneDeep(task)

        if (_.isNil(prompt)) {
          // SCENARIO 1: we couldn't find a matching step builder definition;
          //             show a basic JSON step editor
          prompt = {
            element: 'task-json-editor',
            value: task
          }
        } else if (prompt.element.indexOf('task-') != -1) {
          // SCENARIO 2: we're rendering a pipe step; show the corresponding step UI

          // probably should be thought out better and not just target title...
          var ttitle = _.get(task, 'title', '')
          if (ttitle.length > 0) {
            prompt.title = ttitle
          }

          prompt = _.assign({}, prompt, { form_values: task })
        } else if (prompt.element == 'form') {
          // SCENARIO 3: we're rendering a form step; show the form UI

          // for form builder items, assign the form item value by finding it in the step object
          prompt.form_items = _.map(prompt.form_items, f => {
            if (!f.variable || !_.has(task, f.variable)) {
              return f
            }

            return _.assign(f, {
              value: _.get(task, f.variable, '')
            })
          })

          // now set the form values from the form items
          var form_values = {}
          _.each(prompt.form_items, f => {
            if (f.variable) {
              _.set(form_values, f.variable, f.value)
            }
          })

          prompt = _.assign({}, prompt, { form_values })
        }

        // associate prompt with step
        var icon = this.getIconFromTask(task)
        prompt = _.assign({}, prompt, { task_idx, icon })
        prompt = _.cloneDeep(prompt)

        return prompt
      },
      initFromPipeTask(task) {
        if (this.is_editing) {
          return
        }

        // do this so that we don't fire a bunch of item change events when the items are re-rendered
        this.is_inited = false

        var tasks = []
        var prompts = []

        // map existing steps in model to prompts
        _.each(task.items, (t, task_idx) => {
          // create prompt from step
          prompts.push(this.promptFromTask(t, task_idx))

          // store step internally
          tasks.push(_.cloneDeep(t))
        })

        this.task_items = [].concat(tasks)
        this.prompts = [].concat(prompts)
        this.has_errors = false
        this.is_editing = false

        // NOTE: we cannot use $nextTick here because this call happens multiple times
        setTimeout(() => { this.is_inited = true }, 1)
      },
      selectNewTask(item, index) {
        var items = this.task_items
        _.set(items, '[' + index + ']', { op: item.op })
        this.task_items = [].concat(items)

        var prompts = this.prompts
        _.set(prompts, '[' + index + ']', this.promptFromTask(item, index))
        this.prompts = [].concat(prompts)

        this.$emit('input', { op: 'sequence', items })
        this.$store.track('Added ' + _.startCase(item.op) + ' Step')
      },
      insertStep(idx, task) {
        var items = _.get(this.value, 'items', [])
        items = _.cloneDeep(items)
        if (task) {
          items.splice(idx, 0, task)
          this.$store.track('Added ' + _.startCase(task.op) + ' Step')
        } else {
          items.splice(idx, 0, { op: '' })
          this.$store.track('Clicked Insert Step Button')
        }
        this.is_editing = false
        this.is_inserting = true
        this.$emit('input', { op: 'sequence', items })
        this.$nextTick(() => {
          this.active_prompt_idx = idx
        })
      },
      deleteStep(idx) {
        var items = _.cloneDeep(this.value.items)
        items.splice(idx, 1)
        this.is_editing = false
        this.$emit('input', { op: 'sequence', items })
        this.$nextTick(() => {
          this.active_prompt_idx = -1
          this.$emit('save')
        })
      },
      itemErrorChange(has_errors, index) {
        this.has_errors = has_errors
      },
      itemChange(values, index) {
        if (!this.is_inited) {
          return
        }

        var p = this.prompts[index]
        if (p) {
          var t = _.get(this.task_items, '['+p.task_idx+ ']', null)
          if (t) {
            var items = _.cloneDeep(this.task_items)
            _.set(items, '['+p.task_idx+ ']', values)
            this.task_items = [].concat(items)
            this.is_editing = true
            this.$emit('input', { op: 'sequence', items })
          }
        }
      },
      itemCancel() {
        if (this.is_inserting) {
          this.$store.track('Added Step (Canceled)')
        }

        this.is_editing = false
        this.is_inserting = false
        this.active_prompt_idx = -1
        this.$emit('cancel')
      },
      itemSave() {
        if (this.is_inserting) {
          this.$store.track('Added Step (Saved)')
        }

        this.is_editing = false
        this.is_inserting = false
        this.active_prompt_idx = -1
        this.$emit('save')
      },
      onErrorChange() {
        this.$emit('update:hasErrors', this.has_errors)
      },
      onActivePromptIdxChange() {
        this.$emit('update:activeItemIdx', this.active_prompt_idx)
      }
    }
  }
</script>
