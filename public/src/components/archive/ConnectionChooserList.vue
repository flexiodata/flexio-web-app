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
      :item="item"
      :index="index"
      :key="item.eid"
      @item-activate="onItemActivate"
      v-bind="$attrs"
      v-on="$listeners"
      v-for="(item, index) in items"
    />
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import ConnectionChooserItem from '@/components/ConnectionChooserItem'

  export default {
    inheritAttrs: false,
    props: {
      filterBy: {
        type: Function
      }
    },
    components: {
      Spinner,
      ConnectionChooserItem
    },
    computed: {
      // mix this into the outer object with the object spread operator
      ...mapState({
        is_fetching: state => state.connections.is_fetching,
      }),
      items() {
        var conns = this.getAvailableConnections()
        return this.filterBy ? _.filter(conns, this.filterBy) : conns
      }
    },
    methods: {
      ...mapGetters('connections', {
        'getAvailableConnections': 'getAvailableConnections'
      }),
      onItemActivate(item) {
        this.$emit('item-activate', item)
      }
    }
  }
</script>
