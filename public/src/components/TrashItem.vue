<template>
  <connection-item
    :item="item"
    :index="index"
    v-if="is_connection"
  ></connection-item>
  <pipe-item
    :item="item"
    :index="index"
    v-else-if="is_pipe"
  ></pipe-item>
  <div v-else>Invalid trash item</div>
</template>

<script>
  import { OBJECT_TYPE_CONNECTION, OBJECT_TYPE_PIPE } from '../constants/object-type'
  import { OBJECT_STATUS_AVAILABLE } from '../constants/object-status'
  import ConnectionItem from './ConnectionItem.vue'
  import PipeItem from './PipeItem.vue'

  export default {
    props: {
      'item': {
        type: Object,
        required: true
      },
      'index': {
        type: Number
      }
    },
    components: {
      ConnectionItem,
      PipeItem
    },
    computed: {
      eid_type() {
        return _.get(this.item, 'eid_type', '')
      },
      is_pipe() {
        return this.eid_type === OBJECT_TYPE_PIPE
      },
      is_connection() {
        return this.eid_type === OBJECT_TYPE_CONNECTION
      }
    },
    methods: {
      restorePipe() {
        var attrs = {
          eid_status: OBJECT_STATUS_AVAILABLE
        }

        this.$store.dispatch('updatePipe', { eid: this.item.eid, attrs })
      },
      deletePipe() {
        this.$store.dispatch('deletePipe', { attrs: this.item })
      },
      onDropdownItemClick(menu_item) {
        switch (menu_item.id)
        {
          case 'restore': return this.restorePipe()
          case 'delete':  return this.deletePipe()
        }
      }
    }
  }
</script>

<style lang="less" scoped>
  .css-trash-item {
    border-color: rgba(34, 36, 38, 0.15);

    &:hover {
      background-color: rgba(0,0,0,0.05);
      border-color: rgba(0,0,0,0.2);
    }
  }
</style>
