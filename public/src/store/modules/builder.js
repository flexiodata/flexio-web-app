import _ from 'lodash'

const VFS_TYPE_DIR = 'DIR'

const state = {
  def: {},
  code: '',
  mode: 'prompt', // 'prompt' or 'build'
  pipe: {},
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

  INIT_DEF (state, def) {
    state.def = def
    state.fetched = true

    // determine what was passed to us
    var lang = _.get(def, 'pipe_language', 'json')
    if (lang == 'javascript') {
      var code = _.get(def, 'pipe', '')
      state.code = code
    } else {
      state.code = ''
    }

    var prompts = _.get(def, 'prompts', [])

    // include the summary item in prompt mode
    if (state.mode == 'prompt') {
      var existing_summary = _.find(prompts, { element: 'summary-prompt' })
      if (!existing_summary) {
        prompts.push({ element: 'summary-prompt' })
      }
    }

    state.prompts = _.map(prompts, p => {
      _.assign(p, { id: _.uniqueId('prompt-') })

      if (p.element == 'connection-chooser') {
        return _.assign(p, { connection_eid: null })
      }

      if (p.element == 'file-chooser') {
        return _.assign(p, { connection_eid: null })
      }

      if (p.element == 'form') {
        var form_values = {}
        _.each(p.form_items, f => {
          if (f.element != 'markdown') {
            _.set(form_values, f.variable, f.value)
          }
        })
        return _.assign(p, { form_values })
      }

      return p
    })

    if (state.mode == 'prompt') {
      state.active_prompt_idx = 0
      state.active_prompt = _.get(state.prompts, '['+state.active_prompt_idx+']', {})
    }
  },

  SET_MODE (state, mode) {
    state.mode = mode
  },

  UPDATE_ATTRS (state, attrs) {
    state.attrs = _.assign({}, state.attrs, attrs)

    state.prompts = _.map(state.prompts, p => {
      // update file chooser connections with the associated connection if they match
      if (p.element == 'file-chooser' && p.connection) {
        return _.assign({}, p, {
          connection_eid: _.get(state.attrs, p.connection, '')
        })
      }

      // otherwise, just return the prompt as-is
      return p
    })
  },

  UPDATE_ACTIVE_ITEM (state, attrs) {
    var ap = _.assign({}, state.active_prompt, attrs)
    ap = _.cloneDeep(ap)
    state.active_prompt = ap

    state.prompts = _.map(state.prompts, p => {
      if (p.id == ap.id) {
        return ap
      } else {
        // update file chooser connections with the active prompt's connection if they match
        if (p.element == 'file-chooser' && p.connection && p.connection == ap.variable) {
          return _.assign({}, p, {
            connection_eid: ap.connection_eid,
            files: []
          })
        }
        return p
      }
    })
  },

  UPDATE_CODE (state) {
    var code = state.def.code /* new format */ || state.def.pipe /* old format */ || ''

    _.each(state.prompts, (p, idx) => {
      var regex = new RegExp("\\$\\{" + p.variable + "\\}", "g")

      if (idx > state.active_prompt_idx) {
        return
      }

      switch (p.element) {
        case 'connection-chooser':
          var eid = state.attrs[p.variable]
          if (!_.isNil(eid)) {
            var root_state = this.state
            var connection = _.get(root_state, 'objects[' + eid + ']', null)
            var identifier = _.get(connection, 'alias', '') || _.get(connection, 'eid', '')
            code = code.replace(regex, JSON.stringify(identifier, null, 2))
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

          code = code.replace(regex, JSON.stringify(paths, null, 2))
          break
      }
    })

    _.each(state.attrs, (val, key) => {
      var regex = new RegExp("\\$\\{" + key + "\\}", "g")
      code = code.replace(regex, JSON.stringify(val, null, 2))
    })

    state.code = code
  },

  CREATE_PIPE (state, attrs) {
    state.pipe = attrs
  },

  SET_ACTIVE_ITEM (state, idx) {
    if (idx >= 0 && idx < state.prompts.length) {
      state.active_prompt_idx = idx
      state.active_prompt = _.get(state.prompts, '['+state.active_prompt_idx+']', {})
    }
  },

  UNSET_ACTIVE_ITEM(state) {
    state.active_prompt_idx = -1
    state.active_prompt = {}
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

