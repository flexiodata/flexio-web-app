<template>
  <div :value="value" @input="onChange">
    <BuilderList
      class="mw8"
      :container-id="containerId"
      :show-insert-buttons="true"
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
      onChange(value) {
        this.$emit('input', value)
      },
      updateFromTask(task) {
        var prompts = []
        _.each(task.items, (t) => {
          var item = _.find(builder_items, (bi) => {
            return _.get(bi, 'task.op') == t.op
          })

          if (item) {
            prompts = prompts.concat(item.prompts)
          }
        })

        this.$store.commit('builder/INIT_DEF', { prompts })
      }
    }
  }
</script>
