<template>
  <div class="flex flex-column h-100">
    <FileExplorerBar
      class="flex-none fw4 f6 ba b--black-10 mb2" style="padding: 0.125rem"
      :connection="active_connection"
      :path="connection_path"
      @open-folder="openFolder"
    />
    <div
      class="flex flex-row h-100 overflow-hidden"
      v-if="connections.length > 0"
    >
      <div
        class="flex-none overflow-y-auto"
        v-if="showConnectionList"
      >
        <ConnectionList
          class="h-100 overflow-y-auto br b--black-05"
          style="min-height: 15rem"
          item-size="small"
          :items="connections"
          :selected-item.sync="active_connection"
          :show-status="false"
          :show-add-button="true"
          @item-activate="onConnectionActivate"
        />
      </div>
      <div class="flex-fill overflow-y-auto" :class="{ 'ml2': showConnectionList }">
        <FileChooserList
          ref="file-chooser"
          :team-name="team_name"
          :path="connection_path"
          @open-folder="openFolder"
          @selection-change="updateItems"
          v-bind="$attrs"
        />
      </div>
    </div>
  </div>
</template>

<script>
  import { CONNECTION_TYPE_FLEX } from '@/constants/connection-type'
  import { mapGetters } from 'vuex'
  import * as connections from '@/constants/connection-info'
  import FileExplorerBar from '@/components/FileExplorerBar'
  import FileChooserList from '@/components/FileChooserList'
  import ConnectionList from '@/components/ConnectionList'
  import MixinConnection from '@/components/mixins/connection'

  /*
  const LOCAL_STORAGE_ITEM = {
    connection_type: CONNECTION_TYPE_FLEX,
    eid: 'flex',
    title: 'Flex.io'
  }
  */

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
      ConnectionList
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
        return _.filter(this.getAvailableConnections(), this.$_Connection_isStorage)
      },
      team_name() {
        return _.get(this.active_connection, 'owned_by.eid', 'me')
      }
    },
    methods: {
      ...mapGetters('connections', {
        'getAvailableConnections': 'getAvailableConnections'
      }),
      cinfo() {
        return _.find(connections, { connection_type: this.ctype })
      },
      submit() {
        this.$emit('submit', this.items)
      },
      reset(attrs) {
        this.active_connection = {}
        this.connection_path = this.getConnectionBasePath()
        this.items = []
        this.$emit('update:selectedItems', [])
      },
      onHide() {
        this.reset()
      },
      openFolder(path) {
        this.connection_path = _.defaultTo(path, this.getConnectionBasePath())
        this.items = []
        this.$emit('update:selectedItems', [])
        this.$emit('open-folder', this.connection_path)
      },
      getConnectionIdentifier() {
        var cid = _.get(this.active_connection, 'name', '')
        return cid.length > 0 ? cid : _.get(this.active_connection, 'eid', '')
      },
      getConnectionBasePath() {
        // Flex.io connection now has a name of 'flex'... keeping this for reference for a bit...
        /*
        if (_.get(this.active_connection, 'connection_type', '') == CONNECTION_TYPE_FLEX) {
          return 'flex:/'
        }
        */

        return this.getConnectionIdentifier() + ':/'
      },
      initFromConnection(connection) {
        var old_connection_path = this.connection_path
        var first_connection = _.first(this.connections)

        // do a hard refresh of the file list
        this.active_connection = _.cloneDeep(connection || first_connection)
        this.connection_path = this.getConnectionBasePath()
        this.$nextTick(() => {
          if (old_connection_path != this.connection_path) {
            this.openFolder()
          } else {
            // since the connection path hasn't changed, reactivity isn't going
            // to trigger a refresh here so we need to do it manually
            this.$refs['file-chooser'].refreshList()
          }
        })
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
