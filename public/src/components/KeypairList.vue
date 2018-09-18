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
        type: Array,
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
    computed: {
      items: {
        get() {
          return [].concat(this.value)
        },
        set() {
          // do nothing
        }
      }
    },
    methods: {
      onItemChange(item, index) {
        var items = _.cloneDeep(this.value)

        if (index == _.size(items) - 1) {
          items = [].concat(items).concat(newKeypairItem())
        }

        var arr = [].concat(items)
        arr[index] = _.assign({}, item)
        items = [].concat(arr)

        this.$emit('item-change', item, index)
        this.$emit('input', items)
      },
      onItemDelete(item, index) {
        var tmp = _.cloneDeep(this.value)
        _.pullAt(tmp, [index])

        this.$nextTick(() => {
          var items = [].concat(tmp)
          this.$emit('item-delete', item, index)
          this.$emit('input', items)
        })
      }
    }
  }
</script>
