<template>
  <pipe-item
    :item="item"
    :index="index"
    :is-trash="true"
    :menu-items="menu_items"
    @dropdown-item-click="onDropdownItemClick"
    v-if="is_pipe"
  ></pipe-item>
  <div v-else>Item is not a pipe. Only pipes can be added to the trash at this time.</div>
</template>

<script>
  import { OBJECT_TYPE_PIPE } from '../constants/object-type'
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
      PipeItem
    },
    data() {
      return {
        menu_items: [{
          id: 'restore',
          label: 'Restore',
          icon: 'keyboard_return'
        },{
          id: 'delete',
          label: 'Permanently Delete',
          icon: 'delete'
        }]
      }
    },
    computed: {
      eid_type() {
        return _.get(this.item, 'eid_type', '')
      },
      is_pipe() {
        return this.eid_type === OBJECT_TYPE_PIPE
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
