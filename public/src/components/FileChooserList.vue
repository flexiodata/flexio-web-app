<template>
  <div>
    <div v-if="is_fetching">
      <div class="pa1 flex flex-row items-center">
        <Spinner size="small" />
        <span class="ml2 f6">Loading...</span>
      </div>
    </div>
    <div class="pa1 f7 silver i" v-else-if="error_message.length > 0">
      {{error_message}}
    </div>
    <div class="pa1 f7 silver i" v-else-if="items.length == 0">
      {{empty_message}}
    </div>
    <table v-else class="f6 w-100">
      <tbody class="lh-copy f6">
        <FileChooserItem
          v-for="(item, index) in items"
          :item="item"
          :index="index"
          :key="item.full_path"
          @click="itemClick"
          @ctrl-click="itemCtrlClick"
          @shift-click="itemShiftClick"
          @dblclick="itemDblClick"
        />
      </tbody>
    </table>
  </div>
</template>

<script>
  import api from '../api'
  import Spinner from 'vue-simple-spinner'
  import FileChooserItem from './FileChooserItem.vue'

  const VFS_TYPE_DIR  = 'DIR'
  const VFS_TYPE_FILE = 'FILE'

  const endsWith = (str, suffix) => {
    return str.indexOf(suffix, str.length - suffix.length) !== -1
  }

  export default {
    props: {
      path: {
        type: [String, Boolean],
        default: '/'
      },
      emptyMessage: {
        type: String,
        default: ''
      },
      foldersOnly: {
        type: Boolean,
        default: false
      },
      allowMultiple: {
        type: Boolean,
        default: true
      },
      allowFolders: {
        type: Boolean,
        default: true
      },
      filetypeFilter: {
        type: Array,
        default: () => { return [] }
      },
      fireSelectionChangeOnInit: {
        type: Boolean,
        default: false
      }
    },
    components: {
      Spinner,
      FileChooserItem
    },
    data() {
      return {
        is_fetching: false,
        is_inited: false,
        last_selected_item: {},
        items: [],
        error_message: ''
      }
    },
    watch: {
      path: {
        handler: 'refreshList',
        immediate: true
      }
    },
    computed: {
      selected_items() {
        var items = this.allowMultiple
          ? _.filter(this.items, { is_selected: true })
          : [ this.last_selected_item ]

        items = _.compact(items)

        return this.allowFolders ? items : _.reject(items, { type: VFS_TYPE_DIR })
      },
      empty_message() {
        if (this.emptyMessage.length > 0) {
          return this.emptyMessage
        }

        if (this.foldersOnly) {
          return 'This folder has no subfolders'
        }

        return 'This folder is empty'
      }
    },
    methods: {
      getSelectedItems() {
        return this.selected_items
      },
      fireSelectionChangeEvent(path) {
        // `path` is filled out if the selection change
        // happens from a refresh (when opening a new folder)
        this.$emit('selection-change', this.selected_items, path)
      },
      isFolderItem(item) {
        return _.get(item, 'type') == VFS_TYPE_DIR
      },
      itemClick(item, evt) {
        // don't allow folders to be selected
        if (!this.allowFolders && this.isFolderItem(item)) {
          return
        }

        // handled below
        if (evt.ctrlKey || evt.shiftKey) {
          return
        }

        _.each(this.items, (f) => { f.is_selected = false })
        this.last_selected_item = item
        item.is_selected = true

        this.$emit('item-click', item)

        this.fireSelectionChangeEvent()
      },
      itemCtrlClick(item, evt) {
        // if we aren't allowing multiple selection; reset 'em all to unselected
        if (!this.allowMultiple) {
          _.each(this.items, (f) => { f.is_selected = false })
        }

        this.last_selected_item = item
        item.is_selected = !item.is_selected

        this.fireSelectionChangeEvent()
      },
      itemShiftClick(item, evt) {
        var start_idx = _.indexOf(this.items, item)
        var end_idx = _.indexOf(this.items, this.last_selected_item)

        if (start_idx > end_idx) {
          var tmp = start_idx
          start_idx = end_idx
          end_idx = tmp
        }

        _.each(this.items, (f, idx) => {
          if (idx >= start_idx && idx <= end_idx)
            f.is_selected = true
        })

        // if we aren't allowing multiple selection; reset 'em all to unselected
        if (!this.allowMultiple) {
          _.each(this.items, (f) => { f.is_selected = false })
        }

        this.last_selected_item = item
        item.is_selected = true

        this.fireSelectionChangeEvent()
      },
      itemDblClick(item) {
        if (this.isFolderItem(item)) {
          this.$emit('open-folder', _.defaultTo(item.full_path, '/'))
          this.last_selected_item = null
        }
      },
      refreshList() {
        if (this.path === false) {
          return
        }

        this.items = []
        this.last_selected_item = null
        this.is_fetching = true

        if (this.is_inited || this.fireSelectionChangeOnInit) {
          this.fireSelectionChangeEvent(this.path)
        }

        var path = _.defaultTo(this.path, '/')

        api.v2_vfsListFiles('me', path).then(response => {
          var items = _
            .chain(_.defaultTo(response.data, []))
            .map((f) => { return _.assign({}, { is_selected: false }, f) })
            .sortBy([{ type: VFS_TYPE_FILE }, function(f) { return _.toLower(f.name) } ])
            .value()

          // only show folders
          if (this.foldersOnly) {
            items = _.filter(items, (item) => { return this.isFolderItem(item) })
          }

          // filter by filetype
          if (this.filetypeFilter.length > 0) {
            var filetypes = _.map(this.filetypeFilter, (ft) => { return '.' + ft.toLowerCase() })
            items = _.filter(items, (item) => {
              // don't filter out folders
              if (this.isFolderItem(item)) {
                return true
              }

              var filename = item.path.toLowerCase()

              var found = false
              _.each(filetypes, (ft) => {
                if (endsWith(filename, ft)) {
                  found = true
                }
              })

              return found
            })
          }

          this.is_fetching = false
          this.items = [].concat(items)
          this.error_message = ''

          if (this.is_inited || this.fireSelectionChangeOnInit) {
            this.fireSelectionChangeEvent(path)
          }

          this.is_inited = true
        }).catch(error => {
          this.is_fetching = false
          this.items = []
          this.error_message = _.get(error, 'response.data.error.message', '')
        })
      }
    }
  }
</script>
