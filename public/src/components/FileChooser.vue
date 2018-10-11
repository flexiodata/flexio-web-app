<template>
  <div class="flex flex-column h-100">
    <FileExplorerBar
      class="flex-none fw4 f6 ba b--black-10 mb2" style="padding: 0.125rem"
      :connection="active_connection"
      :path="connection_path"
      @open-folder="openFolder"
      v-if="file_chooser_mode == 'filechooser'"
    />
    <div
      class="flex flex-row h-100 overflow-hidden"
      v-if="connections.length > 0"
    >
      <div
        class="flex-none overflow-y-auto"
        v-if="showConnectionList"
      >
        <AbstractList
          ref="list"
          class="br b--black-05 overflow-y-auto"
          layout="list"
          item-component="AbstractConnectionChooserItem"
          :selected-item.sync="active_connection"
          :items="connections"
          :item-options="{
            'item-cls': 'min-w5 pa3 pr2 darken-05',
            'item-style': 'margin: 0.125rem',
            'show-dropdown': false,
            'show-identifier': true,
            'show-url': false
          }"
          @item-activate="onConnectionActivate"
        />
      </div>
      <div class="flex-fill overflow-y-auto">
        <FileChooserList
          ref="file-chooser"
          :path="connection_path"
          @open-folder="openFolder"
          @selection-change="updateItems"
          v-bind="$attrs"
          v-if="file_chooser_mode == 'filechooser'"
        />
        <UrlInputList
          ref="url-input-list"
          class="ba b--black-10 pa1"
          style="min-height: 16rem; max-height: 16rem"
          @selection-change="updateItems"
          v-if="file_chooser_mode == 'textentry'"
        />
      </div>
    </div>
  </div>
</template>

<script>
  import {
    CONNECTION_TYPE_FLEX,
    CONNECTION_TYPE_HTTP,
    CONNECTION_TYPE_RSS
  } from '../constants/connection-type'
  import { mapGetters } from 'vuex'
  import * as connections from '../constants/connection-info'
  import FileExplorerBar from './FileExplorerBar.vue'
  import FileChooserList from './FileChooserList.vue'
  import AbstractList from './AbstractList.vue'
  import UrlInputList from './UrlInputList.vue'
  import MixinConnection from './mixins/connection'

  const LOCAL_STORAGE_ITEM = {
    connection_type: CONNECTION_TYPE_FLEX,
    eid: 'flex',
    name: 'Flex.io'
  }

  export default {
    inheritAttrs: false,
    mixins: [MixinConnection],
    props: {
      connection: {
        type: Object
      },
      selectedItems: {
        type: Array
      },
      showConnectionList: {
        type: Boolean,
        default: false
      }
    },
    components: {
      FileExplorerBar,
      FileChooserList,
      AbstractList,
      UrlInputList
    },
    watch: {
      connection: {
        handler: 'initFromConnection',
        immediate: true,
        deep: true
      }
    },
    data() {
      return {
        active_connection: {},
        connection_path: this.getConnectionBasePath(),
        items: []
      }
    },
    computed: {
      ctype() {
        return _.get(this.active_connection, 'connection_type', '')
      },
      connections() {
        var items = _.filter(this.getAvailableConnections(), this.$_Connection_isStorage)
        items = _.sortBy(items, 'name')
        return [LOCAL_STORAGE_ITEM].concat(items)
      },
      file_chooser_mode() {
        switch (this.ctype)
        {
          case CONNECTION_TYPE_HTTP: return 'textentry'
          case CONNECTION_TYPE_RSS:  return 'textentry'
        }

        return 'filechooser'
      }
    },
    methods: {
      ...mapGetters([
        'getAvailableConnections'
      ]),
      cinfo() {
        return _.find(connections, { connection_type: this.ctype })
      },
      submit() {
        var url_list = this.$refs['url-input-list']
        if (!_.isNil(url_list))
          url_list.finishEdit()

        this.$emit('submit', this.items)
      },
      reset(attrs) {
        this.connection_path = '/'
        this.items = []
        this.$emit('update:selectedItems', [])
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
        this.$emit('update:selectedItems', [])
        this.$emit('open-folder', this.connection_path)
      },
      getConnectionIdentifier() {
        var cid = _.get(this.active_connection, 'alias', '')
        return cid.length > 0 ? cid : _.get(this.active_connection, 'eid', '')
      },
      getConnectionBasePath() {
        // Flex.io connection now has an alias of 'flex'... keeping this for reference for a bit...
        /*
        if (_.get(this.active_connection, 'connection_type', '') == CONNECTION_TYPE_FLEX) {
          return '/flex'
        }
        */

        return '/' + this.getConnectionIdentifier()
      },
      initFromConnection(connection) {
        // do a hard refresh of the file list
        this.active_connection = _.cloneDeep(connection || LOCAL_STORAGE_ITEM)
        this.connection_path = this.getConnectionBasePath()
        this.$nextTick(() => { this.openFolder() })
      },
      updateItems(items, path) {
        this.items = items
        this.$emit('update:selectedItems', items)
        this.$emit('selection-change', items, path)
      },
      onConnectionActivate(item) {
        this.active_connection = _.cloneDeep(item)
        this.initFromConnection(this.active_connection)
      }
    }
  }
</script>
