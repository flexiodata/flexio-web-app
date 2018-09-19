<template>
  <div>
    <KeypairItem
      :item="header"
      :is-static="true"
      v-if="header !== false"
    />
    <KeypairItem
      v-for="(item, index) in items"
      :key="item.id"
      :item="item"
      :index="index"
      :count="items.length"
      @change="onItemChange"
      @delete="onItemDelete"
      v-bind="$attrs"
    />
  </div>
</template>

<script>
  import KeypairItem from './KeypairItem.vue'

  var idx = 0

  const newKeypairItem = (key, val) => {
    key = _.defaultTo(key, '')
    val = _.defaultTo(val, '')

    return {
      id: 'keypair-item-' + (++idx),
      key,
      val
    }
  }

  export default {
    inheritAttrs: false,
    props: {
      value: {
        type: Object,
        required: true
      },
      header: {
        type: [Boolean, Object],
        default: false
      }
    },
    components: {
      KeypairItem
    },
    watch: {
      value: {
        handler: 'initSelf',
        immediate: true,
        deep: true
      }
    },
    data() {
      return {
        items: [],
        is_editing: false
      }
    },
    methods: {
      initSelf() {
        if (this.is_editing) {
          return
        }

        // convert object to array of keypairs
        var items = []
        var idx = 0

        _.each(this.value, (val, key) => {
          if (key && key.length > 0) {
            items.push(newKeypairItem(key, val))
          }
        })

        items.push(newKeypairItem())

        this.items = items
      },
      emitChange() {
        this.is_editing = true

        // convert array to object
        var obj = {}

        _.each(this.items, (item) => {
          if (item.key && item.key.length > 0) {
            obj[item.key] = item.val
          }
        })

        this.$emit('input', obj)
      },
      revert() {
        this.is_editing = false
        this.$nextTick(() => { this.initSelf() })
      },
      onItemChange(item, index) {
        var items = _.cloneDeep(this.items)

        if (index == _.size(items) - 1) {
          items = [].concat(items).concat(newKeypairItem())
        }

        _.assign(items[index], item)

        this.items = items
        this.emitChange()
        this.$emit('item-change', item, index)
      },
      onItemDelete(item, index) {
        var items = _.cloneDeep(this.items)
        _.pullAt(items, [index])

        this.$nextTick(() => {
          this.items = items
          this.emitChange()
          this.$emit('item-delete', item, index)
        })
      }
    }
  }
</script>
