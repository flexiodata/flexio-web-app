<template>
  <div>
    <KeypairItem
      :item="header"
      :is-static="true"
      v-if="header !== false"
    />
    <KeypairItem
      v-for="(item, index) in items"
      :key="index"
      :item="item"
      :index="index"
      :count="items.length"
      @change="onItemChange"
      @delete="onItemDelete"
    />
  </div>
</template>

<script>
  import KeypairItem from './KeypairItem.vue'

  export default {
    props: {
      header: {
        type: [Boolean, Object],
        default: false
      },
      items: {
        type: Array,
        required: true
      }
    },
    components: {
      KeypairItem
    },
    methods: {
      onItemChange(item, index) {
        this.$emit('item-change', item, index)
      },
      onItemDelete(item, index) {
        this.$emit('item-delete', item, index)
      }
    }
  }
</script>
