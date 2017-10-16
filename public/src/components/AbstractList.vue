<template>
  <div>
    <component
      v-for="(item, key) in items"
      :is="itemComponent"
      :key="key"
      :item="item"
      :selected-item="selected_item"
      v-bind="itemOptions"
      @activate="onItemActivate"
      @edit="onItemEdit"
      @delete="onItemDelete"
    />
  </div>
</template>

<script>
  import AbstractConnectionChooserItem from './AbstractConnectionChooserItem.vue'

  export default {
    props: {
      'layout': {
        type: String,
        default: 'list' // 'list' or 'grid'
      },
      'show-selection': {
        type: Boolean,
        default: false
      },
      'items': {
        type: Array,
        default: () => { return [] }
      },
      'item-component': {
        type: String,
        required: true
      },
      'item-options': {
        type: Object,
        default: () => { return {} }
      },
      'auto-select-item': {
        type: [Boolean, Object, Function], // true/false or object/function for use with _.find()
        default: false
      }
    },
    data() {
      return {
        selected_item: {}
      }
    },
    components: {
      AbstractConnectionChooserItem
    },
    mounted() {
      if (this.autoSelectItem !== false)
        this.tryAutoSelectItem()
    },
    methods: {
      tryAutoSelectItem() {
        if (this.items.length == 0)
        {
          setTimeout(() => { this.tryAutoSelectItem() }, 500)
        }
         else
        {
          if (this.autoSelectItem === true)
            this.onItemActivate(_.first(this.items))
             else
            this.onItemActivate(_.find(this.items, this.autoSelectItem))
        }
      },
      onItemActivate(item) {
        this.selected_item = item
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
