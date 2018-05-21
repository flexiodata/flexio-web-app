<template>
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100">
      <spinner size="large" message="Loading pipes..."></spinner>
    </div>
  </div>
  <empty-item class="flex-fill justify-center h-100" v-else-if="pipes.length == 0 && filter.length > 0">
    <i slot="icon" class="material-icons">storage</i>
    <span slot="text">No pipes match the filter criteria</span>
  </empty-item>
  <empty-item class="flex-fill justify-center h-100" v-else-if="pipes.length == 0">
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
      @embed="onItemEmbed"
      @schedule="onItemSchedule"
      @deploy="onItemDeploy"
      @delete="onItemDelete"
    />
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
      'filter': {
        type: String
      },
      'show-header': {
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
    computed: {
      // mix this into the outer object with the object spread operator
      ...mapState({
        'is_fetching': 'pipes_fetching',
        'is_fetched': 'pipes_fetched'
      }),
      pipes() {
        return this.$_Filter_filter(this.getAllPipes(), this.filter, ['name', 'description'])
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
        if (!this.is_fetched)
          this.$store.dispatch('fetchPipes')
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
      onItemEmbed(item) {
        this.$emit('item-embed', item)
      },
      onItemSchedule(item) {
        this.$emit('item-schedule', item)
      },
      onItemDeploy(item) {
        this.$emit('item-deploy', item)
      },
      onItemDelete(item) {
        this.$emit('item-delete', item)
      }
    }
  }
</script>
