import _ from 'lodash'
import { ROUTE_BUILDER } from '../../constants/route'

const VFS_TYPE_DIR = 'DIR'

const state = {
  route: '',
  def: {},
  task: {},
  pipe: {},
  process: {},
  prompts: [],
  attrs: {},
  active_prompt: {},
  active_prompt_idx: -1,
  fetching: false,
  fetched: false
}

const mutations = {
  FETCHING_DEF (state, fetching) {
    state.fetching = fetching

    if (fetching === true) {
      state.fetched = false
      state.active_prompt_idx = -1
      state.active_prompt = {}
    }
  },

  INIT_ROUTE (state, route) {
    state.route = route
  },

  INIT_DEF (state, def) {
    state.def = def
    state.fetched = true

    // get prompts from definition
    var prompts = _.get(def, 'ui.prompts', [])

    // replace name element with form element
    var existing_name = _.findIndex(prompts, { element: 'name' })
    if (existing_name != -1) {
      prompts[existing_name] = {
        element: 'form',
        element_identifier: 'name',
        title: 'Name your pipe',
        cls: 'el-form--cozy el-form__label-tiny',
        form_items: [{
          element: 'input',
          type: 'text',
          variable: 'pipe.name',
          label: 'Name',
          placeholder: 'Name',
          value: _.get(def, 'name', '')
        },{
          element: 'input',
          type: 'textarea',
          variable: 'pipe.description',
          label: 'Description',
          placeholder: 'Description (optional)',
          value: ''
        }]
      }
    }

    // if we're in the builder, remove all runtime-only steps and add a summary step
    if (state.route == ROUTE_BUILDER) {
      prompts = _.reject(prompts, { runtime_only: true })

      var existing_summary = _.find(prompts, { element: 'summary' })
      if (!existing_summary) {
        prompts.push({ element: 'summary' })
      }
    }

    // map prompt objects
    state.prompts = _.map(prompts, p => {
      // necessary for scrollTo
      _.assign(p, { id: _.uniqueId('prompt-') })

      // add `connection_eid` attribute to connection chooser elements
      if (p.element == 'connection-chooser') {
        return _.assign(p, { connection_eid: null })
      }

      // add `connection_eid` attribute to file chooser elements
      if (p.element == 'file-chooser') {
        return _.assign(p, { connection_eid: null })
      }

      // add `form_values` object to form elements
      if (p.element == 'form') {
        var form_values = {}
        _.each(p.form_items, fi => {
          if (fi.element != 'markdown') {
            _.set(form_values, fi.variable, fi.value)
          }
        })
        return _.assign(p, { form_values })
      }

      return p
    })

    // reset task object
    try {
      var task = _.get(def, 'task', {})
      task = _.cloneDeep(task)
      state.task = _.assign({}, task)
    }
    catch (e) {

    }

    // reset attributes
    state.attrs = {}

    // reset active prompt
    state.active_prompt_idx = 0
    state.active_prompt = _.get(state.prompts, '['+state.active_prompt_idx+']', {})
  },

  UPDATE_ATTRS (state, attrs) {
    var new_attrs = _.cloneDeep(attrs)

    // extend the pipe object, don't overwrite it
    if (_.isObject(new_attrs, 'pipe')) {
      new_attrs.pipe = _.assign({}, _.get(state.attrs, 'pipe', {}), new_attrs.pipe)
    }

    state.attrs = _.assign({}, state.attrs, new_attrs)

    state.prompts = _.map(state.prompts, p => {
      // update all file chooser element connections
      // with the associated connection if they match
      if (p.element == 'file-chooser' && p.connection) {
        return _.assign({}, p, {
          connection_eid: _.get(state.attrs, p.connection, '')
        })
      }

      // otherwise, just return the prompt as-is
      return p
    })
  },

  UPDATE_TASK (state) {
    var task = _.get(state.def, 'task', {})
    var task_str = JSON.stringify(task, null, 2)

    _.each(state.prompts, (p, idx) => {
      var regex = new RegExp("\"?\\$\\{" + p.variable + "\\}\"?", "g")

      if (idx > state.active_prompt_idx) {
        return
      }

      switch (p.element) {
        case 'connection-chooser':
          var identifier = state.attrs[p.variable]
          if (_.isString(identifier)) {
            /*
            var root_state = this.state
            var connection = _.get(root_state, 'objects[' + eid + ']', null)
            var identifier = _.get(connection, 'alias', '') || _.get(connection, 'eid', '')
            */
            task_str = task_str.replace(regex, JSON.stringify(identifier))
          }
          break

        case 'file-chooser':
          var files = state.attrs[p.variable]
          var connection_alias = _.get(p, 'connection_alias', false)
          var paths = []

          // for file selection file choosers, append '/*' to any folders that are selected
          if (p.folders_only !== true) {
            files = _.map(files, (f) => {
              var path = f.type === VFS_TYPE_DIR ? f.path + '/*' : f.path
              var remote_path = f.type === VFS_TYPE_DIR ? f.remote_path + '/*' : f.remote_path
              return _.assign({}, f, { path, remote_path })
            })
          }

          if (connection_alias) {
            // '.connect()' alias is specified; use it instead of the connection eid/alias
            paths = _.map(files, (f) => { return _.get(f, 'remote_path', null) })
            paths = _.compact(paths)
            paths = _.map(paths, (p) => { return '/' + connection_alias + p })
          } else {
            // no connection alias specified; convert array of file objects into an array of paths
            paths = _.map(files, (f) => { return _.get(f, 'path', null) })
            paths = _.compact(paths)
          }

          // for single-select file choosers, don't output an array
          if (p.allow_multiple === false) {
            paths = _.get(paths, '[0]', '')
          }

          task_str = task_str.replace(regex, JSON.stringify(paths))
          break
      }
    })

    _.each(state.attrs, (val, key) => {
      var regex = new RegExp("\\$\\{" + key + "\\}", "g")
      task_str = task_str.replace(regex, JSON.stringify(val))
    })

    try {
      state.task = _.assign({}, JSON.parse(task_str))
    }
    catch (e) {

    }
  },

  ADD_SUMMARY_PROMPT (state) {
    var existing = _.find(state.prompts, { element: 'summary' })
    if (!existing) {
      var prompt = { element: 'summary' }
      state.prompts = [].concat(state.prompts, [prompt])
    }
  },

  ADD_RESULT_PROMPT (state, attrs) {
    var prompt = _.assign({ element: 'result' }, attrs)
    state.prompts = [].concat(state.prompts, [prompt])
  },

  REMOVE_RESULT_PROMPT (state) {
    var idx = _.findIndex(state.prompts, { element: 'result' })
    if (idx != -1) {
      var prompts = [].concat(state.prompts)
      prompts.splice(idx, 1)
      state.prompts = prompts
    }
  },

  CREATE_PIPE (state, attrs) {
    state.pipe = attrs
  },

  CREATE_PROCESS (state, attrs) {
    state.process = attrs

    var prompts = [].concat(state.prompts)
    var result_step = _.find(prompts, { element: 'result' })
    var result_step_idx = _.findIndex(prompts, { element: 'result' })

    // assign the process to the result step
    var new_result_step = _.cloneDeep(result_step)
    new_result_step = _.assign({
      element: 'result',
      id: _.uniqueId('prompt-')
    }, new_result_step, { process_eid: attrs.eid })

    if (result_step_idx == -1) {
      // insert the result step
      prompts.splice(state.active_prompt_idx, 0, new_result_step)
    } else {
      // replace the existing result step
      prompts.splice(result_step_idx, 1, new_result_step)
    }

    state.prompts = prompts
  },

  GO_PREV_ITEM (state) {
    state.active_prompt_idx--
    state.active_prompt = _.get(state.prompts, '['+state.active_prompt_idx+']', {})
  },

  GO_NEXT_ITEM (state) {
    state.active_prompt_idx++
    state.active_prompt = _.get(state.prompts, '['+state.active_prompt_idx+']', {})
  }
}

const actions = {}

const getters = {}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}

