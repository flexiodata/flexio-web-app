<template>
  <div>
    <BuilderItem
      v-for="(item, index) in prompts"
      :item="item"
      :index="index"
      :key="item.id"
      :show-insert-buttons="false"
    />
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import BuilderItem from './BuilderItem.vue'

  export default {
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
    computed: {
      ...mapState({
        prompts: state => state.builder.prompts,
        active_prompt: state => state.builder.active_prompt,
        active_prompt_idx: state => state.builder.active_prompt_idx
      })
    },
    methods: {
      scrollToItem(item_id) {
        if (_.isString(item_id) && _.isString(this.containerId)) {
          setTimeout(() => {
            this.$scrollTo('#'+item_id, {
                container: '#'+this.containerId,
                duration: 400,
                easing: 'ease-out',
                offset: -64
            })
          }, 10)
        }
      },
      scrollToActive() {
        var item_id = _.get(this.active_prompt, 'id', null)
        this.scrollToItem(item_id)
      }
    }
  }
</script>
