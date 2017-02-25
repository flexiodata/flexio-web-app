<template>
  <div v-if="is_fetching">
    <spinner size="medium" show-text loading-text="Loading pipe..."></spinner>
  </div>
  <div v-else class="flex flex-column items-stretch mh2 mh3-m mh5-ns">

    <!-- pipe header -->
    <div class="flex flex-row flex-none pt2">
        <router-link
          :to="project_link"
          class="flex-none link dib f5 f4-m f3-l fw6 ph1 ph2-ns v-mid black-60 hint--bottom-right"
          aria-label="Back to project home"
        >
        <i class="material-icons v-mid f2">home</i>
        </router-link>
      <div class="flex-fill flex flex-column justify-center">
        <span class="dib f4 f3-ns fw6 lh-title truncate mr2 black-60">{{pipe.name}}</span>
      </div>
      <div class="flex-none">
        <btn
          btn-md
          btn-primary
          class="ttu b v-mid"
          @click="cancelProcess"
          v-if="is_process_running && is_process_run_mode"
        >
          Cancel Process
        </btn>
        <btn
          btn-md
          btn-primary
          class="ttu b v-mid"
          @click="runPipe"
          v-else
        >
          Run pipe
        </btn>

        <a
          class="v-mid dib pointer fw6 bg-black-10 black-60 f6 pv1 ph2 br1 darken-10 hint--bottom-left"
          aria-label="Open menu"
          ref="dropdownTrigger"
          tabindex="0"
        ><i class="material-icons v-mid">menu</i></a>

        <ui-popover
          trigger="dropdownTrigger"
          ref="dropdown"
          dropdown-position="bottom right"
        >
          <ui-menu
            contain-focus
            has-icons

            :options="[{
              id: 'file-list',
              label: 'Toggle file list',
              icon: 'list'
            },{
              id: 'content',
              label: 'Toggle content',
              icon: 'description'
            }]"

            @select="onDropdownItemClick"
            @close="$refs.dropdown.close()"
          ></ui-menu>
        </ui-popover>
      </div>
    </div>

    <!-- v-if; running in release mode (entire pipe) -->
    <div
      class="flex-fill flex flex-column mb3 relative"
      v-if="is_process_running && is_process_run_mode"
    >
      <process-progress-list
        class="w-60 center mv3"
        :process="active_process"
      ></process-progress-list>
    </div>

    <!-- v-else; not running entire pipe -->
    <div
      class="flex-fill flex flex-column"
      v-else
    >
      <!-- task list -->
      <div class="flex-none flex flex-row pv2 overflow-x-auto">
        <task-list
          :tasks="tasks"
          :active-task-eid="active_task_eid"
          :is-inserting="inserting_task"
          @ghost-input-item-activate="startInputInsert"
          @ghost-output-item-activate="startOutputInsert"
          @add-task-item-activate="startTaskInsert"
          @item-insert="insertTaskAtIndex"
          @item-cancel-insert="endTaskInsert"
          @item-activate="setActiveTask"
          @item-delete="deleteTask">
        </task-list>
      </div>

      <!-- command bar -->
      <command-bar
        ref="commandbar"
        :is-inserting="inserting_task"
        :orig-json="active_task"
        :task-json="inserting_task ? insert_task : edit_task"
        :connections="project_connections"
        :input-columns="task_input_columns"
        :output-columns="task_output_columns"
        @show-input-file-chooser="openInputFileChooserModal"
        @cancel="inserting_task ? endTaskInsert() : revertTaskChanges()"
        @save="saveTask"
      ></command-bar>

      <!-- content area -->
      <div class="flex-fill flex flex-column mb3 relative">

        <!-- v-if; running in build mode (from adding/editing a task step) -->
        <div v-if="is_process_running && !is_process_run_mode" class="mb3 relative flex flex-column flex-fill">
          <spinner size="medium" show-text loading-text="Running pipe..."></spinner>
        </div>

        <!-- v-else-if; process failed -->
        <div v-else-if="is_process_failed" class="flex flex-column flex-fill justify-center tc mt3 dark-red ba b--black-10 overflow-y-auto">
          <div>
            <i class="material-icons v-mid f1 db">error</i>
            <p>An error occured while running the pipe</p>
            <btn
              btn-md
              btn-primary
              btn-outline
              class="ttu b v-mid dib"
              @click="sendErrorReport"
            >
              Send Error Report
            </btn>
          </div>
        </div>

        <!-- v-if; active process is finished -->
        <div v-else class="flex flex-row flex-fill pt3">
          <div
            class="flex flex-fill overflow-auto file-list"
            style="flex: 1 1 120px"
            v-if="show_files && tasks.length != 0 && active_task_inputs.length > 0 && !is_active_task_execute"
          >
            <input-list
              :inputs="active_task_inputs"
              :show-checkbox="is_active_task_input && false"
              :full-list="!show_content"
              @item-activate="setActiveInput"
            >
            </input-list>
          </div>
          <code-editor
            ref="code"
            class="flex-fill ba b--black-20 relative pipe-content"
            style="flex: 5 1"
            :val="execute_code"
            :mode="execute_lang"
            @change="updateExecuteJson"
            v-if="show_content && is_active_task_execute"
          ></code-editor>
          <pipe-content
            class="flex-fill relative pipe-content"
            style="flex: 5 1"
            :stream-eid="active_stream_eid"
            :task-json="edit_task"
            v-else-if="show_content && tasks.length != 0 && active_task_inputs.length > 0"
          ></pipe-content>
        </div>
      </div>
    </div>

    <!-- choose input/output modal -->
    <pipe-props-modal
      ref="modal-choose-input-output"
      :project-eid="project_eid"
      @add-connection="openConnectionModal"
      @submit="autofillTask"
      @hide="show_input_output_modal = false"
      v-if="show_input_output_modal"
    ></pipe-props-modal>

    <!-- connection modal -->
    <connection-props-modal
      ref="modal-add-connection"
      :project-eid="project_eid"
      @submit="tryAddConnection"
    ></connection-props-modal>

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
  import TaskList from './TaskList.vue'
  import CommandBar from './CommandBar.vue'
  import InputList from './InputList.vue'
  import CodeEditor from './CodeEditor.vue'
  import PipeContent from './PipeContent.vue'
  import ProcessProgressList from './ProcessProgressList.vue'
  import PipePropsModal from './PipePropsModal.vue'
  import ConnectionPropsModal from './ConnectionPropsModal.vue'

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
      TaskList,
      CommandBar,
      InputList,
      CodeEditor,
      PipeContent,
      ProcessProgressList,
      PipePropsModal,
      ConnectionPropsModal
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
      is_process_failed()   { return _.get(this.active_process, 'process_status', '') == PROCESS_STATUS_FAILED },
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

      toggleFiles() {
        this.show_files = !this.show_files
      },

      toggleContent() {
        this.show_content = !this.show_content
      },

      openInputFileChooserModal() {
        if (!this.inserting_task && !this.is_active_task_input)
          this.insertTaskAtIndex(this.active_task)

        this.show_input_output_modal = true
        this.$nextTick(() => { this.$refs['modal-choose-input-output'].open({ mode: 'choose-input' }) })
      },

      openOutputFileChooserModal() {
        if (!this.inserting_task && !this.is_active_task_output)
          this.insertTaskAtIndex(this.active_task)

        this.show_input_output_modal = true
        this.$nextTick(() => { this.$refs['modal-choose-input-output'].open({ mode: 'choose-output' }) })
      },

      openConnectionModal() {
        this.$refs['modal-choose-input-output'].close()
        this.$refs['modal-add-connection'].open()
      },

      autofillTask(attrs, modal) {
        if (this.inserting_task)
          this.insert_task = _.assign({}, _.get(attrs, 'task[0]', {}))
           else
          this.edit_task = _.assign({}, _.get(attrs, 'task[0]', {}))

        modal.close()

        this.$refs['commandbar'].focus()
      },

      tryAddConnection(attrs, modal) {
        var me = this
        var eid = attrs.eid
        attrs = _.pick(attrs, ['name', 'ename', 'description', 'token', 'host', 'port', 'username', 'password', 'database'])
        _.extend(attrs, { eid_status: OBJECT_STATUS_AVAILABLE })

        // update the connection and make it available
        this.$store.dispatch('updateConnection', { eid, attrs }).then(response => {
          if (response.ok)
          {
            // close the connection modal
            modal.close()

            // try to connect to the connection
            me.$store.dispatch('testConnection', { eid, attrs })

            // re-open the input file chooser modal and set its connection
            me.show_input_output_modal = true
            me.$refs['modal-choose-input-output']
              .open({ mode: me.is_active_task_input ? 'choose-input' : 'choose-output' })
              .setConnection(response.body)
          }
           else
          {
            // TODO: add error handling
          }
        })
      },

      startInputInsert(idx) {
        if (_.isNil(idx) || idx == -1)
          idx = 0

        // if there are no inputs, this is a ghost item; show the input chooser
        var input_idx = _.findIndex(this.tasks, (t) => { return _.get(t, 'type') == TASK_TYPE_INPUT })

        this.inserting_task = true
        this.insert_idx = idx
        this.insert_task = _.extend({}, DEFAULT_INSERT_TASK, {
          type: TASK_TYPE_INPUT,
          params: {
            connection: ''
          },
          metadata: {
            name: 'New Input'
          }
        })

        this.$refs['commandbar'].focus()

        if (input_idx == -1)
          this.openInputFileChooserModal()
      },

      startOutputInsert(idx) {
        if (_.isNil(idx) || idx == -1)
          idx = this.raw_tasks.length

        // if there are no outputs, this is a ghost item; show the output chooser
        var output_idx = _.findIndex(this.tasks, (t) => { return _.get(t, 'type') == TASK_TYPE_OUTPUT })

        this.inserting_task = true
        this.insert_idx = idx
        this.insert_task = _.extend({}, DEFAULT_INSERT_TASK, {
          type: TASK_TYPE_OUTPUT,
          params: {
            connection: ''
          },
          metadata: {
            name: 'New Output'
          }
        })

        this.$refs['commandbar'].focus()

        if (output_idx == -1)
          this.openOutputFileChooserModal()
      },

      startTaskInsert(idx) {
        if (_.isNil(idx) || idx == -1)
          idx = _.findLastIndex(this.raw_tasks, { type: TASK_TYPE_OUTPUT })

        this.inserting_task = true
        this.insert_idx = idx >= 0 ? idx : this.raw_tasks.length
        this.insert_task = _.extend({}, DEFAULT_INSERT_TASK, {
          metadata: {
            name: 'New Task'
          }
        })

        this.$refs['commandbar'].focus()
      },

      insertTaskAtIndex(item) {
        var idx = _.findIndex(this.raw_tasks, { eid: _.get(item, 'eid') })
        var task_type = _.get(item, 'type')

        if (idx >= 0)
        {
          if (task_type == TASK_TYPE_INPUT)
            this.startInputInsert(idx+1)
             else if (task_type == TASK_TYPE_OUTPUT)
            this.startOutputInsert(idx+1)
             else
            this.startTaskInsert(idx+1)
        }
         else
        {
          this.startTaskInsert(-1)
        }
      },

      endTaskInsert() {
        this.inserting_task = false
        this.insert_idx = -1
        this.insert_task = {}
      },

      revertTaskChanges() {
        this.edit_task = _.cloneDeep(this.active_task)

        var cm = this.$refs['code']
        if (cm)
          cm.setValue(this.execute_code)
      },

      updateExecuteJson(val) {
        // encode as base64
        if (val.length > 0)
          val = btoa(val)

        var tmp = _.assign({}, this.edit_task)
        _.set(tmp, 'params.code', val)
        this.edit_task = _.assign({}, tmp)
      },

      setActiveTask(item) {
        this.active_task_eid = _.get(item, 'eid', '')
      },

      setActiveInput(item) {
        this.active_stream_eid = _.get(item, 'eid', '')
      },

      deleteTask(item) {
        var me = this
        var item_idx = _.findIndex(this.raw_tasks, { eid: item.eid })
        var eid = this.eid
        var task_eid = item.eid

        this.$store.dispatch('deletePipeTask', { eid, task_eid }).then(response => {
          if (response.ok)
          {
            // we deleted the active task; set a new active task
            if (item.eid == me.active_task_eid)
            {
              if (item_idx >= me.raw_tasks.length)
                item_idx--

              var task = _.get(this.raw_tasks, '['+item_idx+']', {})
              me.active_task_eid = _.get(task, 'eid')
            }
          }
           else
          {
            // TODO: add error handling
          }
        })
      },

      saveTask(attrs) {
        // add connection type to input task metadata
        if (_.get(attrs, 'type') == TASK_TYPE_INPUT ||
            _.get(attrs, 'type') == TASK_TYPE_OUTPUT)
        {
          var cidentifier = _.get(attrs, 'params.connection', '')

          if (cidentifier.length > 0)
          {
            // lookup the related connection
            var c = _.find(this.getOurConnections(), (c) => {
              return _.get(c, 'eid') == cidentifier || _.get(c, 'ename') == cidentifier
            })

            var ctype = _.get(c, 'connection_type', '')

            // set task metadata
            _.set(attrs, 'metadata.connection_type', ctype)
          }
           else
          {
            // if we can't find the connection, default to web link (for now)
            _.set(attrs, 'metadata.connection_type', CONNECTION_TYPE_HTTP)
          }
        }

        if (this.inserting_task || this.raw_tasks.length == 0)
          this.createTask(attrs)
           else
          this.updateTask(attrs)
      },

      createTask(attrs) {
        var me = this

        // make sure that our insert position is always a positive number
        if (this.insert_idx == -1)
          this.insert_idx = this.raw_tasks.length

        // add the insert index to the JSON we'll send to the server
        attrs.index = this.insert_idx

        var eid = this.eid
        var attrs = _.omit(attrs, 'eid')

        // edit pipe task
        this.$store.dispatch('createPipeTask', { eid, attrs }).then(response => {
          if (response.ok)
          {
            var is_execute = _.get(attrs, 'type') == TASK_TYPE_EXECUTE

            // set the active task eid
            var task_eid = _.get(response.body, 'eid', '')
            if (task_eid.length > 0)
              me.active_task_eid = task_eid

            me.$store.dispatch('createProcess', {
              attrs: {
                parent_eid: me.eid,
                process_mode: PROCESS_MODE_BUILD,
                run: is_execute ? false : true // this will automatically run the process and start polling the process
              }
            })

            // start another insert unless on an execute step
            // which will go into edit mode for the code editor
            if (is_execute)
              me.endTaskInsert()
               else
              me.startTaskInsert(me.insert_idx+1)
          }
           else
          {
            // TODO: add error handling
          }
        })
      },

      updateTask(attrs) {
        var me = this

        var eid = this.eid
        var task_eid = attrs.eid
        var attrs = _.omit(attrs, 'eid')

        // edit pipe task
        this.$store.dispatch('updatePipeTask', { eid, task_eid, attrs }).then(response => {
          if (response.ok)
          {
            me.$store.dispatch('createProcess', {
              attrs: {
                parent_eid: me.eid,
                process_mode: PROCESS_MODE_BUILD,
                run: true // this will automatically run the process and start polling the process
              }
            })
          }
           else
          {
            // TODO: add error handling
          }
        })
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

      sendErrorReport() {
        alert('TODO')
      },

      onDropdownItemClick(menu_item) {
        switch (menu_item.id)
        {
          case 'file-list': return this.toggleFiles()
          case 'content':   return this.toggleContent()
        }
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

<style lang="less">
  .file-list + .pipe-content {
    margin-left: 1rem;
  }
</style>
