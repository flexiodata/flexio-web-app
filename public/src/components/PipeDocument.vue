<template>
  <!-- fetching -->
  <div v-if="is_fetching">
    <div class="flex flex-row items-center">
      <Spinner size="small" />
      <span class="ml2 f6">Loading...</span>
    </div>
  </div>

  <!-- fetched -->
  <div v-else-if="is_fetched">
    <!-- header -->
    <div class="flex flex-row">
      {{pipe.name}}
    </div>

    <!-- content -->
    <div>
      <pre class="f7 overflow-x-scroll">{{pipe.task}}</pre>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import Spinner from 'vue-simple-spinner'

  export default {
    props: {
      pipeEid: {
        type: String,
        required: true
      }
    },
    components: {
      Spinner
    },
    watch: {
      pipeEid: {
        handler: 'tryFetchPipe',
        immediate: true,
      }
    },
    data() {
      return {
        is_local_fetching: false,
      }
    },
    computed: {
      ...mapState({
        active_team_name: state => state.teams.active_team_name
      }),
      pipe() {
        return _.get(this.$store.state.pipes, `items.${this.pipeEid}`, {})
      },
      is_fetched() {
        return _.get(this.pipe, 'vuex_meta.is_fetched', false)
      },
      is_fetching() {
        return _.get(this.pipe, 'vuex_meta.is_fetching', false) || this.is_local_fetching
      },
    },
    methods: {
      tryFetchPipe() {
        var team_name = this.active_team_name
        var name = this.pipe.name

        if (!this.is_fetched && !this.is_fetching) {
          this.is_local_fetching = true
          this.$store.dispatch('pipes/fetch', { team_name, name }).finally(() => {
            this.is_local_fetching = false
          })
        }
      }
    }
  }
</script>
