import _ from 'lodash'
import api from '../../api'

const state = {
  def: {},
  code: '',
  pipe: {},
  prompts: [],
  active_prompt: {},
  active_prompt_idx: null,
  fetching: false,
  fetched: false
}

const mutations = {
  FETCHING_DEF (state, fetching) {
    state.fetching = fetching

    if (fetching === true)
    {
      state.fetched = false
      state.active_prompt_idx = null
      state.active_prompt = {}
    }
  },

  INIT_DEF (state, def) {
    state.def = def
    state.code = _.get(def, 'pipe', '').trim()

    var prompts = _.get(def, 'prompts', [])

    // always include the summary item
    prompts.push({ element: 'summary-page' })

    state.fetched = true
    state.prompts = _.map(prompts, p => {
      _.assign(p, { id: _.uniqueId('prompt-') })

      if (p.element == 'connection-chooser')
        return _.assign(p, { connection_eid: null })

      if (p.element == 'file-chooser')
        return _.assign(p, { connection_eid: null })

      if (p.element == 'form') {
        var form_values = {}
        _.each(p.form_items, f => { form_values[f.variable] = f.value })
        return _.assign(p, { form_values })
      }

      return p
    })

    state.active_prompt_idx = 0
    state.active_prompt = _.get(state.prompts, '['+state.active_prompt_idx+']', {})
  },

  UPDATE_ACTIVE_ITEM (state, attrs) {
    var ap = _.assign({}, state.active_prompt, attrs)
    ap = _.cloneDeep(ap)
    state.active_prompt = ap

    state.prompts = _.map(state.prompts, p => {
      if (p.id == ap.id) {
        return ap
      } else {
        // update any prompts that want to reference
        // the active prompt's connection
        if (p.connection == ap.variable) {
          return _.assign(p, {
            connection_eid: ap.connection_eid,
            files: []
          })
        }
        return p
      }
    })
  },

  UPDATE_CODE (state) {
    var code = state.def.pipe

    _.each(state.prompts, (p, idx) => {
      var regex = new RegExp("\\$\\{" + p.variable + "\\}", "g")

      if (idx > state.active_prompt_idx)
        return

      switch (p.element) {
        case 'connection-chooser':
          var eid = p.connection_eid
          if (!_.isNil(eid)) {
            var root_state = this.state
            var connection = _.get(root_state, 'objects[' + eid + ']', null)
            var identifier = _.get(connection, 'alias', '') || _.get(connection, 'eid', '')
            code = code.replace(regex, JSON.stringify(identifier, null, 2))
          }
          break
        case 'file-chooser':
          var files = _.get(p, 'files', [])
          var connection_alias = _.get(p, 'connection_alias', false)
          var paths = _.map(files, (f) => { return _.get(f, 'path', null) })
          paths = _.compact(paths)

          // '.connect()' alias is specified; use it instead of the connection eid/alias
          if (connection_alias) {
            paths = _.map(files, (f) => { return _.get(f, 'remote_path', null) })
            paths = _.compact(paths)
            paths = _.map(paths, (p) => { return '/' + connection_alias + p })
          }

          // for single-select file choosers, don't output an array
          if (p.allow_multiple === false) {
            paths = _.get(paths, '[0]', '')
          }

          code = code.replace(regex, JSON.stringify(paths, null, 2))
          break
        case 'form':
          var form_vals = _.get(p, 'form_values', {})
          _.each(form_vals, (val, key) => {
            var regex = new RegExp("\\$\\{" + key + "\\}", "g")
            code = code.replace(regex, JSON.stringify(val, null, 2))
          })
          break
      }
    })

    state.code = code
  },

  CREATE_PIPE (state, attrs) {
    state.pipe = attrs
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

