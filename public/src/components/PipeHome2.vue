<template>
  <div v-if="is_fetching">
    <spinner size="medium" show-text loading-text="Loading pipe..."></spinner>
  </div>
  <div v-else class="flex flex-column items-stretch">
    <div class="mt3 pb2 mh5">
      <div class="mb1">
        <div class="dib f3 li-title v-mid dark-gray mr2 v-mid">{{pipe.name}}</div>
        <div class="dib f7 li-title v-mid silver pv1 ph2 bg-black-05">
          <span v-if="pipe.ename.length > 0">{{pipe.ename}}</span>
          <span v-else>Add an alias</span>
        </div>
      </div>
      <div class="f6 lh-title mid-gray">
        <span v-if="pipe.description.length > 0">{{pipe.description}}</span>
        <span class="fw6 black-20" v-else>Add a description</span>
      </div>
    </div>

    <div class="flex-fill flex flex-column">
      <task-list2 :tasks="tasks"></task-list2>
    </div>
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

  const DEFAULT_INSERT_TASK = {
    inserting: true,
    name: 'New Task',
    type: '',
    description: ''
  }

  export default {
    mixins: [setActiveProject],
    components: {
      Btn,
      Spinner,
      TaskList2,
    },
    data() {
      return {
        eid: this.$route.params.eid,
        show_files: true,
        show_content: true,
        show_input_output_modal: false,
        inserting_task: false,
        insert_idx: -1,
        insert_task: {},
        edit_task: {},
        active_task_eid: '',
        active_stream_eid: ''
      }
    },
    watch: {
      // TODO: are there any ill effects to having a watcher
      // on a computed value from a Vuex store?
      raw_tasks: function(val, old_val) {
        this.$nextTick(() => {
          if (val.length == 0)
          {
            // no tasks in the pipe; remove the active item
            this.active_task_eid = ''
          }
           else if (this.active_task_eid.length == 0)
          {
            // make sure we have an active item
            this.active_task_eid = _.get(_.last(val), 'eid', '')
          }
        })
      },
      active_task: function(val, old_val) {
        if (_.get(val, 'type', '') == TASK_TYPE_INPUT)
          this.show_files = true
           else
          this.show_files = false

        // we need to do a deep clone here since we have nested objects in the pipe
        // and Javascript objects keep their reference even when using _.assign()
        this.edit_task = _.cloneDeep(val)
      },
      active_subprocess: function(val, old_val) {
        // right now the input list is actually what drives which stream
        // is shown in the content area so without the input list visible,
        // we need to make sure that still happens
        if (!this.show_files)
        {
          var stream = _.head(this.active_task_inputs)
          this.active_stream_eid = _.get(stream, 'eid', '')
        }
      },
      active_stream_eid: function(val, old_val) {
        // the active stream gets set after a process finishes, so this is
        // a good way of making sure we have the columns for a given task
        this.tryFetchColumns()
      },
      project_eid: function(val, old_val) {
        this.tryFetchConnections()
      }
    },
    computed: {
      pipe()              { return _.get(this.$store, 'state.objects.'+this.eid) },
      is_fetched()        { return _.get(this.pipe, 'is_fetched', false) },
      is_fetching()       { return _.get(this.pipe, 'is_fetching', false) },
      processes_fetched() { return _.get(this.pipe, 'processes_fetched', false) },
      raw_tasks()         { return _.get(this.pipe, 'task', []) },
      project_eid()       { return _.get(this.pipe, 'project.eid', '') },
      project_link()      { return '/project/'+this.project_eid },

      tasks() {
        if (!this.inserting_task)
          return this.raw_tasks

        var head = _.slice(this.raw_tasks, 0, this.insert_idx)
        var to_insert = [this.insert_task]
        var tail = _.slice(this.raw_tasks, this.insert_idx)
        return _.concat([], head, to_insert, tail)
      },

      active_task() {
        var task = _.find(this.tasks, { 'eid': this.active_task_eid })
        return _.isObject(task) ? task : {}
      },

      is_active_task_input()   { return _.get(this.active_task, 'type') == TASK_TYPE_INPUT },
      is_active_task_output()  { return _.get(this.active_task, 'type') == TASK_TYPE_OUTPUT },
      is_active_task_execute() { return _.get(this.active_task, 'type') == TASK_TYPE_EXECUTE },

      active_process()      { return _.last(this.getActiveDocumentProcesses()) },
      is_process_running()  { return _.get(this.active_process, 'process_status', '') == PROCESS_STATUS_RUNNING },
      is_process_run_mode() { return _.get(this.active_process, 'process_mode', '') == PROCESS_MODE_RUN },

      execute_code() {
        var code = _.get(this.edit_task, 'params.code', '')
        try { return atob(code) } catch(e) { return '' }
      },

      execute_lang() {
        return _.get(this.edit_task, 'params.lang', 'python')
      },

      process_task_id() {
        var process_eid = _.get(this.active_process, 'eid', '')
        if (process_eid.length == 0 || this.active_task_eid.length == 0)
          return ''
        return process_eid + '--' + this.active_task_eid
      },

      // find the active subprocess by matching on our active task eid with
      // the task eid of each subprocess
      active_subprocess() {
        var me = this

        return _
          .chain(this.active_process)
          .get('subprocesses')
          .find(function(s) { return _.get(s, 'task.eid') == me.active_task_eid })
          .value()
      },

      is_subprocess_failed() {
        return _.get(this.active_subprocess, 'process_status', '') == PROCESS_STATUS_FAILED
      },

      active_task_inputs() {
        var inputs = _.get(this.active_subprocess, 'output', [])

        // use the inputs specified in the input task
        if (inputs.length == 0 && this.is_active_task_input)
          return _.get(this.active_task, 'params.items', [])

        // ...otherwise, use the output array from the active subprocess
        return inputs
      },

      active_stream() {
        var stream = _.find(this.active_task_inputs, { 'eid': this.active_stream_eid })
        return _.isObject(stream) ? stream : {}
      },

      project_connections() { return this.getOurConnections() },
      task_input_columns()  { return this.getOurInputColumns() },
      task_output_columns() { return this.getOurOutputColumns() }
    },
    created() {
      this.tryFetchPipe()
      this.tryFetchProcesses()
      this.tryFetchConnections()
      this.tryFetchColumns()
    },
    mounted() {
      if (this.active_task_eid.length == 0 && this.raw_tasks.length > 0)
      {
        // make sure we have an active item
        this.active_task_eid = _.get(_.last(this.raw_tasks), 'eid', '')
      }
    },
    methods: {
      ...mapGetters([
        'getActiveDocumentProcesses',
        'getAllConnections',
        'getAllProjects'
      ]),

      tryFetchPipe() {
        var me = this

        if (!this.is_fetched)
        {
          this.$store.dispatch('fetchPipe', { eid: this.eid }).then(response => {
            if (response.ok)
            {
              if (me.project_eid.length > 0)
                me.setActiveProject(me.project_eid)
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
      },

      tryFetchColumns() {
        var process_eid = _.get(this.active_process, 'eid', '')

        if (process_eid.length == 0 || this.process_task_id.length == 0)
          return

        // if we haven't fetched the columns for the active process task, do so now
        var is_fetched = _.get(this.$store, 'state.objects.'+this.process_task_id+'.input_fetched', false)
        if (!is_fetched)
          this.$store.dispatch('fetchProcessTaskInputInfo', { eid: process_eid, task_eid: this.active_task_eid })

        // if we haven't fetched the columns for the active process task, do so now
        var is_fetched = _.get(this.$store, 'state.objects.'+this.process_task_id+'.output_fetched', false)
        if (!is_fetched)
          this.$store.dispatch('fetchProcessTaskOutputInfo', { eid: process_eid, task_eid: this.active_task_eid })
      },

      getOurConnections() {
        var me = this

        // NOTE: it's really important to include the '_' on the same line
        // as the 'return', otherwise JS will return without doing anything
        return _
          .chain(this.getAllConnections())
          .filter(function(p) { return _.get(p, 'project.eid') == me.project_eid })
          .sortBy([ function(p) { return new Date(p.created) } ])
          .reverse()
          .value()
      },

      getOurInputColumns() {
        var columns = _.get(this.$store, 'state.objects.'+this.process_task_id+'.input_columns', [])

        // NOTE: it's really important to include the '_' on the same line
        // as the 'return', otherwise JS will return without doing anything
        return _
          .chain(columns)
          .sortBy([ function(c) { return c.name } ])
          .reverse()
          .value()
      },

      getOurOutputColumns() {
        var columns = _.get(this.$store, 'state.objects.'+this.process_task_id+'.output_columns', [])

        // NOTE: it's really important to include the '_' on the same line
        // as the 'return', otherwise JS will return without doing anything
        return _
          .chain(columns)
          .sortBy([ function(c) { return c.name } ])
          .reverse()
          .value()
      }
    }
  }
</script>
