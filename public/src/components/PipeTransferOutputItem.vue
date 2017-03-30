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
    <div class="ma3">
      <div class="tl" v-if="is_dropbox">
        <div class="lh-copy mid-gray f6 mb2">Files will be output to the following folder:</div>
        <div class="flex flex-row items-center">
          <div class="flex-fill f6 mr2 pa2 ba b--black-10 black">
            <span class="i" v-if="location == '/'">{{friendly_location}}</span>
            <span v-else>{{location}}</span>
          </div>
          <btn
            btn-md
            btn-primary
          >
            <span class="ttu b">Change folder</span>
          </btn>
        </div>
      </div>
      <div class="tl" v-else-if="is_stdout">
        <div class="lh-copy mid-gray f6 mb3 i">Output files from the command line.</div>
        <div class="pv1 ph2 bg-black-05">
          <code class="f6">flexio pipes run pipe-name > output.txt</code>
        </div>
      </div>
    </div>
  </article>
</template>

<script>
  import { CONNECTION_TYPE_DROPBOX, CONNECTION_TYPE_STDOUT } from '../constants/connection-type'
  import { mapGetters } from 'vuex'
  import * as connections from '../constants/connection-info'
  import Btn from './Btn.vue'
  import ConnectionIcon from './ConnectionIcon.vue'

  export default {
    props: ['item'],
    components: {
      Btn,
      ConnectionIcon
    },
    computed: {
      service_name() {
        return _.result(this, 'cinfo.service_name', '')
      },
      ctype() {
        return _.get(this.item, 'metadata.connection_type', '')
      },
      conn_identifier() {
        return _.get(this.item, 'params.connection', '')
      },
      location() {
        return _.get(this.item, 'params.location', '')
      },
      friendly_location() {
        return '<' + this.service_name + ' ' + 'root folder' + '>'
      },
      is_dropbox() {
        return this.ctype == CONNECTION_TYPE_DROPBOX
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
      ...mapGetters([
        'getAllConnections'
      ]),
      cinfo() {
        return _.find(connections, { connection_type: this.ctype })
      },
      getOurConnection() {
        // NOTE: it's really important to include the '_' on the same line
        // as the 'return', otherwise JS will return without doing anything
        return _
          .chain(this.getAllConnections())
          .filter((p) => {
            return _.get(p, 'eid') == this.conn_identifier || _.get(p, 'ename') == this.conn_identifier
          })
          .value()
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
