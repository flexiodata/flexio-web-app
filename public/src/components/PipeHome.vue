<template>
  <div class="flex flex-column justify-center h-100" v-if="is_fetching">
    <spinner size="large" message="Loading pipe..."></spinner>
  </div>

  <div class="flex flex-column justify-center items-center" v-else-if="has_error">
    <div class="f3 mid-gray">{{error_msg}}</div>
    <div class="mt3" v-if="!is_signed_in">
      <router-link :to="signin_route" class="link no-underline dib ttu b br1 white bg-blue darken-10 ph4 pv2a">
        Sign in
      </router-link>
    </div>
  </div>

  <div class="flex flex-column items-stretch bg-nearer-white" v-else>
    <pipe-home-header
      class="flex-none"
      :pipe-options="pipe_options"
      v-bind="pipe_options"
      @set-pipe-view="setPipeView"
      @run-pipe="runPipe"
      @cancel-process="cancelProcess"
    />

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
      v-if="is_builder_view"
    />

    <pipe-code-editor
      :pipe-options="pipe_options"
      v-bind="pipe_options"
      v-else
    />

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
  import { PROCESS_STATUS_RUNNING, PROCESS_MODE_BUILD } from '../constants/process'
  import {
    PIPEHOME_VIEW_JS_SDK,
    PIPEHOME_VIEW_BUILDER,
    PIPEHOME_STATUS_CONFIGURE
  } from '../constants/pipehome'
  import Spinner from 'vue-simple-spinner'
  import PipeHomeHeader from './PipeHomeHeader.vue'
  import PipeCodeEditor from './PipeCodeEditor.vue'
  import PipeBuilderList from './PipeBuilderList.vue'

  export default {
    components: {
      Spinner,
      PipeHomeHeader,
      PipeCodeEditor,
      PipeBuilderList
    },
    provide() {
      return {
        pipeEid: this.eid
      }
    },
    watch: {
      active_process(val, old_val) {
        if (_.get(val, 'eid', '') != _.get(old_val, 'eid', ''))
          this.tryFetchProcessLog()
      },
      is_process_running(val, old_val) {
        if (val === false && old_val === true)
          this.tryFetchProcessLog()
      }
    },
    data() {
      return {
        eid: this.$route.params.eid,
        pipe_view: PIPEHOME_VIEW_JS_SDK,
        prompt_tasks: [],
        active_prompt_idx: 0,
        is_prompting: false
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
          activeProcess: this.active_process
        }
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
        return _.get(this.pipe, 'task', [])
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
      is_process_running() {
        return _.get(this.active_process, 'process_status', '') == PROCESS_STATUS_RUNNING
      },
      is_builder_view()  {
        return this.pipe_view == PIPEHOME_VIEW_BUILDER
      },
      connections() {
        return this.getAllConnections()
      }
    },
    created() {
      this.tryFetchPipe()
      this.tryFetchProcesses()
      this.tryFetchConnections()
      this.setPipeView(PIPEHOME_VIEW_JS_SDK)
    },
    mounted() {
      // only start in configure mode if we have a pipe and it's already been fetched;
      // if not, do this check one the pipe has been fetched in tryFetchPipe()
      if (_.isObject(this.pipe) && _.get(this.pipe, 'is_fetched'))
      {
        // if we're starting in configure mode, start the prompting
        if (_.get(this.$route, 'params.state') == PIPEHOME_STATUS_CONFIGURE)
          this.runPipe()
      }
    },
    methods: {
      ...mapGetters([
        'getAllConnections',
        'getActiveDocumentProcesses'
      ]),

      setPipeView(view) {
        if (_.includes([PIPEHOME_VIEW_JS_SDK, PIPEHOME_VIEW_BUILDER], view))
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
         else if (this.has_prompt_tasks && !this.is_prompting)
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
         else
        {
          var attrs = _.assign({
            parent_eid: this.eid,
            process_mode: PROCESS_MODE_BUILD,
            run: true // this will automatically run the process and start polling the process
          }, attrs)

          this.$store.dispatch('createProcess', { attrs }).then(response => {
            if (response.ok)
            {
              /*
              var eid = this.eid
              var task_types_arr = _.map(this.tasks, (t) => { return _.get(t, 'op', '') })
              var task_types = task_types_arr.join(', ')
              var task_count = _.size(task_types_arr)
              var process_eid = _.get(response.body, 'eid', '')

              analytics.track('Ran Pipe: Start', { eid, process_eid, task_types, task_count })
              */

              this.$nextTick(() => { this.is_prompting = false })
            }
          })
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
            }
             else
            {
              // we weren't able to load the pipe; redirect to the sign in page
              this.$router.push(this.signin_route)
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
          var pipe_eid = this.eid
          var attrs = { tail: 0 }
          this.$store.dispatch('fetchProcesses', { pipe_eid, attrs })
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
      }
    }
  }
</script>
