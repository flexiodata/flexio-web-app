<template>
  <!-- fetching -->
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100">
      <Spinner size="large" message="Loading storage items..." />
    </div>
  </div>

  <!-- fetched -->
  <div class="flex flex-column" v-else-if="is_fetched">
    <!-- control bar -->
    <div class="pa3 relative bg-white bb b--black-05">
      <div class="flex flex-row">
        <div class="flex-fill flex flex-row items-center">
          <div class="f2">Storage</div>
        </div>
        <div class="flex-none flex flex-row items-center" v-if="false">
          <el-button type="primary" class="ttu b" @click="openAddModal">New storage</el-button>
        </div>
      </div>
    </div>

    <div class="flex flex-row h-100" v-if="connections.length > 0">
      <AbstractList
        ref="list"
        class="br b--black-05 overflow-y-auto"
        layout="list"
        item-component="AbstractConnectionChooserItem"
        :selected-item.sync="connection"
        :items="connections"
        :item-options="{
          'item-cls': 'min-w5 pa3 pr2 ba b--white bg-white hover-bg-nearer-white',
          'item-style': 'margin: 3px',
          'selected-cls': 'relative b--black-10 bg-nearer-white',
          'show-dropdown': false,
          'show-identifier': true,
          'show-url': false
        }"
        @item-activate="onConnectionActivate"
      />
      <div class="flex-fill">
        <FileChooser class="pa2"
          :connection="connection"
          v-if="has_connection"
        />
      </div>
    </div>
    <div class="flex flex-column justify-center h-100" v-else>
      <EmptyItem>
        <i slot="icon" class="material-icons">repeat</i>
        <span slot="text">No storage items to show</span>
      </EmptyItem>
    </div>
  </div>
</template>

<script>
  import { CONNECTION_TYPE_FLEX } from '../constants/connection-type'
  import { OBJECT_STATUS_AVAILABLE, OBJECT_STATUS_PENDING } from '../constants/object-status'
  import { mapState, mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import AbstractList from './AbstractList.vue'
  import FileChooser from './FileChooser.vue'
  import EmptyItem from './EmptyItem.vue'
  import MixinConnection from './mixins/connection'

  const LOCAL_STORAGE_ITEM = {
    connection_type: CONNECTION_TYPE_FLEX,
    eid: 'flex',
    name: 'Flex.io'
  }

  export default {
    mixins: [MixinConnection],
    components: {
      Spinner,
      AbstractList,
      FileChooser,
      EmptyItem
    },
    data() {
      return {
        connection: LOCAL_STORAGE_ITEM,
        show_connection_props_modal: false
      }
    },
    computed: {
      // mix this into the outer object with the object spread operator
      ...mapState({
        'is_fetching': 'connections_fetching',
        'is_fetched': 'connections_fetched'
      }),
      connections() {
        var items = _.filter(this.getAvailableConnections(), this.$_Connection_isStorage)
        items = _.sortBy(items, 'name')
        return [LOCAL_STORAGE_ITEM].concat(items)
      },
      ctype() {
        return _.get(this.connection, 'connection_type', '')
      },
      cname() {
        return _.get(this.connection, 'name', '')
      },
      has_connection() {
        return this.ctype.length > 0
      }
    },
    created() {
      this.tryFetchConnections()
    },
    mounted() {
      this.$store.track('Visited Storage Page')
    },
    methods: {
      ...mapGetters([
        'getAvailableConnections'
      ]),
      openAddModal() {
        this.show_connection_props_modal = true
        this.$nextTick(() => { this.$refs['modal-connection-props'].open() })
      },
      tryFetchConnections() {
        if (!this.is_fetched && !this.is_fetching)
          this.$store.dispatch('fetchConnections')
      },
      onConnectionActivate(item) {
        this.connection = _.assign({}, item)
      }
    }
  }
</script>
