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
            itemCls: 'min-w5 pa3 pr2 ba b--white bg-white hover-bg-nearer-white',
            itemStyle: 'margin: 3px 3px 3px 0',
            selectedCls: 'relative b--black-10 bg-nearer-white',
            showIdentifier: true,
            showUrl: false
          }"
          @item-activate="onConnectionActivate"
        />
      </div>
      <div class="flex-fill overflow-y-auto" :class="{ 'ml2': showConnectionList }">
        <FileChooserList
          ref="file-chooser"
          :path="connection_path"
          @open-folder="openFolder"
          @selection-change="updateItems"
          v-bind="$attrs"
          v-if="file_chooser_mode == 'filechooser'"
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
  import FileExplorerBar from '@comp/FileExplorerBar'
  import FileChooserList from '@comp/FileChooserList'
  import AbstractList from '@comp/AbstractList'
  import MixinConnection from '@comp/mixins/connection'

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
      AbstractList
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
        /*
        var url_list = this.$refs['url-input-list']
        if (!_.isNil(url_list))
          url_list.finishEdit()
        */

        this.$emit('submit', this.items)
      },
      reset(attrs) {
        this.active_connection = {}
        this.connection_path = this.getConnectionBasePath()
        this.items = []
        this.$emit('update:selectedItems', [])
      },
      onHide() {
        /*
        var url_list = this.$refs['url-input-list']
        if (!_.isNil(url_list))
          url_list.reset()
        */

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
          return 'flex:/'
        }
        */

        return this.getConnectionIdentifier() + ':/'
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
