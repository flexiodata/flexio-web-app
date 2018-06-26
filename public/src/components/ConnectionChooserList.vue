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
      v-for="(item, index) in items"
      :item="item"
      :index="index"
      :key="item.eid"
      @item-activate="onItemActivate"
      v-bind="$attrs"
      v-on="$listeners"
    />
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import ConnectionChooserItem from './ConnectionChooserItem.vue'

  export default {
    inheritAttrs: false,
    props: {
      connectionTypeFilter: {
        type: String,
        default: ''
      }
    },
    components: {
      Spinner,
      ConnectionChooserItem
    },
    computed: {
      // mix this into the outer object with the object spread operator
      ...mapState({
        'is_fetching': 'connections_fetching'
      }),
      items() {
        var items = this.getAvailableConnections()
        var connection_type = this.connectionTypeFilter
        return connection_type.length == 0 ? items : _.filter(items, { connection_type })
      }
    },
    methods: {
      ...mapGetters([
        'getAvailableConnections'
      ]),
      onItemActivate(item) {
        this.$emit('item-activate', item)
      }
    }
  }
</script>
