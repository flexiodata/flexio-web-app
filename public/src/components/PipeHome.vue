<template>
  <div class="flex flex-column justify-center h-100" v-if="is_fetching">
    <spinner size="large" message="Loading pipe..." />
  </div>

  <div class="flex flex-column bg-nearer-white" v-else>
    <pipe-home-header
      class="flex-none"
      :pipe-options="pipe_options"
      v-bind="pipe_options"
      @pipe-view-change="setPipeView"
      @run-pipe="runPipe"
      @cancel-process="cancelProcess"
    />

    <div class="flex-fill flex flex-column justify-center items-center" v-if="has_error">
      <div class="f3 mid-gray">{{error_msg}}</div>
      <div class="mt3">
        <el-button type="primary" size="large" class="ttu b" @click="signOutAndRedirect">Sign in as another user</el-button>
      </div>
    </div>

    <div class="flex-fill pv4 overflow-y-auto" v-else-if="is_signed_in">
      <div class="center" style="max-width: 1440px">
        <ui-alert
          class="mb3 mh4"
          style="width: auto; box-shadow: 0 2px 4px -2px rgba(0,0,0,0.4)"
          type="success"
          :dismissible="false"
          @dismiss="show_success = false"
          v-show="show_success">
          {{success_message}}
        </ui-alert>
      </div>

      <div class="center" style="max-width: 1440px">
        <ui-alert
          class="mb3 mh4"
          style="width: auto; box-shadow: 0 2px 4px -2px rgba(0,0,0,0.4)"
          type="error"
          @dismiss="show_error = false"
          v-show="show_error">
          {{error_message}}
        </ui-alert>
      </div>

      <div class="flex flex-column justify-center h-100" v-if="is_process_running">
        <spinner size="large" message="Running pipe..." />
      </div>

      <!--
      <pipe-builder-list
        class="flex-fill"
        style="padding-bottom: 16rem"
        :pipe-options="pipe_options"
        v-bind="pipe_options"
        @prompt-value-change="onPromptValueChange"
        @go-prev-prompt="goPrevPrompt"
        @go-next-prompt="goNextPrompt"
        @run-once-with-values="runOnceWithPromptValues"
        @save-values-and-run="savePromptValuesAndRun"
        v-else-if="is_builder_view"
      />
      -->

      <pipe-code-editor
        ref="code"
        class="mv3 ph4 center"
        style="max-width: 1440px"
        :pipe-options="pipe_options"
        v-bind="pipe_options"
        v-else
      />

      <div
        class="mv3 ph4 center"
        style="max-width: 1440px"
        v-if="is_process_failed && is_superuser"
      >
        <div class="bg-white ba b--black-10">
          <CodeEditor2
            class="overflow-y-auto"
            style="max-height: 24rem"
            lang="application/json"
            :options="{
              lineNumbers: false,
              readOnly: true
            }"
            v-model="active_process_info_str"
          />
        </div>
      </div>

      <help-items class="mv3" />
    </div>

    <ui-snackbar-container
      ref="snackbar-container"
      position="center"
    />

<pre ref="last-prompt" v-show="false">
<div class="flex flex-row items-center justify-center">
  <i class="material-icons f2 mr2 dark-green">check_circle</i><span class="f2">Your pipe is ready to be run!</span>
</div>
----
### You've got options...
  - Run the pipe once with the values you've chosen, but keep the placeholder variables for the next time the pipe is run.
  - Save the values you've chosen and always run the pipe with those values.
