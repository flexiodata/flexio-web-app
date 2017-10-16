<template>
  <div>
    <component
      v-for="(item, key) in items"
      :is="itemComponent"
      :key="key"
      :item="item"
      v-bind="itemProps"
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
      'item-cls': {
        type: String,
        default: ''
      },
      'item-style': {
        type: String,
        default: ''
      },
      'item-selected-cls': {
        type: String,
        default: 'bg-light-gray'
      },
      'item-show-checkmark': {
        type: Boolean,
        default: false
      },
      'item-show-dropdown': {
        type: Boolean,
        default: false
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
    computed: {
      itemProps() {
        return _.assign({
          'item-selected': this.selected_item
        }, _.omit(this._props, ['items']))
      }
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
