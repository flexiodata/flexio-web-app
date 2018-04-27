<template>
  <div>
    <div v-if="is_fetching">
      <div class="pa1 flex flex-row items-center">
        <spinner size="small"></spinner>
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
        <file-chooser-item
          v-for="(item, index) in items"
          :item="item"
          :index="index"
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

  export default {
    props: {
      'path': {
        type: String,
        default: '/'
      },
      'empty-message': {
        type: String,
        default: ''
      },
      'folders-only': {
        type: Boolean,
        default: false
      },
      'allow-multiple': {
        type: Boolean,
        default: true
      },
      'allow-folders': {
        type: Boolean,
        default: true
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
      path(val, old_val) {
        this.refreshList()
      }
    },
    computed: {
      selected_items() {
        var items = this.allowMultiple
          ? _.filter(this.items, { is_selected: true })
          : [ this.last_selected_item ]

        return this.allowFolders ? items : _.reject(items, { type: VFS_TYPE_DIR })
      },
      empty_message() {
        if (this.emptyMessage.length > 0)
          return this.emptyMessage

        if (this.foldersOnly)
          return 'This folder has no subfolders'

        return 'This folder is empty'
      }
    },
    mounted() {
      this.refreshList()
    },
    methods: {
      getSelectedItems() {
        return this.selected_items
      },
      fireSelectionChangeEvent() {
        this.$emit('selection-change', this.selected_items)
      },
      itemClick(item, evt) {
        // handled below
        if (evt.ctrlKey || evt.shiftKey)
          return

        _.each(this.items, (f) => { f.is_selected = false })
        this.last_selected_item = item
        item.is_selected = true

        this.$emit('item-click', item)

        this.fireSelectionChangeEvent()
      },
      itemCtrlClick(item, evt) {
        // if we aren't allowing multiple selection; reset 'em all to unselected
        if (!this.allowMultiple)
          _.each(this.items, (f) => { f.is_selected = false })

        this.last_selected_item = item
        item.is_selected = !item.is_selected

        this.fireSelectionChangeEvent()
      },
      itemShiftClick(item, evt) {
        var start_idx = _.indexOf(this.items, item)
        var end_idx = _.indexOf(this.items, this.last_selected_item)

        if (start_idx > end_idx)
        {
          var tmp = start_idx
          start_idx = end_idx
          end_idx = tmp
        }

        _.each(this.items, (f, idx) => {
          if (idx >= start_idx && idx <= end_idx)
            f.is_selected = true
        })

        // if we aren't allowing multiple selection; reset 'em all to unselected
        if (!this.allowMultiple)
          _.each(this.items, (f) => { f.is_selected = false })

        this.last_selected_item = item
        item.is_selected = true

        this.fireSelectionChangeEvent()
      },
      itemDblClick(item) {
        if (_.get(item, 'type') == VFS_TYPE_DIR)
        {
          this.$emit('open-folder', _.defaultTo(item.path, '/'))
          this.last_selected_item = null
        }
      },
      refreshList() {
        var path = _.defaultTo(this.path, '/')

        this.is_fetching = true
        api.vfsListFiles({ path }).then(response => {
          var items = _
            .chain(_.defaultTo(response.body, []))
            .map((f) => { return _.assign({}, { is_selected: false }, f) })
            .sortBy([{ type: VFS_TYPE_FILE }, function(f) { return _.toLower(f.name) } ])
            .value()

          // only show folders
          if (this.foldersOnly)
            items = _.filter(items, (item) => { return _.get(item, 'type') == VFS_TYPE_DIR })

          this.is_fetching = false
          this.items = [].concat(items)
          this.error_message = ''

          if (this.is_inited)
            this.fireSelectionChangeEvent()

          this.is_inited = true
        }, response => {
          this.is_fetching = false
          this.items = [].concat([])
          this.error_message = _.get(response.body, 'error.message', '')
        })
      }
    }
  }
</script>
