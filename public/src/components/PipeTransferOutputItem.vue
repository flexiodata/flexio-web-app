<template>
  <article class="mb3">
    <div class="flex flex-row items-center pa2 bg-black-05 bb b--black-05">
      <connection-icon :type="ctype" class="v-mid br1 fx-square-2 mr2"></connection-icon>
      <div class="f6 fw6 ttu silver">{{service_name}}</div>
      <div class="flex-fill"></div>
      <a
        class="no-underline f5 b dib pointer pa0 link black-30 hover-black-60 popover-trigger"
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
          :options="menu_options"
          @select="onMenuItemClick"
          @close="$refs.menu.close()"
        ></ui-menu>
      </ui-popover>
    </div>
    <div class="ma3 tc">
      <div class="tl" v-if="is_stdout">
        <div class="lh-copy mid-gray f6 mb3 i">Output files from the command line.</div>
        <div class="pv1 ph2 bg-black-05">
          <code class="f6">flexio pipes run pipe-name > output.txt</code>
        </div>
      </div>
    </div>
  </article>
</template>

<script>
  import { CONNECTION_TYPE_STDOUT } from '../constants/connection-type'
  import * as connections from '../constants/connection-info'
  import ConnectionIcon from './ConnectionIcon.vue'

  export default {
    props: ['item', 'connection-type'],
    components: {
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
      },
      is_stdout() {
        return this.ctype == CONNECTION_TYPE_STDOUT
      },
      menu_options() {
        return [{
          id: 'delete',
          label: 'Remove this output',
          icon: 'delete'
        }]
      }
    },
    methods: {
      cinfo() {
        return _.find(connections, { connection_type: this.ctype })
      },
      onMenuItemClick(menu_item) {
        switch (menu_item.id)
        {
          case 'delete':  return this.$emit('delete', this.item)
        }
      }
    }
  }
</script>
