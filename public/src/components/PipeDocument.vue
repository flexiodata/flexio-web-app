<template>
  <!-- pipe fetching -->
  <div class="bg-nearer-white" v-if="is_fetching">
    <div class="h-100 flex flex-row items-center justify-center">
      <Spinner size="large" message="Loading..." />
    </div>
  </div>

  <!-- pipe fetched -->
  <div class="bg-nearer-white" v-else-if="is_fetched">
    <!-- runtime mode -->
    <div
      v-if="is_runtime"
    >
      Runtime mode...
    </div>

    <!-- build mode -->
    <multipane
      class="vertical-panes"
      layout="vertical"
      v-else
    >
      <div
        class="pane overflow-y-auto"
        :style="{ minWidth: '100px', width: '22%', maxWidth: '40%' }"
      >
        <div>Code</div>
        <el-button @click="active_view = 'run'">Change view</el-button>
      </div>
      <multipane-resizer />
      <div
        class="pane overflow-y-auto"
        :style="{ flexGrow: 1 }"
      >
        <div>Content</div>
      </div>
    </multipane>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import { Multipane, MultipaneResizer } from 'vue-multipane'
  import Spinner from 'vue-simple-spinner'

  const PIPEDOC_VIEW_BUILD  = 'build'
  const PIPEDOC_VIEW_RUN    = 'run'

  export default {
    components: {
      Multipane,
      MultipaneResizer,
      Spinner
    },
    watch: {
      eid: {
        handler: 'loadPipe',
        immediate: true
      },
      active_view: {
        handler: 'updateRoute',
        immediate: true
      }
    },
    data() {
      return {
        active_view: _.get(this.$route, 'params.view', PIPEDOC_VIEW_BUILD)
      }
    },
    computed: {
      ...mapState({
        is_fetching: state => state.pipe.fetching,
        is_fetched: state => state.pipe.fetched
      }),
      eid() {
        return _.get(this.$route, 'params.eid', undefined)
      },
      // if we're in runtime mode or not...
      is_runtime() {
        return this.active_view == PIPEDOC_VIEW_RUN
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
      },
      updateRoute() {
        // update the route
        var new_route = _.pick(this.$route, ['name', 'meta', 'params', 'path'])
        var view = this.active_view
        _.set(new_route, 'params.view', view)
        this.$router.replace(new_route)
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .vertical-panes
    width: 100%
    height: 100%

  .vertical-panes > .pane ~ .pane
    border-left: 1px solid #ddd
</style>
