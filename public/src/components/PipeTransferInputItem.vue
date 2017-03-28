<template>
  <article class="mb5">
    <div class="flex flex-row items-center pa2 bg-black-05 bb b--black-05">
      <connection-icon :type="ctype" class="v-mid br1 fx-square-2 mr2"></connection-icon>
      <div class="f6 fw6 ttu silver">{{service_name}}</div>
      <div class="flex-fill"></div>
      <a
        class="no-underline f5 b dib pointer pa0 link silver hover-black popover-trigger"
        ref="menutrigger"
        tabindex="0"
      >
        <i class="material-icons">menu</i>
      </a>
      <ui-popover
        trigger="menutrigger"
        ref="menu"
        dropdown-position="bottom right"
      >
        <ui-menu
          contain-focus
          has-icons

          :options="[{
            id: 'add-items',
            label: 'Add files',
            icon: 'add_circle'
          },{
            type: 'divider'
          },{
            id: 'delete',
            label: 'Remove this input',
            icon: 'delete'
          }]"

          @select="onMenuItemClick"
          @close="$refs.menu.close()"
        >
        </ui-menu>
      </ui-popover>
    </div>
    <div v-if="items.length > 0">
      <div class="flex flex-row items-center pa2 f6 truncate bb b--light-gray hide-child" v-for="(item, index) in items">
        <div class="flex-fill">{{item.path}}</div>
        <div class="flex-none f4 pointer silver hover-black child">&times;</div>
      </div>
    </div>
    <div class="ma3 tc" v-else>
      <div class=" lh-copy mid-gray f6 mb3 tc i">There are no files that have been selected from this connection.</div>
      <div class="">
        <btn
          btn-md
          btn-primary
          class="ttu b"
        >
          Add files
        </btn>
      </div>
    </div>
  </article>
</template>

<script>
  import * as connections from '../constants/connection-info'
  import Btn from './Btn.vue'
  import ConnectionIcon from './ConnectionIcon.vue'

  export default {
    props: ['item', 'connection-type'],
    components: {
      Btn,
      ConnectionIcon
    },
    computed: {
      ctype() {
        return _.get(this.item, 'metadata.connection_type', '')
      },
      items() {
        return _.get(this.item, 'params.items', '')
      },
      service_name() {
        return _.result(this, 'cinfo.service_name', '')
      }
    },
    methods: {
      cinfo() {
        return _.find(connections, { connection_type: this.ctype })
      },
      onMenuItemClick(menu_item) {
        switch (menu_item.id)
        {
          case 'add-items': return this.$emit('add-items', this.item)
          case 'delete':  return this.$emit('delete', this.item)
        }
      }
    }
  }
</script>
