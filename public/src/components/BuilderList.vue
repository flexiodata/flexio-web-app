<template>
  <div>
    <BuilderItem
      v-for="(item, index) in prompts"
      :item="item"
      :index="index"
      :key="item.id"
      v-bind="$attrs"
    />
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import BuilderItem from './BuilderItem.vue'

  export default {
    inheritAttrs: false,
    props: {
      containerId: {
        type: String
      }
    },
    components: {
      BuilderItem
    },
    watch: {
      active_prompt_idx: {
        handler: 'scrollToActive',
        immediate: true
      }
    },
    data() {
      return {
        do_initial_scroll: false
      }
    },
    computed: {
      ...mapState({
        prompts: state => state.builder.prompts,
        active_prompt: state => state.builder.active_prompt,
        active_prompt_idx: state => state.builder.active_prompt_idx
      })
    },
    mounted() {
      setTimeout(() => { this.do_initial_scroll = true }, 500)
    },
    methods: {
      scrollToItem(item_id) {
        if (_.isString(item_id) && _.isString(this.containerId)) {
          setTimeout(() => {
            this.$scrollTo('#'+item_id, {
                container: '#'+this.containerId,
                duration: 400,
                offset: -32
            })
          }, 10)
        }
      },
      scrollToActive() {
        if (!this.do_initial_scroll)
          return

        var item_id = _.get(this.active_prompt, 'id', null)
        this.scrollToItem(item_id)
      }
    }
  }
</script>
