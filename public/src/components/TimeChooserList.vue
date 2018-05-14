<template>
  <div>
    <TimeChooserItem
      v-for="(time, index) in times"
      :item="time"
      :index="index"
      @change="onItemChange"
      @delete="onItemDelete"
    />
  </div>
</template>

<script>
  import TimeChooserItem from './TimeChooserItem.vue'

  export default {
    props: ['times'],
    components: {
      TimeChooserItem
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
