<template>
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100">
      <spinner size="large" message="Loading pipes..."></spinner>
    </div>
  </div>
  <empty-item v-else-if="pipes.length == 0 && filter.length > 0">
    <i slot="icon" class="material-icons">storage</i>
    <span slot="text">No pipes match the filter criteria</span>
  </empty-item>
  <empty-item v-else-if="pipes.length == 0">
    <i slot="icon" class="material-icons">storage</i>
    <span slot="text">No pipes to show</span>
  </empty-item>
  <div v-else>
    <pipe-item
      v-for="(pipe, index) in pipes"
      :item="pipe"
      :index="index"
      @edit="onItemEdit"
      @duplicate="onItemDuplicate"
      @share="onItemShare"
      @schedule="onItemSchedule"
      >
    </pipe-item>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import PipeItem from './PipeItem.vue'
  import EmptyItem from './EmptyItem.vue'
  import CommonFilter from './mixins/common-filter'

  export default {
    props: ['filter', 'project-eid'],
    mixins: [CommonFilter],
    components: {
      Spinner,
      PipeItem,
      EmptyItem
    },
    computed: {
      pipes() {
        return this.commonFilter(this.getOurPipes(), this.filter, ['name', 'description'])
      },
      is_fetching() {
        return _.get(_.find(this.getAllProjects(), { eid: this.projectEid }), 'pipes_fetching', true)
      }
    },
    methods: {
      ...mapGetters([
        'getAllPipes',
        'getAllProjects'
      ]),
      getOurPipes() {
        var project_eid = this.projectEid

        // NOTE: it's really important to include the '_' on the same line
        // as the 'return', otherwise JS will return without doing anything
        return _
          .chain(this.getAllPipes())
          .sortBy([ function(p) { return new Date(p.created) } ])
          .reverse()
          .value()
      },
      onItemEdit(item) {
        this.$emit('item-edit', item)
      },
      onItemDuplicate(item) {
        this.$emit('item-duplicate', item)
      },
      onItemShare(item) {
        this.$emit('item-share', item)
      },
      onItemSchedule(item) {
        this.$emit('item-schedule', item)
      }
    }
  }
</script>
