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
    computed: {
      ...mapState({
        orig_pipe: state => state.pipe.orig_pipe
      })
    },
    methods: {
      updateFromTask(task) {
        var prompts = []
        _.each(task.items, (t) => {
          var item

          if (t.op == '') {
            item = { task: { op: '' }, prompts: [{ element: 'task-chooser' }] }
          } else {
            item = _.find(builder_items, (bi) => {
              return _.get(bi, 'task.op') == t.op
            })
          }

          if (item) {
            prompts = prompts.concat(item.prompts)
          }
        })

        this.$store.commit('builder/SET_MODE', 'build')
        this.$store.commit('builder/INIT_DEF', { prompts })
      },
      insertStep(idx) {
        var items = _.cloneDeep(this.value.items)
        items.splice(idx, 0, { op: '' })
        this.$emit('input', { op: 'sequence', items })
        this.$nextTick(() => {
          this.$store.commit('builder/SET_ACTIVE_ITEM', idx)
        })
      },
      itemChange(prompt, idx) {
        // TODO: do we want to handle this here?
      },
      itemCancel(prompt_idx) {
        var value = _.get(this.orig_pipe, 'task', { op: 'sequence', items: [] })
        this.$emit('input', value)
        this.$store.commit('builder/UNSET_ACTIVE_ITEM')
      },
      itemSave(prompt_idx) {
        this.$store.commit('builder/UNSET_ACTIVE_ITEM')
      }
    }
  }
</script>