</pre>

  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import { ROUTE_SIGNIN, ROUTE_PIPES } from '../constants/route'
  import { VARIABLE_REGEX } from '../constants/common'
  import { TASK_OP_COMMENT } from '../constants/task-op'
  import { TASK_INFO_COMMENT } from '../constants/task-info'
  import {
    PROCESS_STATUS_RUNNING,
    PROCESS_STATUS_FAILED,
    PROCESS_STATUS_COMPLETED,
    PROCESS_MODE_BUILD
  } from '../constants/process'
  import {
    PIPEHOME_VIEW_SDK_JS,
    PIPEHOME_VIEW_BUILDER,
    PIPEHOME_STATUS_CONFIGURE,
    PIPEHOME_STATUS_RUN
  } from '../constants/pipehome'
  import Spinner from 'vue-simple-spinner'
  import PipeHomeHeader from './PipeHomeHeader.vue'
  import PipeCodeEditor from './PipeCodeEditor.vue'
  //import PipeBuilderList from './PipeBuilderList.vue'
  import CodeEditor2 from './CodeEditor2.vue'
  import HelpItems from './HelpItems.vue'

  export default {
    components: {
      Spinner,
      PipeHomeHeader,
      PipeCodeEditor,
      //PipeBuilderList,
      CodeEditor2,
      HelpItems
    },
    provide() {
      return {
        pipeEid: this.eid
      }
    },
    watch: {
      is_process_running(val, old_val) {
        if (val === false && old_val === true)
          this.tryFetchProcessLog()
      },
      active_process(val, old_val) {
        if (_.get(val, 'eid', '') != _.get(old_val, 'eid', ''))
          this.tryFetchProcessLog()
      },
      active_process_status: function(val, old_val) {
        if (val == PROCESS_STATUS_RUNNING)
        {
          this.show_error = false
          this.show_success = false
        }
         else if (old_val == PROCESS_STATUS_RUNNING)
        {
          if (val == PROCESS_STATUS_COMPLETED)
          {
            setTimeout(() => { this.show_success = false }, 4000)

            setTimeout(() => {
              this.show_success = true
              this.show_error = false
            }, 200)
          }

          if (val == PROCESS_STATUS_FAILED)
          {
            this.error_message = _.get(this.active_process_info, 'error.message', '')

            //this.message =
            setTimeout(() => {
              this.show_success = false
              this.show_error = true
            }, 200)
          }
        }
      }
    },
    data() {
      return {
        eid: this.$route.params.eid,
        pipe_view: PIPEHOME_VIEW_SDK_JS,
        prompt_tasks: [],
        active_prompt_idx: 0,
        is_prompting: false,
        show_success: false,
        show_error: false,
        fetch_error: false,
        success_message: 'The pipe was run successfully!',
        error_message: 'An error occurred while running the pipe.'
      }
    },
    computed: {
      ...mapState([
        'active_user_eid',
        'connections_fetching',
        'connections_fetched'
      ]),
      pipe() {
        return _.get(this.$store, 'state.objects.'+this.eid, {})
      },
      pipe_options() {
        return {
          pipeView: this.pipe_view,
          tasks: this.is_prompting ? this.prompt_tasks : this.tasks,
          connections: this.connections,
          isPrompting: this.is_prompting,
          isProcessRunning: this.is_process_running,
          activePromptIdx: this.active_prompt_idx,
          activeProcess: this.active_process,
          fetchError: this.fetch_error
        }
      },
      is_superuser() {
        // limit to @flex.io users for now
        var user_email = _.get(this.getActiveUser(), 'email', '')
        return _.includes(user_email, '@flex.io')
      },
      is_fetched() {
        return _.get(this.pipe, 'is_fetched', false)
      },
      is_fetching() {
        return _.get(this.pipe, 'is_fetching', false)
      },
      is_signed_in() {
        return this.active_user_eid.length > 0
      },
      has_error() {
        return _.get(this.pipe, 'error.code', '').length > 0
      },
      error_msg() {
        return _.get(this.pipe, 'error.message', '')
      },
      signin_route() {
        return {
          name: ROUTE_SIGNIN,
          query: { redirect: this.$route.fullPath }
        }
      },
      processes_fetched() {
        return _.get(this.pipe, 'processes_fetched', false)
      },
      tasks() {
        var tasks = _.get(this.pipe, 'task.params.items', [])

        // take into account the old pipe syntax with the task array (for now)
        if (tasks.length == 0)
          tasks = _.get(this.pipe, 'task', [])

        // for new pipes that are created after the migration
        // to sequences, the task node is an empty object;
        // in this scenario, use an empty array (for now)
        if (_.isPlainObject(tasks))
          tasks = []

        return tasks
      },
      empty_tasks() {
        return _.filter(this.tasks, (t) => { return _.isNil(_.get(t, 'op')) })
      },
      has_empty_tasks() {
        return this.empty_tasks.length > 0
      },
      has_prompt_tasks() {
        // even if we have tasks with `is_prompt` set to true, only show
        // the prompting mode if we actually have variables to fill in
        return _.filter(this.prompt_tasks, { has_variables: true }).length > 0
      },
      active_process() {
        return _.last(this.getActiveDocumentProcesses())
      },
      active_process_status() {
        return _.get(this.active_process, 'process_status', '')
      },
      active_process_info() {
        return _.get(this.active_process, 'process_info', {})
      },
      active_process_info_str() {
        return JSON.stringify(this.active_process_info, null, 2)
      },
      is_process_running() {
        return this.active_process_status == PROCESS_STATUS_RUNNING
      },
      is_process_failed() {
        return this.active_process_status == PROCESS_STATUS_FAILED
      },
      is_builder_view()  {
        return this.pipe_view == PIPEHOME_VIEW_BUILDER
      },
      connections() {
        return this.getAvailableConnections()
      }
    },
    created() {
      this.tryFetchPipe()
      this.tryFetchProcesses()
      this.tryFetchConnections()
      //this.setPipeView(PIPEHOME_VIEW_SDK_JS)
    },
    mounted() {
      // only start in configure mode if we have a pipe and it's already been fetched;
      // if not, do this check once the pipe has been fetched in tryFetchPipe()
      if (_.isObject(this.pipe) && _.get(this.pipe, 'is_fetched'))
      {
        // if we're starting in configure mode, start the prompting
        if (_.get(this.$route, 'params.state') == PIPEHOME_STATUS_CONFIGURE)
          this.runPipe()

        // if we're starting in run mode, run the pipe
        if (_.get(this.$route, 'params.state') == PIPEHOME_STATUS_RUN)
          this.runPipe()
      }
    },
    methods: {
      ...mapGetters([
        'getAvailableConnections',
        'getActiveDocumentProcesses',
        'getActiveUser'
      ]),

      setPipeView(view) {
        /*
        if (_.includes([PIPEHOME_VIEW_SDK_JS, PIPEHOME_VIEW_BUILDER], view))
        {
          // don't strip off query string
          var query = _.get(this.$route, 'query', undefined)

          this.pipe_view = view
          var eid = this.eid
          var params = { eid, view }

          // if we're in the builder view, make sure we try to include the state
          if (view == PIPEHOME_VIEW_BUILDER)
            _.assign(params, { state: _.get(this.$route, 'params.state', undefined) })

          this.$router.replace({ name: ROUTE_PIPES, params, query })
        }
        */
      },

      showBuilderView() {
        this.setPipeView(PIPEHOME_VIEW_BUILDER)
      },

      getPromptTasks() {
        var task_idx = 0
        var variable_idx = 0
        var variable_set_key = ''
        var set_key = ''

        var prompts = _.map(this.tasks, (task) => {
          var task_eid = _.get(task, 'eid')
          var params = _.get(task, 'params', {})

          var matched_vars = []

          function getVariable(val, set_key) {
            var m

            do {
              m = VARIABLE_REGEX.exec(val)
              if (m)
              {
                variable_idx = _.size(matched_vars)
                variable_set_key = 'prompt_tasks['+task_idx+'].variables['+variable_idx+'].val'

                matched_vars.push({
                  task_eid,
                  task_idx,
                  variable_idx,
                  variable_set_key,
                  set_key,
                  type: m[2],
                  variable_name: m[3],
                  default_val: m[5] || '',
                  val: m[5] || ''
                })
              }
            } while (m)
          }

          function getChildVariables(obj, set_key) {
            // if the object is a string, try to find a variable in it
            if (_.isString(obj))
              return getVariable(obj, set_key)

            _.each(obj, (v, k) => {
              // recurse over the array to find any variables in it
              if (_.isArray(v))
                return _.each(v, (item, idx) => { getChildVariables(v[idx], set_key+'.'+k+'['+idx+']') })

              // recurse over the object to find any variables in it
              if (_.isObject(v))
                return _.each(v, (v2, k2) => { getChildVariables(v[k2], set_key+'.'+k) })

              // if we find any matches, add them to our set of matched variables
              if (_.isString(v))
                return getVariable(v, set_key+'.'+k)
            })
          }

          // start traversing the task params object
          getChildVariables(params, 'task['+task_idx+'].params')

          // increment our task index
          task_idx++

          // add the array of variables to the output
          if (matched_vars.length > 0)
          {
            return _.assign({
              is_prompt: true,
              has_variables: true,
              variables: matched_vars,
              error_msg: ''
            }, task)
          }
           else if (_.get(task, 'op') == TASK_OP_COMMENT)
          {
            return _.assign({ is_prompt: true }, task)
          }
           else
          {
            return task
          }
        })

        // add the final step for running the pipe
        prompts.push(_.assign({
          is_prompt: true,
          description: this.$refs['last-prompt'].innerHTML,
          // this is required so the pipe builder item
          // has an 'id' anchor for the scrolling to work
          eid: _.uniqueId('last-prompt-step-')
        }, TASK_INFO_COMMENT))

        return prompts
      },

      runPipe(attrs) {
        this.prompt_tasks = [].concat(this.getPromptTasks())
        this.active_prompt_idx = _.findIndex(this.prompt_tasks, { is_prompt: true })

        if (this.has_empty_tasks)
        {
          var first_empty_task = _.head(this.empty_tasks)
          var eid = _.get(first_empty_task, 'eid')

          this.showSnackbarMessage({
            message: 'The pipe contains empty steps. Please remove the empty steps from the pipe in order to run it.',
            action: 'Remove',
            onActionClick: this.removeEmptySteps
          })

          // make sure the empty item is in the view
          setTimeout(() => { this.scrollToTask(eid) }, 1000)
        }
         else if (false /* don't prompt anymore (for now) */ && this.has_prompt_tasks && !this.is_prompting)
        {
          this.is_prompting = true

          // update the route
          var new_route = _.pick(this.$route, ['eid', 'name', 'params'])
          _.set(new_route, 'params.state', PIPEHOME_STATUS_CONFIGURE)
          this.$router.replace(new_route)

          // make sure the active item is in the view
          setTimeout(() => { this.scrollToPromptTask() }, 1000)
          return
        }
         else if (this.$refs['code'].is_changed)
        {
          // TODO: this shouldn't be done imperatively
          var xhr = this.$refs['code'].saveChanges()

          if (!_.isNil(xhr))
            xhr.then(response => { this.runPipe() })
        }
         else
        {
          var edit_code = this.$refs['code'].getEditCode()

          this.$store.track('Ran Pipe', {
            title: _.get(this.pipe, 'name', ''),
            code: edit_code
          })

          var attrs = _.assign({
            parent_eid: this.eid,
            process_mode: PROCESS_MODE_BUILD,
            run: true // this will automatically run the process and start polling the process
          }, attrs)

          this.$store.dispatch('createProcess', { attrs }).then(response => {
            if (response.ok)
            {
              this.$nextTick(() => { this.is_prompting = false })
            }
          })
        }

        // make sure we remove the 'run' state
        if (_.get(this.$route, 'params.state') == PIPEHOME_STATUS_RUN) {
          var params = _.omit(_.get(this.$route, 'params', {}), ['state'])
          var query = _.get(this.$route, 'query', undefined)
          this.$router.replace({ name: ROUTE_PIPES, params, query })
        }
      },

      cancelProcess() {
        // exit prompt mode
        if (this.is_prompting)
          this.is_prompting = false

        // update the route
        var new_route = _.pick(this.$route, ['eid', 'name', 'params'])
        _.set(new_route, 'params.state', null)
        this.$router.replace(new_route)

        // cancel the process if it is still running
        if (this.is_process_running)
        {
          var eid = _.get(this.active_process, 'eid', '')
          this.$store.dispatch('cancelProcess', { eid })
        }
      },

      tryFetchPipe() {
        if (!this.is_fetched)
        {
          this.$store.dispatch('fetchPipe', { eid: this.eid }).then(response => {
            if (response.ok)
            {
              // if we're starting in configure mode, start the prompting
              if (_.get(this.$route, 'params.state') == PIPEHOME_STATUS_CONFIGURE)
                this.runPipe()

              // if we're starting in run mode, run the pipe
              if (_.get(this.$route, 'params.state') == PIPEHOME_STATUS_RUN)
                this.runPipe()
            }
             else
            {
              // we weren't able to load the pipe; redirect to the sign in page
              //this.$router.push(this.signin_route)
              this.fetch_error = true
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
        {
          var parent_eid = this.eid
          var attrs = { parent_eid, tail: 0 }
          this.$store.dispatch('fetchProcesses', { attrs })
        }
      },

      tryFetchProcessLog() {
        var eid = _.get(this.active_process, 'eid', '')
        if (eid.length > 0)
          this.$store.dispatch('fetchProcessLog', { eid })
      },

      tryFetchConnections() {
        if (!this.connections_fetched && !this.connections_fetching)
          this.$store.dispatch('fetchConnections')
      },

      getAllVariables() {
        return _.reduce(this.prompt_tasks, (result, task, index) => {
          return result.concat(_.get(task, 'variables', []))
        }, [])
      },

      // use the variable set key provided to set the appropriate value in the prompt task JSON
      onPromptValueChange(val, variable_set_key) {
        _.set(this, variable_set_key, val)
      },

      scrollToPromptTask(idx) {
        // default to active task
        idx = _.defaultTo(idx, this.active_prompt_idx)

        var task = _.get(this.prompt_tasks, '['+idx+']', null)
        if (_.isNil(task))
          return

        this.scrollToTask(_.get(task, 'eid', null))
      },

      scrollToTask(dom_id) {
        if (!_.isString(dom_id))
          return

        var options = {
            container: '#'+this.eid,
            duration: 400,
            easing: 'ease-out',
            offset: -60
        }

        this.$scrollTo('#'+dom_id, options)
      },

      validatePrompt(task_eid) {
        var prompt = _.find(this.prompt_tasks, { eid: task_eid })
        var prompt_vars = _.filter(this.getAllVariables(), { task_eid })

        var empty_vars = _.filter(prompt_vars, (v) => {
          return _.size(_.get(v, 'val', '')) == 0
        })

        prompt.error_msg = ''

        if (_.size(empty_vars) > 0)
          prompt.error_msg = 'One or more required values are missing'

        return _.size(empty_vars) == 0
      },

      goPrevPrompt() {
        var start_idx = Math.max(this.active_prompt_idx-1, 0)
        this.active_prompt_idx = _.findLastIndex(this.prompt_tasks, { is_prompt: true }, start_idx)
        this.scrollToPromptTask()
      },

      goNextPrompt(task_eid) {
        if (!this.validatePrompt(task_eid))
          return

        var start_idx = Math.min(this.active_prompt_idx+1, _.size(this.prompt_tasks)-1)
        this.active_prompt_idx = _.findIndex(this.prompt_tasks, { is_prompt: true }, start_idx)
        this.scrollToPromptTask()
      },

      // returns a cloned pipe with all of the variables replaced with values
      getRunPipe() {
        var variables = this.getAllVariables()

        var run_pipe = _.cloneDeep(this.pipe)

        _.each(variables, (v) => {
          if (_.isNil(v))
            return

          var str = _.get(run_pipe, v.set_key, '')
          var m

          do {
            m = VARIABLE_REGEX.exec(str)
            if (m)
            {
              if (v.variable_name == m[3] /* variable name */)
              {
                str = str.replace(m[0], v.val)
                _.set(run_pipe, v.set_key, str)
              }
            }
          } while (m)
        })

        return run_pipe
      },

      removeEmptySteps() {
        var task = _.differenceWith(this.tasks, this.empty_tasks, _.isEqual)
        var attrs = { task }

        this.$store.dispatch('updatePipe', { eid: this.eid, attrs }).then(response => {
          if (response.ok)
          {
            this.showSnackbarMessage({
              message: 'The empty steps have been removed from the pipe. Would you like to run the pipe now?',
              action: 'Run',
              onActionClick: this.runPipe
            })
          }
        })
      },

      runOnceWithPromptValues() {
        var run_pipe = this.getRunPipe()
        this.runPipe(_.pick(run_pipe, 'task'))
      },

      savePromptValuesAndRun() {
        var run_pipe = this.getRunPipe()
        var attrs = _.pick(run_pipe, 'task')

        this.$store.dispatch('updatePipe', { eid: this.eid, attrs }).then(response => {
          if (response.ok)
          {
            this.runPipe(_.pick(run_pipe, 'task'))
          }
           else
          {
            // TODO: add error handling
          }
        })
      },

      showSnackbarMessage(options) {
        setTimeout(() => {
          this.$refs['snackbar-container'].createSnackbar(_.assign({
            actionColor: 'primary',
            duration: 7500
          }, options))
        }, 50)
      },

      signOutAndRedirect() {
        this.$store.dispatch('signOut').then(response => {
          //if (response.ok)
            //this.$router.push(this.signin_route)
        })
      }
    }
  }
</script>
