<template>
  <div>
    <div v-if="is_fetching">
      <div class="pa1 flex flex-row items-center">
        <spinner size="mini" inline></spinner>
        <span class="ml2 f7">Loading...</span>
      </div>
    </div>
    <div class="pa1 f7 i" v-else-if="items.length == 0">
      This folder is either empty or does not exist
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
        >
        </file-chooser-item>
      </tbody>
    </table>
  </div>
</template>

<script>
  import api from '../api'
  import Spinner from './Spinner.vue'
  import FileChooserItem from './FileChooserItem.vue'

  export default {
    props: {
      'connection': {},
      'path': {},
      'folders-only': {
        default: false,
        type: Boolean
      },
      'allow-multiple': {
        default: true,
        type: Boolean
      },
      'allow-folders': {
        default: true,
        type: Boolean
      }
    },
    components: {
      Spinner,
      FileChooserItem
    },
    data() {
      return {
        is_fetching: false,
        last_selected_item: {},
        items: []
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
          : this.last_selected_item

        return this.allowFolders ? items : _.reject(items, { is_dir: true })
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
        if (_.get(item, 'is_dir') === true)
        {
          this.$emit('open-folder', _.defaultTo(item.path, '/'))
          this.last_selected_item = null
        }
      },
      refreshList() {
        var me = this
        var path = _.defaultTo(this.path, '/')
        var eid = _.get(this.connection, 'eid', '')

        if (eid.length == 0)
          return

        this.is_fetching = true
        api.describeConnection({ eid, path }).then(response => {
          var items = _
            .chain(_.defaultTo(response.body, []))
            .map((f) => { return _.assign({}, { is_selected: false }, f) })
            .sortBy([{ is_dir: false }, function(f) { return _.toLower(f.name) } ])
            .value()

          // only show folders
          if (me.foldersOnly)
            items = _.filter(items, (item) => { return _.get(item, 'is_dir') === true })

          me.items = [].concat(items)
          me.is_fetching = false

          me.fireSelectionChangeEvent()
        }, response => {
          console.log(response)
        })
      }
    }
  }
</script>
