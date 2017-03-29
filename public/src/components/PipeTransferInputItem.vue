<template>
  <article class="mb3">
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
          :options="menu_options"
          @select="onMenuItemClick"
          @close="$refs.menu.close()"
        ></ui-menu>
      </ui-popover>
    </div>
    <div v-if="items.length > 0">
      <div class="flex flex-row items-center pa2 f6 truncate bb b--light-gray hide-child" v-for="(item, index) in items">
        <div class="flex-fill">{{item.path}}</div>
        <div class="flex-none f4 pointer silver hover-black child">&times;</div>
      </div>
    </div>
    <div class="ma3 tc" v-else>
      <div class="tl" v-if="is_stdin">
        <div class="lh-copy mid-gray f6 mb3 i">Input files from the command line.</div>
        <div class="pv1 ph2 bg-black-05">
          <code class="f6">$ flexio pipes run pipe-name file.txt *.csv</code>
        </div>
      </div>
      <div v-else>
        <div class="lh-copy mid-gray f6 mb3 tc i">There are no files that have been selected from this connection.</div>
        <div>
          <btn
            btn-md
            btn-primary
            class="ttu b"
            @click="openFileChooser"
          >
            Add files
          </btn>
        </div>
      </div>
    </div>

    <!-- file chooser modal -->
    <file-chooser-modal
      ref="modal-file-chooser"
      :connection="connection"
      @submit="addFiles"
      @hide="show_file_chooser_modal = false"
      v-if="show_file_chooser_modal"
    ></file-chooser-modal>
  </article>
</template>

<script>
  import { CONNECTION_TYPE_STDIN } from '../constants/connection-type'
  import * as connections from '../constants/connection-info'
  import Btn from './Btn.vue'
  import ConnectionIcon from './ConnectionIcon.vue'
  import FileChooserModal from './FileChooserModal.vue'

  export default {
    props: ['item'],
    components: {
      Btn,
      ConnectionIcon,
      FileChooserModal
    },
    inject: ['pipeEid'],
    data() {
      return {
        show_file_chooser_modal: false
      }
    },
    computed: {
      connection() {
        var connection_eid = _.get(this.item, 'params.connection', '')
        return _.get(this.$store, 'state.objects.'+connection_eid)
      },
      ctype() {
        return _.get(this.item, 'metadata.connection_type', '')
      },
      items() {
        return _.get(this.item, 'params.items', '')
      },
      service_name() {
        return _.result(this, 'cinfo.service_name', '')
      },
      is_stdin() {
        return this.ctype == CONNECTION_TYPE_STDIN
      },
      menu_options() {
        var items = []

        if (!this.is_stdin)
        {
          items = [{
            id: 'add-items',
            label: 'Add files',
            icon: 'add_circle'
          },{
            type: 'divider'
          }]
        }

        items.push({
          id: 'delete',
          label: 'Remove this input',
          icon: 'delete'
        })

        return items
      }
    },
    methods: {
      cinfo() {
        return _.find(connections, { connection_type: this.ctype })
      },
      addFiles(items, modal) {
        var eid = this.pipeEid
        var task_eid = _.get(this.item, 'eid', '')
        var existing_items = _.get(this.item, 'params.items', [])
        var attrs = _.cloneDeep(_.omit(this.item, 'eid'))

        // add the chosen files to the existing files
        _.set(attrs, 'params.items', existing_items.concat(items))

        // edit pipe task
        this.$store.dispatch('updatePipeTask', { eid, task_eid, attrs }).then(response => {
          if (response.ok)
          {
            modal.close()
          }
           else
          {
          }
        })
      },
      openFileChooser() {
        if (!_.isNil(this.connection))
        {
          this.show_file_chooser_modal = true
          this.$nextTick(() => { this.$refs['modal-file-chooser'].open() })
        }
      },
      onMenuItemClick(menu_item) {
        switch (menu_item.id)
        {
          case 'add-items': return this.openFileChooser()
          case 'delete':    return this.$emit('delete', this.item)
        }
      }
    }
  }
</script>
