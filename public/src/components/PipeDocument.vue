<template>
  <!-- pipe fetching -->
  <div class="bg-nearer-white" v-if="is_fetching">
    <div class="h-100 flex flex-row items-center justify-center">
      <Spinner size="large" message="Loading..." />
    </div>
  </div>

  <!-- pipe fetched -->
  <div class="bg-nearer-white" v-else-if="is_fetched">
    <div class="h-100 flex flex-row items-center justify-center">
      Pipe Document
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import Spinner from 'vue-simple-spinner'

  export default {
    components: {
      Spinner
    },
    watch: {
      eid: {
        handler: 'loadPipe',
        immediate: true
      }
    },
    computed: {
      ...mapState({
        is_fetching: state => state.pipe.fetching,
        is_fetched: state => state.pipe.fetched
      }),
      eid() {
        return _.get(this.$route, 'params.eid', undefined)
      }
    },
    methods: {
      loadPipe() {
        this.$store.commit('pipe/FETCHING_PIPE', true)

        this.$store.dispatch('fetchPipe', { eid: this.eid }).then(response => {
          if (response.ok) {
            var pipe = response.data
            this.$store.commit('pipe/INIT_PIPE', pipe)
            this.$store.commit('pipe/FETCHING_PIPE', false)
          } else {
            this.$store.commit('pipe/FETCHING_PIPE', false)
          }
        })
      }
    }
  }
</script>

<style lang="stylus" scoped>
</style>
