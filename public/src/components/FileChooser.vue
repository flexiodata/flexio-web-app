<template>
  <div class="flex flex-column h-100">
    <file-explorer-bar
      class="flex-none fw4 f6 ba b--black-10 mb2" style="padding: 0.125rem"
      :connection="connection"
      :path="connection_path"
      @open-folder="openFolder"
      v-if="file_chooser_mode == 'filechooser'"
    />

    <div class="flex-fill overflow-y-auto">
      <file-chooser-list
        ref="file-chooser"
        :connection="connection"
        :path="connection_path"
        :folders-only="foldersOnly"
        :allow-multiple="allowMultiple"
        :allow-folders="allowFolders"
        @open-folder="openFolder"
        @selection-change="updateItems"
        v-if="file_chooser_mode == 'filechooser'"
      />
      <url-input-list
        ref="url-input-list"
        class="ba b--black-10 pa1 min-h5 max-h5"
        @selection-change="updateItems"
        v-if="file_chooser_mode == 'textentry'"
      />
    </div>
  </div>
</template>

<script>
  import {
    CONNECTION_TYPE_HOME,
    CONNECTION_TYPE_HTTP,
    CONNECTION_TYPE_RSS
  } from '../constants/connection-type'
  import { TASK_OP_INPUT, TASK_OP_OUTPUT } from '../constants/task-op'
  import * as connections from '../constants/connection-info'
  import FileExplorerBar from './FileExplorerBar.vue'
  import FileChooserList from './FileChooserList.vue'
  import UrlInputList from './UrlInputList.vue'

  export default {
    props: {
      'connection': {
        type: Object,
        required: true
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
      FileExplorerBar,
      FileChooserList,
      UrlInputList
    },
    watch: {
      connection() {
        this.openFolder()
      }
    },
    data() {
      return {
        connection_path: this.getConnectionBasePath(),
        items: []
      }
    },
    computed: {
      ctype() {
        return _.get(this.connection, 'connection_type', '')
      },
      file_chooser_mode() {
        switch (this.ctype)
        {
          case CONNECTION_TYPE_HTTP: return 'textentry'
          case CONNECTION_TYPE_RSS:  return 'textentry'
        }

        return 'filechooser'
      },
      title() {
        return 'Choose Files'
      },
      submit_label() {
        return 'Add files'
      }
    },
    methods: {
      cinfo() {
        return _.find(connections, { connection_type: this.ctype })
      },
      submit() {
        var url_list = this.$refs['url-input-list']
        if (!_.isNil(url_list))
          url_list.finishEdit()

        this.$emit('submit', this.items, this)
      },
      reset(attrs) {
        this.connection_path = '/'
        this.items = []
      },
      onHide() {
        var url_list = this.$refs['url-input-list']
        if (!_.isNil(url_list))
          url_list.reset()

        this.reset()
      },
      openFolder(path) {
        this.connection_path = _.defaultTo(path, this.getConnectionBasePath())
        this.items = []
      },
      getConnectionIdentifier() {
        var cid = _.get(this.connection, 'alias', '')
        return cid.length > 0 ? cid : _.get(this.connection, 'eid', '')
      },
      getConnectionBasePath() {
        if (_.get(this.connection, 'connection_type', '') == CONNECTION_TYPE_HOME)
          return '/home'
        return '/' + this.getConnectionIdentifier()
      },
      updateItems(items) {
        this.items = items
        this.$emit('selection-change', this.items, this)
      }
    }
  }
</script>
