<template>
  <div :value="value">
    <BuilderList
      :container-id="containerId"
      :show-insert-buttons="true"
      @insertStep="insertStep"
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
    methods: {
      updateFromTask(task) {
        var pipe = { op: 'sequence', items: [] } // this is really the task object
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
            pipe.items = pipe.items.concat(item.task)
            prompts = prompts.concat(item.prompts)
          }
        })

        this.$store.commit('builder/SET_MODE', 'build')
        this.$store.commit('builder/INIT_DEF', { prompts, pipe })
      },
      insertStep(idx) {
        var items = _.cloneDeep(this.value.items)
        items.splice(idx, 0, { op: '' })
        this.$emit('input', { op: 'sequence', items })
        this.$nextTick(() => {
          this.$store.commit('builder/SET_ACTIVE_ITEM', idx)
        })
      }
    }
  }
</script>
