<template>
  <div v-if="is_fetching">
    <spinner size="medium" show-text loading-text="Loading pipe..."></spinner>
  </div>
  <div v-else class="flex flex-column items-stretch" style="background-color: #f1f4f5">
    <pipe-home-header
      class="pv2 ph3 ph4-l"
      :pipe-eid="eid"
      :pipe-view="pipe_view"
      :process-running="is_process_running"
      @set-pipe-view="setPipeView"
      @run-pipe="runPipe"
      @cancel-process="cancelProcess">
    </pipe-home-header>

    <pipe-transfer
      class="flex-fill pv0 pv3-l pl4-l bt-l b--black-10"
      :project-eid="project_eid"
      :pipe-eid="eid"
      :tasks="tasks"
      @open-builder="pipe_view = 'builder'"
      v-if="pipe_view == 'transfer'">
    </pipe-transfer>

    <pipe-builder-list
      class="flex-fill pv3 pl4-l bt b--black-10"
      :pipe-eid="eid"
      :tasks="tasks"
      :active-process="active_process"
      :project-connections="project_connections"
      v-else>
    </pipe-builder-list>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import { TASK_TYPE_INPUT, TASK_TYPE_OUTPUT } from '../constants/task-type'
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
    provide() {
      return {
        pipeEid: this.eid
      }
    },
    data() {
      return {
        eid: this.$route.params.eid,
        show_choose_input_modal: false,
        show_add_connection_modal: false,
        pipe_view: 'transfer'
      }
    },
    computed: {
      pipe()              { return _.get(this.$store, 'state.objects.'+this.eid, {}) },
      is_fetched()        { return _.get(this.pipe, 'is_fetched', false) },
      is_fetching()       { return _.get(this.pipe, 'is_fetching', false) },
      processes_fetched() { return _.get(this.pipe, 'processes_fetched', false) },
      tasks()             { return _.get(this.pipe, 'task', []) },
      project_eid()       { return _.get(this.pipe, 'project.eid', '') },

      active_process()       { return _.last(this.getActiveDocumentProcesses()) },
      is_process_running()   { return _.get(this.active_process, 'process_status', '') == PROCESS_STATUS_RUNNING },
      is_process_run_mode()  { return _.get(this.active_process, 'process_mode', '') == PROCESS_MODE_RUN },

      project_connections() { return this.getOurConnections() }
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
        'getAllConnections',
        'getActiveDocumentProcesses'
      ]),

      setPipeView(view) {
        if (_.includes(['transfer', 'builder'], view))
          this.pipe_view = view
      },

      runPipe() {
        var attrs = {
          parent_eid: this.eid,
          process_mode: PROCESS_MODE_RUN,
          run: true // this will automatically run the process and start polling the process
        }

        this.$store.dispatch('createProcess', { attrs })
      },

      cancelProcess() {
        if (this.is_process_running)
        {
          var eid = _.get(this.active_process, 'eid', '')
          this.$store.dispatch('cancelProcess', { eid })
        }
      },

      addInput(attrs, modal) {
        // insert input after any existing inputs
        var input_idx = _.findIndex(this.tasks, (t) => { return _.get(t, 'type') == TASK_TYPE_INPUT })

        // if there are no inputs, insert at the beginning of the pipe
        if (input_idx == -1)
          input_idx = 0

        var eid = this.eid
        var attrs = _.assign({}, _.get(attrs, 'task[0]', {}))
        var attrs = _.omit(attrs, 'eid')

        // add input task
        this.$store.dispatch('createPipeTask', { eid, attrs })

        modal.close()
      },

      getOurConnections() {
        // NOTE: it's really important to include the '_' on the same line
        // as the 'return', otherwise JS will return without doing anything
        return _
          .chain(this.getAllConnections())
          .filter((p) => { return _.get(p, 'project.eid') == this.project_eid })
          .sortBy([ function(p) { return new Date(p.created) } ])
          .reverse()
          .value()
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
        var connections_fetched = _.get(this.$store, 'state.objects.'+this.project_eid+'.connections_fetched', false)
        if (!connections_fetched)
          this.$store.dispatch('fetchConnections', this.project_eid)
      }
    }
  }
</script>
