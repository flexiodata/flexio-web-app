<template>
  <article class="mb3">
    <div class="flex flex-row items-center pa2 bg-black-05">
      <connection-icon :type="ctype" class="v-mid br1 fx-square-2 mr2"></connection-icon>
      <div class="f6 fw6 ttu silver">{{title}}</div>
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
    <div v-if="items.length > 0">
      <div class="flex flex-row items-center pv1 ph2 f6 truncate bb b--light-gray hide-child" v-for="(item, index) in items">
        <div class="flex-none mr1">
          <img v-if="isDir(item)" src="../assets/file-icon/folder-open-16.png" class="dib" alt="Folder">
          <img v-else src="../assets/file-icon/file-16.png" class="dib" alt="File">
        </div>
        <div class="flex-fill">{{item.path}}</div>
        <div class="flex-none pointer f3 lh-solid b child black-30 hover-black-60" @click="deleteFile(item)">&times;</div>
      </div>
    </div>
    <div class="ma3 tc" v-else>
      <div class="tl" v-if="is_stdin">
        <div class="lh-copy mid-gray f6 mb2 i">Input files using the command line.</div>
        <div class="flex flex-row items-stretch">
          <div class="flex-fill pa2 f6 code bt bb bl b--black-10 br1 br--left" :id="code_id">
            {{pipe_cmd_line_example}}
          </div>
          <btn
            btn-md
            btn-primary
            class="br1 br--right hint--top-left clipboardjs"
            aria-label="Copy to Clipboard"
            :data-clipboard-target="'#'+code_id"
          ><span class="ttu b">Copy</span></btn>
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
        code_id: _.uniqueId('code-'),
        show_file_chooser_modal: false
      }
    },
    computed: {
      ctype() {
        return _.get(this.item, 'metadata.connection_type', '')
      },
      items() {
        return _.get(this.item, 'params.items', '')
      },
      pipe() {
        return _.get(this.$store, 'state.objects.'+this.pipeEid, {})
      },
      pipe_identifier() {
        var ename = _.get(this.pipe, 'ename', '')
        return ename.length > 0 ? ename : _.get(this.pipe, 'eid', '')
      },
      pipe_cmd_line_example() {
        return 'flexio pipes run ' + this.pipe_identifier + ' myfile.txt'
      },
      connection() {
        var connection_eid = _.get(this.item, 'params.connection', '')
        return _.get(this.$store, 'state.objects.'+connection_eid, {
          connection_type: this.ctype
        })
      },
      service_name() {
        return _.result(this, 'cinfo.service_name', '')
      },
      title() {
        var name = _.get(this.connection, 'name', '')
        return name.length > 0 ? name : this.service_name
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
      isDir(item) {
        return _.includes(_.get(item, 'path', ''), '*.')
      },
      updateFiles(items, modal) {
        var eid = this.pipeEid
        var task_eid = _.get(this.item, 'eid', '')
        var attrs = _.cloneDeep(_.omit(this.item, 'eid'))

        _.set(attrs, 'params.items', items)

        // edit pipe task
        this.$store.dispatch('updatePipeTask', { eid, task_eid, attrs }).then(response => {
          if (response.ok)
          {
            if (!_.isNil(modal))
              modal.close()
          }
           else
          {
          }
        })
      },
      addFiles(items, modal) {
        // add wildcards for folders
        var new_items = _.map(items, (f) => {
          return _.get(f, 'is_dir', false)
            ? { path: _.get(f, 'path') + '/*.*' }
            : _.pick(f, ['path'])
        })

        var existing_items = _.get(this.item, 'params.items', [])
        var new_items = existing_items.concat(new_items)
        this.updateFiles(new_items, modal)
      },
      deleteFile(item) {
        var existing_items = _.get(this.item, 'params.items', [])
        var new_items = _.reject(existing_items, (i) => {
          return _.get(i, 'path') == _.get(item, 'path')
        })
        this.updateFiles(new_items)
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
