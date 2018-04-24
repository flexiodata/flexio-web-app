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
  BUILDER__FETCHING_DEF (state, fetching) {
    state.fetching = fetching

    if (fetching === true)
    {
      state.fetched = false
      state.active_prompt_idx = null
      state.active_prompt = {}
    }
  },

  BUILDER__INIT_DEF (state, def) {
    state.def = def
    state.code = _.get(def, 'pipe', '').trim()

    var prompts = _.get(def, 'prompts', [])

    state.fetched = true
    state.prompts = _.map(prompts, p => {
      _.assign(p, { id: _.uniqueId('prompt-') })

      if (p.element == 'connection-chooser')
        return _.assign(p, { connection_eid: null })

      if (p.element == 'file-chooser')
        return _.assign(p, { connection_eid: null })

      if (p.element == 'form') {
        var form = {}
        _.each(p.form_items, f => { form[f.variable] = f.value })
        return _.assign(p, { form })
      }

      return p
    })

    state.active_prompt_idx = 0
    state.active_prompt = _.get(state.prompts, '['+state.active_prompt_idx+']', {})
  },

  BUILDER__UPDATE_ACTIVE_ITEM (state, attrs) {
    state.active_prompt = _.assign({}, state.active_prompt, attrs)

    var ap = state.active_prompt
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

  BUILDER__UPDATE_CODE (state) {
    var code = state.def.pipe

    _.each(state.prompts, p => {
      var regex = new RegExp("\\$\\{" + p.variable + "\\}", "g")

      switch (p.element) {
        case 'connection-chooser':
          if (!_.isNil(p.connection_eid)) {
            code = code.replace(regex, p.connection_eid)
          }
          break
        case 'file-chooser':
          var path = _.get(p, 'files[0].path', null)
          if (!_.isNil(path)) {
            code = code.replace(regex, path)
          }
          break
        case 'form':
          var form_val = _.get(p, 'form', {})
          _.each(form_val, (k, v) => {
            var regex = new RegExp("\\$\\{" + k + "\\}", "g")
            code = code.replace(regex, v)
          })
          break
        case 'input':
          var val = _.get(p, 'value', '')
          code = code.replace(regex, val)
          break
      }
    })

    state.code = code
  },

  BUILDER__CREATE_PIPE (state, attrs) {
    state.pipe = attrs
  },

  BUILDER__GO_PREV_ITEM (state) {
    state.active_prompt_idx--
    state.active_prompt = _.get(state.prompts, '['+state.active_prompt_idx+']', {})
  },

  BUILDER__GO_NEXT_ITEM (state) {
    state.active_prompt_idx++
    state.active_prompt = _.get(state.prompts, '['+state.active_prompt_idx+']', {})
  }
}

const actions = {}

const getters = {}

export default {
  state,
  mutations,
  actions,
  getters
}

