<template>
  <div>
    <BuilderItem
      :item="item"
      :index="index"
      :items="items"
      :active-item-idx="activeItemIdx"
      :key="item.id"
      @active-item-change="onActiveItemChange"
      v-bind="$attrs"
      v-on="$listeners"
      v-for="(item, index) in items"
    />
  </div>
</template>

<script>
  import BuilderItem from './BuilderItem.vue'

  export default {
    inheritAttrs: false,
    props: {
      items: {
        type: Array,
        required: true
      },
      activeItemIdx: {
        type: Number
      },
      containerId: {
        type: String
      }
    },
    components: {
      BuilderItem
    },
    watch: {
      activeItemIdx: {
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
      active_item() {
        return _.get(this.items, '[' + this.activeItemIdx + ']', null)
      }
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
                offset: this.activeItemIdx == 0 ? -100 : -32
            })
          }, 10)
        }
      },
      scrollToActive() {
        if (!this.do_initial_scroll) {
          return
        }

        var item_id = _.get(this.active_item, 'id', null)
        this.scrollToItem(item_id)
      },
      onActiveItemChange(index) {
        this.$emit('update:activeItemIdx', index)
      }
    }
  }
</script>
