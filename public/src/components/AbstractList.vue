<template>
  <div>
    <component
      v-for="(item, key) in items"
      :is="itemComponent"
      :key="key"
      :item="item"
      v-bind="itemProps"
      @activate="onItemActivate"
    >
    </component>
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
    methods: {
      onItemActivate(item) {
        this.selected_item = item
        this.$emit('item-activate', item)
      }
    }
  }
</script>
