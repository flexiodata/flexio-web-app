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
      :class="{ [item.id]: true }"
    />
  </div>
</template>

<script>
  import KeypairItem from './KeypairItem.vue'

  const newKeypairItem = (key, val) => {
    key = _.defaultTo(key, '')
    val = _.defaultTo(val, '')

    return {
      key,
      val
    }
  }

  export default {
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
    data() {
      return {
        is_dirty: false
      }
    },
    computed: {
      items: {
        get() {
          // convert object to array of keypairs
          var items = []
          var idx = 0

          _.each(this.value, (val, key) => {
            if (key && key.length > 0) {
              items.push(newKeypairItem(key, val))
            }
          })

          items.push(newKeypairItem())

          return items
        },
        set(value) {
          // convert array to object
          var obj = {}

          _.each(value, (item) => {
            if (item.key && item.key.length > 0) {
              obj[item.key] = item.val
            }
          })

          this.$emit('input', obj)
        }
      }
    },
    methods: {
      onItemChange(item, index) {
        var items = _.cloneDeep(this.items)

        if (index == _.size(items) - 1) {
          items = [].concat(items).concat(newKeypairItem())
        }

        _.set(items, '[' + index + ']', item)

        this.$emit('item-change', item, index)
        this.items = items
      },
      onItemDelete(item, index) {
        var items = _.cloneDeep(this.items)
        _.pullAt(items, [index])

        this.$nextTick(() => {
          this.$emit('item-delete', item, index)
          this.items = items
        })
      }
    }
  }
</script>
