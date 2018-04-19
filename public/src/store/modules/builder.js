import _ from 'lodash'
import api from '../../api'

const state = {
  def: {},
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

    var prompts = _.get(state, 'def.prompts', [])

    state.fetched = true
    state.prompts = _.map(prompts, p => {
      _.assign(p, { id: _.uniqueId('prompt-') })

      if (p.ui == 'connection-chooser' || p.ui == 'file-chooser')
        return _.assign(p, { connection_eid: null })

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
          return _.assign(p, { connection_eid: ap.connection_eid })
        }
        return p
      }
    })
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

