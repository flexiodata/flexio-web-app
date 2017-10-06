<template>
  <div>
    <div>
      <file-explorer-bar
        class="fw4 f6 ba b--black-10 mb2 css-explorer-bar"
        :connection="connection"
        :path="connection_path"
        @open-folder="openFolder"
        v-if="file_chooser_mode == 'filechooser'"
      ></file-explorer-bar>
    </div>

    <div class="relative">
      <file-chooser-list
        ref="file-chooser"
        class="min-h5 max-h5"
        :connection="connection"
        :path="connection_path"
        @open-folder="openFolder"
        @selection-change="updateItems"
        v-if="file_chooser_mode == 'filechooser'"
      ></file-chooser-list>
      <url-input-list
        ref="url-input-list"
        class="ba b--black-20 pa1 min-h5 max-h5"
        @selection-change="updateItems"
        v-if="file_chooser_mode == 'textentry'"
      ></url-input-list>
    </div>
  </div>
</template>

<script>
  import {
    CONNECTION_TYPE_HTTP,
    CONNECTION_TYPE_RSS,
    CONNECTION_TYPE_MYSQL,
    CONNECTION_TYPE_POSTGRES
  } from '../constants/connection-type'
  import { TASK_TYPE_INPUT, TASK_TYPE_OUTPUT } from '../constants/task-type'
  import * as connections from '../constants/connection-info'
  import Btn from './Btn.vue'
  import FileExplorerBar from './FileExplorerBar.vue'
  import FileChooserList from './FileChooserList.vue'
  import UrlInputList from './UrlInputList.vue'

  const defaultAttrs = () => {
    return {
      eid: null,
      name: 'New Pipe',
      ename: '',
      description: ''
    }
  }

  export default {
    props: {
      'connection': {
        type: Object,
        required: true
      }
    },
    components: {
      Btn,
      FileExplorerBar,
      FileChooserList,
      UrlInputList
    },
    data() {
      return {
        connection_path: '/',
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
      open(attrs) {
        this.reset(attrs)
        this.$refs['dialog'].open()
        return this
      },
      close() {
        this.$refs['dialog'].close()
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
        this.connection_path = _.defaultTo(path, '/')
        this.items = []
      },
      updateItems(items) {
        this.items = items
      }
    }
  }
</script>

<style scoped>
  .css-explorer-bar {
    padding: 0.25rem 0.375rem;
  }
</style>
