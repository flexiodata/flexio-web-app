<template>
  <div v-if="!force_render">
    <KeypairItem
      :item="header"
      :is-static="true"
      v-if="header !== false"
    />
    <KeypairItem
      :item="item"
      :index="index"
      :count="items.length"
      @change="onItemChange"
      @delete="onItemDelete"
      v-bind="$attrs"
      v-for="(item, index) in items"
    />
  </div>
</template>

<script>
  import KeypairItem from '@/components/KeypairItem'

  const newKeypairItem = (key, val) => {
    key = _.defaultTo(key, '')
    val = _.defaultTo(val, '')

    return {
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
    data() {
      return {
        force_render: false
      }
    },
    computed: {
      items: {
        get() {
          var items = this.toArray(this.value)
          items.push(newKeypairItem())
          return items
        },
        set(value) {
          // do nothing
        }
      }
    },
    methods: {
      forceRender() {
        this.force_render = true
        this.$nextTick(() => { this.force_render = false })
      },
      toArray(obj) {
        // convert object to array of keypairs
        var items = []

        _.each(obj, (val, key) => {
          if (key && key.length > 0) {
            items.push(newKeypairItem(key, val))
          }
        })

        return items
      },
      toObject(arr) {
        // convert array to object
        var obj = {}

        _.each(arr, (item) => {
          if (item.key && item.key.length > 0) {
            obj[item.key] = item.val
          }
        })

        return obj
      },
      onItemChange(item, index) {
        var items = _.cloneDeep(this.items)

        if (index == _.size(items) - 1) {
          items = [].concat(items).concat(newKeypairItem())
        }

        _.assign(items[index], item)

        this.$emit('input', this.toObject(items))
        this.$emit('item-change', item, index)
      },
      onItemDelete(item, index) {
        var items = _.cloneDeep(this.items)
        _.pullAt(items, [index])

        this.$emit('input', this.toObject(items))
        this.$emit('item-delete', item, index)

        this.forceRender()
      }
    }
  }
</script>
