<template>
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100">
      <Spinner size="large" message="Loading pipes..." />
    </div>
  </div>

  <EmptyItem class="flex-fill justify-center h-100" v-else-if="pipes.length == 0 && filter.length > 0">
    <i slot="icon" class="material-icons">storage</i>
    <span slot="text">No pipes match the filter criteria</span>
  </EmptyItem>

  <EmptyItem class="flex-fill justify-center h-100" v-else-if="pipes.length == 0">
    <i slot="icon" class="material-icons">storage</i>
    <span slot="text">No pipes to show</span>
  </EmptyItem>

  <div v-else>
    <PipeItem
      :is-header="true"
      :item="{}"
      :show-selection-checkbox="false"
      :sort.sync="sort"
      :sort-direction.sync="sort_direction"
      v-if="showHeader"
    />
    <transition-group name="pipe-item">
      <PipeItem
        v-for="(pipe, index) in sorted_pipes"
        :key="pipe.eid"
        :item="pipe"
        :index="index"
        :show-selection-checkbox="false"
        :selected="isItemSelected(pipe.eid)"
        @select="onItemSelect"
        @edit="onItemEdit"
        @duplicate="onItemDuplicate"
        @delete="onItemDelete"
      />
    </transition-group>
    <div class="h4"></div>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import PipeItem from './PipeItem.vue'
  import EmptyItem from './EmptyItem.vue'
  import MixinFilter from './mixins/filter'

  export default {
    props: {
      filter: {
        type: String
      },
      showHeader: {
        type: Boolean,
        default: false
      },
      showSelectionCheckboxes: {
        type: Boolean,
        default: false
      }
    },
    mixins: [MixinFilter],
    components: {
      Spinner,
      PipeItem,
      EmptyItem
    },
    data() {
      return {
        sort: '',
        sort_direction: '',
        selected_items: []
      }
    },
    computed: {
      // mix this into the outer object with the object spread operator
      ...mapState({
        'is_fetching': 'pipes_fetching',
        'is_fetched': 'pipes_fetched',
        'is_summary_fetching': 'process_summary_fetching',
        'is_summary_fetched': 'process_summary_fetched'
      }),
      mapped_pipes() {
        return _.map(this.getAllPipes(), p => {
          return _.assign({}, p, {
            execution_cnt: parseInt(_.get(p, 'stats.total_count', '0'))
          })
        })
      },
      pipes() {
        return this.$_Filter_filter(this.mapped_pipes, this.filter, ['name', 'description'])
      },
      sorted_pipes() {
        if (this.sort.length == 0) {
          return this.pipes
        }

        return _.orderBy(this.pipes, [this.sort], [this.sort_direction])
      }
    },
    created() {
      this.tryFetchPipes()
    },
    methods: {
      ...mapGetters([
        'getAllPipes'
      ]),
      tryFetchPipes() {
        if (!this.is_fetched) {
          this.$store.dispatch('fetchPipes')
        }
        if (!this.is_summary_fetched) {
          this.$store.dispatch('fetchProcessSummary')
        }
      },
      isItemSelected(eid) {
        return _.includes(this.selected_items, eid)
      },
      onItemSelect(eid) {
        if (this.isItemSelected(eid)) {
          this.selected_items = _.without(this.selected_items, eid)
        } else {
          this.selected_items = [].concat(this.selected_items).concat([eid])
        }
        console.log(this.selected_items)
      },
      onItemEdit(item) {
        this.$emit('item-edit', item)
      },
      onItemDuplicate(item) {
        this.$emit('item-duplicate', item)
      },
      onItemDelete(item) {
        this.$emit('item-delete', item)
      }
    }
  }
</script>

<style lang="stylus">
  //.pipe-item-move
  //  transition: transform 0.75s
</style>
