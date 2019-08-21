<template>
  <div :class="disabled ? 'o-40 no-pointer-events': ''">
    <PipeListItem
      v-for="(item, key) in items"
      :key="key"
      :item="item"
      :selected-item="selectedItem"
      @activate="onItemActivate"
      @delete="onItemDelete"
      v-bind="$attrs"
    />
  </div>
</template>

<script>
  import PipeListItem from '@/components/PipeListItem'

  export default {
    inheritAttrs: false,
    props: {
      items: {
        type: Array,
        default: () => []
      },
      selectedItem: {
        type: Object,
        default: () => {}
      },
      disabled: {
        type: Boolean,
        default: false
      }
    },
    components: {
      PipeListItem
    },
    methods: {
      onItemActivate(item) {
        this.$emit('item-activate', item)
      },
      onItemDelete(item) {
        this.$emit('item-delete', item)
      }
    }
  }
</script>
