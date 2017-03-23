<template>
  <div v-if="is_fetching">
    <spinner size="medium" show-text loading-text="Loading pipe..."></spinner>
  </div>
  <div v-else class="flex flex-column items-stretch">
    <pipe-home-header
      class="mv3 mh5"
      :pipe-eid="eid"
      :pipe-view="pipe_view"
      :process-running="is_process_running"
      @set-pipe-view="setPipeView">
    </pipe-home-header>

    <pipe-transfer
      class="flex-fill mh5"
      :tasks="tasks"
      @open-builder="pipe_view = 'builder'"
      v-if="pipe_view == 'transfer'">
    </pipe-transfer>

    <pipe-builder-list
      class="flex-fill"
      :tasks="tasks"
      v-else>
    </pipe-builder-list>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import { PROCESS_STATUS_RUNNING, PROCESS_MODE_RUN } from '../constants/process'
  import setActiveProject from './mixins/set-active-project'

  import Btn from './Btn.vue'
  import Spinner from './Spinner.vue'
  import PipeHomeHeader from './PipeHomeHeader.vue'
  import PipeTransfer from './PipeTransfer.vue'
  import PipeBuilderList from './PipeBuilderList.vue'

  export default {
    mixins: [setActiveProject],
    components: {
      Btn,
      Spinner,
      PipeHomeHeader,
      PipeTransfer,
      PipeBuilderList
    },
    data() {
      return {
        eid: this.$route.params.eid,
        pipe_view: 'transfer'
      }
    },
    computed: {
      pipe()              { return _.get(this.$store, 'state.objects.'+this.eid, {}) },
      pipe_name()         { return _.get(this.pipe, 'name', '') },
      pipe_ename()        { return _.get(this.pipe, 'ename', '') },
      pipe_description()  { return _.get(this.pipe, 'description', '') },
      is_fetched()        { return _.get(this.pipe, 'is_fetched', false) },
      is_fetching()       { return _.get(this.pipe, 'is_fetching', false) },
      processes_fetched() { return _.get(this.pipe, 'processes_fetched', false) },
      tasks()             { return _.get(this.pipe, 'task', []) },
      project_eid()       { return _.get(this.pipe, 'project.eid', '') },

      active_process()       { return _.last(this.getActiveDocumentProcesses()) },
      is_process_running()   { return _.get(this.active_process, 'process_status', '') == PROCESS_STATUS_RUNNING },
      is_process_run_mode()  { return _.get(this.active_process, 'process_mode', '') == PROCESS_MODE_RUN }
    },
    watch: {
      project_eid: function(val, old_val) {
        this.tryFetchConnections()
      }
    },
    created() {
      this.tryFetchPipe()
      this.tryFetchProcesses()
      this.tryFetchConnections()
    },
    methods: {
      ...mapGetters([
        'getActiveDocumentProcesses'
      ]),

      setPipeView(view) {
        if (_.includes(['transfer', 'builder'], view))
          this.pipe_view = view
      },

      tryFetchPipe() {
        if (!this.is_fetched)
        {
          this.$store.dispatch('fetchPipe', { eid: this.eid }).then(response => {
            if (response.ok)
            {
              if (this.project_eid.length > 0)
                this.setActiveProject(this.project_eid)
            }
             else
            {
              // TODO: add error handling
            }
          })
        }
      },

      tryFetchProcesses() {
        // this will fetch all of the processes for this pipe; doing so will update the processes
        // in the global store, which will in turn cause the getActiveDocumentProcesses()
        // getter on the store to change, which will cause the computed 'active_process' value
        // to be updated which will propogate to any derived computed values to change which
        // will cause our component to update
        if (!this.processes_fetched)
          this.$store.dispatch('fetchProcesses', this.eid)
      },

      tryFetchConnections() {
        if (this.project_eid.length == 0)
          return

        // if we haven't fetched the columns for the active process task, do so now
        var is_fetched = _.get(this.$store, 'state.objects.'+this.project_eid+'.connections_fetched', false)
        if (!is_fetched)
          this.$store.dispatch('fetchConnections', this.project_eid)
      }
    }
  }
</script>
