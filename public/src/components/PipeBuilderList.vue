<template>
  <div :value="value">
    <BuilderList
      :container-id="containerId"
      :show-insert-buttons="true"
      @insert-step="insertStep"
      @item-change="itemChange"
      @item-cancel="itemCancel"
      @item-save="itemSave"
      v-bind="$attrs"
    />
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import builder_items from '../data/builder'
  import BuilderList from './BuilderList.vue'

  export default {
    inheritAttrs: false,
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
      BuilderList
    },
    watch: {
      value: {
        handler: 'updateFromTask',
        immediate: true
      }
    },
    data() {
      return {
        is_editing: false,
        task_items: []
      }
    },
    computed: {
      ...mapState({
        builder_prompts: state => state.builder.prompts,
        orig_pipe: state => state.pipe.orig_pipe
      })
    },
    methods: {
      updateFromTask(task) {
        var tasks = []
        var prompts = []

        if (this.is_editing) {
          return
        }

        _.each(task.items, (t, task_idx) => {
          var item

          if (t.op == '') {
            item = { task: { op: '' }, prompts: [{ element: 'task-chooser' }] }
          } else {
            item = _.find(builder_items, (bi) => {
              return _.get(bi, 'task.op') == t.op
            })
          }

          if (item) {
            tasks.push(_.cloneDeep(t))

            // associate prompts with tasks
            _.each(item.prompts, (p) => {
              prompts.push(_.assign({ task_idx }, p))
            })
          }
        })

        this.$store.commit('builder/SET_MODE', 'build')
        this.$store.commit('builder/INIT_DEF', { prompts })
        this.task_items = tasks
      },
      insertStep(idx) {
        var items = _.cloneDeep(this.value.items)
        items.splice(idx, 0, { op: '' })
        this.$emit('input', { op: 'sequence', items })
        this.$nextTick(() => {
          this.$store.commit('builder/SET_ACTIVE_ITEM', idx)
        })
      },
      itemChange(prompt_values, prompt_idx) {
        var p = this.builder_prompts[prompt_idx]
        if (p) {
          var t = _.get(this.task_items, '['+p.task_idx+ ']', null)
          if (t) {
            t = _.assign(t, prompt_values)
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
        this.$emit('input', value)
        this.$store.commit('builder/UNSET_ACTIVE_ITEM')
      },
      itemSave() {
        this.is_editing = false
        this.$store.commit('builder/UNSET_ACTIVE_ITEM')
      }
    }
  }
</script>
