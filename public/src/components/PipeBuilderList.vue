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
    mounted() {
      this.$store.commit('builder/SET_MODE', 'build')
    },
    methods: {
      updateFromTask(task) {
        var prompts = []
        _.each(task.items, (t) => {
          var item

          if (t.op == '') {
            item = { prompts: [{ element: 'task-chooser' }] }
          } else {
            item = _.find(builder_items, (bi) => {
              return _.get(bi, 'task.op') == t.op
            })
          }

          if (item) {
            prompts = prompts.concat(item.prompts)
          }
        })

        this.$store.commit('builder/INIT_DEF', { prompts })
      },
      insertStep(idx) {
        var items = _.cloneDeep(this.value.items)
        items.splice(idx, 0, { op: '' })
        this.$emit('input', { op: 'sequence', items })
      }
    }
  }
</script>
