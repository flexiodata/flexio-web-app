<template>
  <div>
    <TimeChooserItem
      :item="time"
      :index="index"
      @change="onItemChange"
      @delete="onItemDelete"
      v-for="(time, index) in times"
    />
  </div>
</template>

<script>
  import TimeChooserItem from '@/components/TimeChooserItem'

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
