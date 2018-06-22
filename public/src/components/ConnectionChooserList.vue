<template>
  <div
    class="flex flex-row justify-center items-center"
    style="min-height: 4rem"
    v-if="is_fetching"
  >
    <Spinner size="medium" />
    <span class="ml2 f5">Loading...</span>
  </div>
  <div v-else>
    <ConnectionChooserItem
      v-for="(item, idx) in items"
      :key="item.eid"
      :item="item"
      :layout="layout"
      :connection-eid="connection_eid"
      :show-selection-checkmark="showSelectionCheckmark"
      @item-activate="onItemActivate"
      v-on="$listeners"
    />
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import ConnectionChooserItem from './ConnectionChooserItem.vue'

  export default {
    props: {
      'connection': {
        type: Object,
        default: () => { return null }
      },
      'connection-type-filter': {
        type: String,
        default: ''
      },
      'layout': {
        type: String, // 'list' or 'grid'
        default: 'list'
      },
      'show-selection-checkmark': {
        type: Boolean,
        default: false
      }
    },
    components: {
      Spinner,
      ConnectionChooserItem
    },
    watch: {
      connection: {
        handler: 'updateConnection',
        immediate: true,
        deep: true
      }
    },
    data() {
      return {
        connection_eid: ''
      }
    },
    computed: {
      // mix this into the outer object with the object spread operator
      ...mapState({
        'is_fetching': 'connections_fetching'
      }),
      items() {
        var items = this.getAvailableConnections()

        if (this.connectionTypeFilter.length == 0)
          return items

        return _.filter(items, { connection_type: this.connectionTypeFilter })
      }
    },
    methods: {
      ...mapGetters([
        'getAvailableConnections'
      ]),
      updateConnection(item) {
        this.connection_eid = _.get(item, 'eid', '')
      },
      onItemActivate(item) {
        this.connection_eid = _.get(item, 'eid', '')
        this.$emit('item-activate', item)
      }
    }
  }
</script>
