<template>
  <div v-if="is_fetching">
    <spinner size="medium" show-text loading-text="Loading pipe..."></spinner>
  </div>
  <div v-else class="flex flex-column items-stretch">
    <pipe-home-header
      class="pv3 mh5 bb b--black-10"
      :pipe-eid="eid"
      :pipe-view="pipe_view"
      :process-running="is_process_running"
      @set-pipe-view="setPipeView">
    </pipe-home-header>

    <pipe-transfer
      class="flex-fill ph5-l ph3-m"
      :project-eid="project_eid"
      :tasks="tasks"
      @choose-input-connection="openInputFileChooserModal"
      @open-builder="pipe_view = 'builder'"
      v-if="pipe_view == 'transfer'">
    </pipe-transfer>

    <pipe-builder-list
      class="flex-fill"
      :pipe-eid="eid"
      :tasks="tasks"
      v-else>
    </pipe-builder-list>

    <!-- choose input modal -->
    <pipe-props-modal
      ref="modal-choose-input"
      :project-eid="project_eid"
      @add-connection="openAddConnectionModal"
      @submit="addInput"
      @hide="show_choose_input_modal = false"
      v-if="show_choose_input_modal"
    ></pipe-props-modal>

    <!-- add connection modal -->
    <connection-props-modal
      ref="modal-add-connection"
      :project-eid="project_eid"
      @hide="show_add_connection_modal = false"
      v-if="show_add_connection_modal"
    ></connection-props-modal>
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
  import PipePropsModal from './PipePropsModal.vue'
  import ConnectionPropsModal from './ConnectionPropsModal.vue'

  export default {
    mixins: [setActiveProject],
    components: {
      Btn,
      Spinner,
      PipeHomeHeader,
      PipeTransfer,
      PipeBuilderList,
      PipePropsModal,
      ConnectionPropsModal
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

      openInputFileChooserModal() {
        this.show_choose_input_modal = true
        this.$nextTick(() => { this.$refs['modal-choose-input'].open({ mode: 'choose-input' }) })
      },

      openAddConnectionModal() {
        this.show_add_connection_modal = true
        this.$refs['modal-choose-input'].close()
        this.$nextTick(() => { this.$refs['modal-add-connection'].open() })
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
