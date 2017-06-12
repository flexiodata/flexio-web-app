<template>
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100">
      <spinner size="large" message="Loading connections..."></spinner>
    </div>
  </div>
  <empty-item v-else-if="connections.length == 0 && filter.length > 0">
    <i slot="icon" class="material-icons">repeat</i>
    <span slot="text">No connections match the filter criteria</span>
  </empty-item>
  <empty-item v-else-if="connections.length == 0">
    <i slot="icon" class="material-icons">repeat</i>
    <span slot="text">No connections to show</span>
  </empty-item>
  <div v-else>
    <connection-header-item v-if="showHeader && false"></connection-header-item>
    <connection-item
      v-for="(connection, index) in connections"
      :item="connection"
      :index="index"
      @edit="onItemEdit"
      @delete="onItemDelete"
    ></connection-item>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import ConnectionHeaderItem from './ConnectionHeaderItem.vue'
  import ConnectionItem from './ConnectionItem.vue'
  import EmptyItem from './EmptyItem.vue'
  import CommonFilter from './mixins/common-filter'

  export default {
    props: {
      'filter': {
        type: String
      },
      'show-header': {
        type: Boolean,
        default: false
      }
    },
    mixins: [CommonFilter],
    components: {
      Spinner,
      ConnectionHeaderItem,
      ConnectionItem,
      EmptyItem
    },
    computed: {
      // mix this into the outer object with the object spread operator
      ...mapState({
        'is_fetching': 'connections_fetching',
        'is_fetched': 'connections_fetched'
      }),
      connections() {
        return this.commonFilter(this.getOurConnections(), this.filter, ['name', 'description'])
      }
    },
    created() {
      this.tryFetchConnections()
    },
    methods: {
      ...mapGetters([
        'getAllConnections'
      ]),
      tryFetchConnections() {
        if (!this.is_fetched && !this.is_fetching)
          this.$store.dispatch('fetchConnections')
      },
      getOurConnections() {
        var project_eid = this.projectEid

        // NOTE: it's really important to include the '_' on the same line
        // as the 'return', otherwise JS will return without doing anything
        return _
          .chain(this.getAllConnections())
          .sortBy([ function(p) { return new Date(p.created) } ])
          .reverse()
          .value()
      },
      onItemEdit(item) {
        this.$emit('item-edit', item)
      },
      onItemDelete(item) {
        this.$emit('item-delete', item)
      }
    }
  }
</script>
