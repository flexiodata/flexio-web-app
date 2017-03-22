<template>
  <div v-if="is_fetching">
    <spinner size="medium" show-text loading-text="Loading pipe..."></spinner>
  </div>
  <div v-else class="flex flex-column items-stretch">
    <div class="flex flex-row mv3 mh5">
      <div class="flex-fill">
        <div class="mb1">
          <div class="dib f3 li-title v-mid dark-gray mr2 v-mid">{{pipe_name}}</div>
          <div class="dib f7 li-title v-mid silver pv1 ph2 bg-black-05">
            <span v-if="pipe_ename.length > 0">{{pipe_ename}}</span>
            <span v-else>Add an alias</span>
          </div>
        </div>
        <div class="f6 lh-title gray">
          <span v-if="pipe_description.length > 0">{{pipe_description}}</span>
          <span class="fw6 black-20" v-else>Add a description</span>
        </div>
      </div>
      <div class="flex-none flex flex-row items-center">
        <div
          class="f6 fw6 blue pointer mr3"
          @click="is_transfer_view = false"
          v-if="is_transfer_view"
        >
          Use Builder View
        </div>
        <div
          class="f6 fw6 blue pointer mr3"
          @click="is_transfer_view = true"
          v-else
        >
          Use Transfer View
        </div>

        <btn
          btn-md
          btn-primary
          class="ttu b"
          v-if="is_process_running && is_process_run_mode"
        >
          Cancel
        </btn>
        <btn
          btn-md
          btn-primary
          class="ttu b"
          v-else
        >
          Run
        </btn>
      </div>
    </div>

    <div class="flex-fill mh5" :tasks="tasks" v-if="is_transfer_view">Transer view goes here...</div>
    <task-list2 class="flex-fill" :tasks="tasks" v-else></task-list2>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import { OBJECT_STATUS_AVAILABLE } from '../constants/object-status'
  import { CONNECTION_TYPE_HTTP } from '../constants/connection-type'
  import { TASK_TYPE_INPUT, TASK_TYPE_OUTPUT, TASK_TYPE_EXECUTE } from '../constants/task-type'
  import { PROCESS_STATUS_RUNNING, PROCESS_STATUS_FAILED, PROCESS_MODE_BUILD, PROCESS_MODE_RUN } from '../constants/process'
  import setActiveProject from './mixins/set-active-project'

  import Btn from './Btn.vue'
  import Spinner from './Spinner.vue'
  import TaskList2 from './TaskList2.vue'

  export default {
    mixins: [setActiveProject],
    components: {
      Btn,
      Spinner,
      TaskList2
    },
    data() {
      return {
        eid: this.$route.params.eid,
        is_transfer_view: true
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
