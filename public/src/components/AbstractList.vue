<template>
  <div :class="disabled ? 'o-40 no-pointer-events': ''">
    <component
      v-for="(item, key) in items"
      :is="itemComponent"
      :key="key"
      :item="item"
      :selected-item="selectedItem"
      v-bind="itemOptions"
      @activate="onItemActivate"
      @edit="onItemEdit"
      @delete="onItemDelete"
    />
  </div>
</template>

<script>
  import AbstractConnectionChooserItem from '@/components/AbstractConnectionChooserItem'

  export default {
    props: {
      'layout': {
        type: String,
        default: 'list' // 'list' or 'grid'
      },
      'items': {
        type: Array,
        default: () => []
      },
      'item-component': {
        type: String,
        required: true
      },
      'item-options': {
        type: Object,
        default: () => {}
      },
      'auto-select-item': {
        type: [Boolean, Object, Function], // true/false or object/function for use with _.find()
        default: false
      },
      'selected-item': {
        type: Object,
        default: () => {}
      },
      'disabled': {
        type: Boolean,
        default: false
      }
    },
    components: {
      AbstractConnectionChooserItem
    },
    methods: {
      onItemActivate(item) {
        this.$emit('item-activate', item)
      },
      onItemEdit(item) {
        this.$emit('item-edit', item)
      },
      onItemDelete(item) {
        this.$emit('item-delete', item)
      }
    }
  }
</script>
