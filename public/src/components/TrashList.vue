<template>
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100">
      <spinner size="large" message="Loading trash..."></spinner>
    </div>
  </div>
  <empty-item v-else-if="trash_items.length == 0 && filter.length > 0">
    <i slot="icon" class="material-icons">delete</i>
    <span slot="text">No trash match the filter criteria</span>
  </empty-item>
  <empty-item v-else-if="trash_items.length == 0">
    <i slot="icon" class="material-icons">delete</i>
    <span slot="text">No trash to show</span>
  </empty-item>
  <div v-else>
    <trash-item
      v-for="(trash_item, index) in trash_items"
      :item="trash_item"
      :index="index">
    </trash-item>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import TrashItem from './TrashItem.vue'
  import EmptyItem from './EmptyItem.vue'
  import CommonFilter from './mixins/common-filter'

  export default {
    props: ['filter'],
    mixins: [CommonFilter],
    components: {
      Spinner,
      TrashItem,
      EmptyItem
    },
    computed: {
      // mix this into the outer object with the object spread operator
      ...mapState({
        'is_fetching': 'trash_fetching',
        'is_fetched': 'trash_fetched'
      }),
      trash_items() {
        return this.commonFilter(this.getOurTrash(), this.filter, ['name', 'description'])
      },
    },
    methods: {
      ...mapGetters([
        'getAllTrash'
      ]),
      getOurTrash() {
        // NOTE: it's really important to include the '_' on the same line
        // as the 'return', otherwise JS will return without doing anything
        return _
          .chain(this.getAllTrash())
          .sortBy([ function(p) { return new Date(p.created) } ])
          .reverse()
          .value()
      }
    }
  }
</script>
