<template>
  <div>
    <BuilderList
      :items="prompts"
      :container-id="containerId"
      :active-item-idx.sync="active_prompt_idx"
      :active-item-cls="'css-active-blue'"
      :show-insert-buttons="active_prompt_idx == -1"
      :show-edit-buttons="false"
      :show-numbers="false"
      :show-content-border="false"
      @task-chooser-select-task="selectNewTask"
      @insert-step="insertStep"
      @delete-step="deleteStep"
      @item-error-change="itemErrorChange"
      @item-change="itemChange"
      @item-cancel="itemCancel"
      @item-save="itemSave"
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
  import * as ops from '../constants/task-op'
  import * as task_info from '../constants/task-info'
  import builder_defs from '../data/builder'
  import BuilderList from '@comp/BuilderList'
  import PipeBuilderEmptyItem from '@comp/PipeBuilderEmptyItem'

  export default {
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
          var def = _.find(builder_defs, (bi) => {
            return _.get(bi, 'task.op') == task.op
          })
          prompt = _.get(def, 'prompt', null)

          // TODO: use an execute task under the hood, but allow for a custom UI for the lookup task
          if (task.op == 'lookup' || (task.op == 'execute' && task.real_op == 'lookup')) {
            prompt = {
              element: 'task-lookup',
              title: 'Lookup'
            }

            task.op = 'execute'
            task.real_op = 'lookup'
            task.code = ''
            task.lang = 'python'
          }
        }

        // make sure we don't overwrite any objects
        prompt = _.cloneDeep(prompt)

        // if we couldn't find a matching task builder definition
        // show a basic JSON task editor
        if (_.isNil(prompt)) {
          var task = _.omit(task, ['eid'])
          prompt = {
            element: 'task-json-editor',
            value: task
          }
        } else if (prompt.element.indexOf('task-') != -1) {
          var task = _.omit(task, ['eid'])

          // probably should be thought out better and not just target title...
          var ttitle = _.get(task, 'title', '')
          if (ttitle.length > 0) {
            prompt.title = ttitle
          }

          prompt = _.assign({}, prompt, { form_values: task })
        } else if (prompt.element == 'form') {
          // for form builder items, assign the form item value by finding it in the task object
          prompt.form_items = _.map(prompt.form_items, fi => {
            if (!fi.variable || !_.has(task, fi.variable)) {
              return fi
            }

            return _.assign(fi, {
              value: _.get(task, fi.variable, '')
            })
          })

          // now set the form values from the form items
          var form_values = {}
          _.each(prompt.form_items, fi => {
            if (fi.variable) {
              _.set(form_values, fi.variable, fi.value)
            }
          })

          prompt = _.assign({}, prompt, { form_values })
        }

        // associate prompt with task
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

        // map existing tasks in model to prompts
        _.each(task.items, (t, task_idx) => {
          // create prompt from task
          prompts.push(this.promptFromTask(t, task_idx))

          // store task internally
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
        this.$store.track('Added ' + _.startCase(item.op) + ' Task')
      },
      insertStep(idx, task) {
        var items = _.get(this.value, 'items', [])
        items = _.cloneDeep(items)
        if (task) {
          items.splice(idx, 0, task)
          this.$store.track('Added ' + _.startCase(task.op) + ' Task')
        } else {
          items.splice(idx, 0, { op: '' })
          this.$store.track('Clicked Insert Task Button')
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
          this.$store.track('Added Task (Canceled)')
        }

        this.is_editing = false
        this.is_inserting = false
        this.active_prompt_idx = -1
        this.$emit('cancel')
      },
      itemSave() {
        if (this.is_inserting) {
          this.$store.track('Added Task (Saved)')
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
