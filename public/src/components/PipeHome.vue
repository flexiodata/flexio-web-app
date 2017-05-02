<template>
  <div class="flex flex-column justify-center h-100" v-if="is_fetching">
    <spinner size="large" message="Loading pipe..."></spinner>
  </div>
  <div v-else class="flex flex-column items-stretch" style="background-color: #f9f9f9">
    <pipe-home-header
      class="flex-none pv2a ph3 ph4-l"
      style="background-color: #f3f3f3"
      :pipe-eid="eid"
      :pipe-view="pipe_view"
      :is-prompting="is_prompting"
      :is-process-running="is_process_running"
      @set-pipe-view="setPipeView"
      @run-pipe="runPipe"
      @cancel-process="cancelProcess">
    </pipe-home-header>

    <pipe-transfer
      class="flex-fill pv0 pv4-l pl4-l bt b--black-10 min-h5"
      :project-eid="project_eid"
      :pipe-eid="eid"
      :tasks="tasks"
      @open-builder="showBuilderView"
      v-if="is_transfer_view">
    </pipe-transfer>

    <pipe-builder-list
      class="flex-fill pv4 pl4-l bt b--black-10"
      :id="eid"
      :pipe-eid="eid"
      :tasks="is_prompting ? prompt_tasks : tasks"
      :active-prompt-idx="active_prompt_idx"
      :is-prompting="is_prompting"
      :active-process="active_process"
      :project-connections="project_connections"
      @prompt-value-change="onPromptValueChange"
      @go-prev-prompt="goPrevPrompt"
      @go-next-prompt="goNextPrompt"
      @run-once-with-values="runOnceWithPromptValues"
      @save-values-and-run="savePromptValuesAndRun"
      v-else>
    </pipe-builder-list>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import { ROUTE_PIPEHOME } from '../constants/route'
  import { TASK_TYPE_INPUT, TASK_TYPE_OUTPUT } from '../constants/task-type'
  import { PROCESS_STATUS_RUNNING, PROCESS_MODE_RUN } from '../constants/process'
  import {
    PIPEHOME_VIEW_TRANSFER,
    PIPEHOME_VIEW_BUILDER,
    PIPEHOME_STATUS_CONFIGURE
  } from '../constants/pipehome'
  import Btn from './Btn.vue'
  import Spinner from 'vue-simple-spinner'
  import PipeHomeHeader from './PipeHomeHeader.vue'
  import PipeTransfer from './PipeTransfer.vue'
  import PipeBuilderList from './PipeBuilderList.vue'
  import SetActiveProject from './mixins/set-active-project'

  // try to find variables that match ${...} for each parameter
  const variable_regex = /\$\{((string|boolean|integer|number|connection|column)[ ])?([a-z_-][a-z0-9_-]*)[ ]*(:([^}]*))?\}/gi

  export default {
    mixins: [SetActiveProject],
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
        pipe_view: PIPEHOME_VIEW_TRANSFER,
        prompt_tasks: [],
        active_prompt_idx: 0,
        is_prompting: false
      }
    },
    computed: {
      pipe() {
        return _.get(this.$store, 'state.objects.'+this.eid, {})
      },
      is_fetched() {
        return _.get(this.pipe, 'is_fetched', false)
      },
      is_fetching() {
        return _.get(this.pipe, 'is_fetching', false)
      },
      processes_fetched() {
        return _.get(this.pipe, 'processes_fetched', false)
      },
      tasks() {
        return _.get(this.pipe, 'task', [])
      },
      has_prompt_tasks() {
        return _.filter(this.prompt_tasks, { has_variable: true }).length > 0
      },
      project_eid() {
        return _.get(this.pipe, 'project.eid', '')
      },
      active_process() {
        return _.last(this.getActiveDocumentProcesses())
      },
      is_process_running() {
        return _.get(this.active_process, 'process_status', '') == PROCESS_STATUS_RUNNING
      },
      is_process_run_mode() {
        return _.get(this.active_process, 'process_mode', '') == PROCESS_MODE_RUN
      },
      is_transfer_view() {
        return this.pipe_view == PIPEHOME_VIEW_TRANSFER
      },
      is_builder_view()  {
        return this.pipe_view == PIPEHOME_VIEW_BUILDER
      },
      project_connections() {
        return this.getOurConnections()
      }
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

      if (_.get(this.$route, 'params.view') == PIPEHOME_VIEW_BUILDER)
        this.setPipeView(PIPEHOME_VIEW_BUILDER)
         else
        this.setPipeView(PIPEHOME_VIEW_TRANSFER)
    },
    methods: {
      ...mapGetters([
        'getAllConnections',
        'getActiveDocumentProcesses'
      ]),

      setPipeView(view) {
        if (_.includes([PIPEHOME_VIEW_TRANSFER, PIPEHOME_VIEW_BUILDER], view))
        {
          this.pipe_view = view
          var eid = this.eid
          var params = { eid, view }
          if (view == PIPEHOME_VIEW_BUILDER)
            _.assign(params, { state: _.get(this.$route, 'params.state', undefined) })

          this.$router.replace({ name: ROUTE_PIPEHOME, params })
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

        return _.map(this.tasks, (task) => {
          var params = _.get(task, 'params', {})

          var matched_vars = []

          function getChildVariables(obj, set_key) {
            _.each(obj, (v, k) => {
              variable_idx = _.size(matched_vars)
              variable_set_key = 'prompt_tasks['+task_idx+'].variables['+variable_idx+'].val'

              // recurse over the array to find any variables in it
              if (_.isArray(v))
                return _.each(v, (item, idx) => { getChildVariables(v[idx], set_key+'.'+k+'['+idx+']') })

              // recurse over the object to find any variables in it
              if (_.isObject(v))
                return _.each(v, (v2, k2) => { getChildVariables(v[k2], set_key+'.'+k) })

              // if we find any matches, add them to our set of matched variables
              if (_.isString(v))
              {
                var m

                do {
                  m = variable_regex.exec(v)
                  if (m)
                  {
                    matched_vars.push({
                      task_eid: _.get(task, 'eid'),
                      task_idx,
                      variable_idx,
                      variable_set_key,
                      set_key: set_key+'.'+k,
                      type: m[2],
                      variable_name: m[3],
                      default_val: m[5] || '',
                      val: m[5] || ''
                    })
                  }
                } while (m)
              }
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
              has_variable: true,
              variables: matched_vars
            }, task)
          }
           else
          {
            return task
          }
        })
      },

      runPipe() {
        this.prompt_tasks = [].concat(this.getPromptTasks())
        this.active_prompt_idx = _.findIndex(this.prompt_tasks, { has_variable: true })

        if (this.has_prompt_tasks && !this.is_prompting)
        {
          this.is_prompting = true

          // update the route
          var new_route = _.pick(this.$route, ['eid', 'name', 'params'])
          _.set(new_route, 'params.state', PIPEHOME_STATUS_CONFIGURE)
          this.$router.replace(new_route)

          // make sure the active item is in the view
          setTimeout(() => { this.scrollToTask() }, 1000)
          return
        }

        var attrs = {
          parent_eid: this.eid,
          process_mode: PROCESS_MODE_RUN,
          run: true // this will automatically run the process and start polling the process
        }

        this.$store.dispatch('createProcess', { attrs })
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

      scrollToTask(idx) {
        // default to active task
        idx = _.defaultTo(idx, this.active_prompt_idx)

        var task = _.get(this.tasks, '['+idx+']', null)
        if (_.isNil(task))
          return

        var options = {
            container: '#'+this.eid,
            duration: 400,
            easing: 'ease-out',
            offset: -60
        }

        this.$scrollTo('#'+task.eid, options)
      },

      goPrevPrompt() {
        var start_idx = Math.max(this.active_prompt_idx-1, 0)
        this.active_prompt_idx = _.findLastIndex(this.prompt_tasks, { has_variable: true }, start_idx)
        this.scrollToTask()
      },

      goNextPrompt() {
        var start_idx = Math.min(this.active_prompt_idx+1, _.size(this.prompt_tasks)-1)
        this.active_prompt_idx = _.findIndex(this.prompt_tasks, { has_variable: true }, start_idx)
        this.scrollToTask()
      },

      runOnceWithPromptValues() {
        var variables = this.getAllVariables()

        var run_pipe = _.cloneDeep(this.pipe)

        _.each(variables, (v) => {
          if (_.isNil(v))
            return

          var str = _.get(run_pipe, v.set_key, '')
          var m

          do {
            m = variable_regex.exec(str)
            if (m)
            {
              if (v.variable_name == m[3] /* variable name */)
              {
                str = str.replace(m.input, v.val)
                _.set(run_pipe, v.set_key, str)
              }
            }
          } while (m)
        })

        console.log(run_pipe)
        //this.runPipe()
      },

      savePromptValuesAndRun() {
        alert('TODO')
      }
    }
  }
</script>
