<template>
  <!-- fetching -->
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100">
      <Spinner size="large" message="Loading storage items..." />
    </div>
  </div>

  <!-- fetched -->
  <div class="flex flex-column" v-else-if="is_fetched">
    <div class="flex flex-row flex-fill" v-if="connections.length > 0">
      <AbstractList
        ref="list"
        class="overflow-y-auto br b--black-05"
        layout="list"
        item-component="AbstractConnectionChooserItem"
        :selected-item.sync="connection"
        :items="connections"
        :item-options="{
          itemCls: 'min-w5 pa3 bb b--black-05 bg-white hover-bg-nearer-white',
          selectedCls: 'relative bg-nearer-white',
          showIdentifier: true,
          showUrl: false
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
  import AbstractList from '@comp/AbstractList'
  import FileChooser from '@comp/FileChooser'
  import EmptyItem from '@comp/EmptyItem'
  import MixinConnection from '@comp/mixins/connection'

  const LOCAL_STORAGE_ITEM = {
    connection_type: CONNECTION_TYPE_FLEX,
    eid: 'flex',
    short_description: 'Flex.io'
  }

  export default {
    metaInfo: {
      title: 'Storage'
    },
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
        'is_fetched': 'connections_fetched',
        'active_team_name': 'active_team_name'
      }),
      connections() {
        var items = _.filter(this.getAvailableConnections(), this.$_Connection_isStorage)
        items = _.sortBy(items, 'short_description')
        return [LOCAL_STORAGE_ITEM].concat(items)
      },
      ctype() {
        return _.get(this.connection, 'connection_type', '')
      },
      cname() {
        return _.get(this.connection, 'short_description', '')
      },
      has_connection() {
        return this.ctype.length > 0
      },
      title() {
        var ru = this.active_team_name
        return ru && ru.length > 0 ? ru + '/' + 'storage' : 'Storage'
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
        if (!this.is_fetched && !this.is_fetching) {
          this.$store.dispatch('v2_action_fetchConnections', {}).catch(error => {
            // TODO: add error handling?
          })
        }
      },
      onConnectionActivate(item) {
        this.connection = _.assign({}, item)
      }
    }
  }
</script>
