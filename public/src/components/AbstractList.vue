<template>
  <div :class="disabled ? 'o-40 no-pointer-events': ''">
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
      },
      'disabled': {
        type: Boolean,
        default: false
      }
    },
    watch: {
      items(val, old_val) {
        if (!_.find(val, this.selected_item) && this.selected_idx >= 0)
        {
          if (this.selected_idx >= this.items.length)
            this.selected_idx--

          this.selected_item = _.get(val, '['+this.selected_idx+']', {})
          this.syncIndex()
        }
      }
    },
    data() {
      return {
        selected_item: {},
        selected_idx: -1
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
      getSelectedItem() {
        return this.selected_item
      },
      syncIndex() {
        this.selected_idx = _.findIndex(this.items, (item) => {
          return _.isEqual(this.selected_item, item)
        })
      },
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
        this.syncIndex()
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
