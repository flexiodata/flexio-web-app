<template>
  <div v-if="is_fetching">
    <div class="pa2">
      <spinner size="small" inline></spinner>
    </div>
  </div>
  <div v-else>
    <pipe-embed-item :item="pipe"></pipe-embed-item>
  </div>
</template>

<script>
  import Spinner from './Spinner.vue'
  import PipeEmbedItem from './PipeEmbedItem.vue'

  export default {
    components: {
      Spinner,
      PipeEmbedItem
    },
    data() {
      return {
        eid: this.$route.params.eid
      }
    },
    computed: {
      pipe() {
        return _.get(this.$store, 'state.objects.'+this.eid)
      },
      is_fetched() {
        return _.get(this.pipe, 'is_fetched', false)
      },
      is_fetching() {
        return _.get(this.pipe, 'is_fetching', true)
      }
    },
    created() {
      this.tryFetchPipe()
    },
    methods: {
      tryFetchPipe() {
        if (!this.is_fetched)
          this.$store.dispatch('fetchPipe', { eid: this.eid })
      }
    }
  }
</script>
