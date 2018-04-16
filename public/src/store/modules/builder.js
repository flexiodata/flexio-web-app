import _ from 'lodash'
import api from '../../api'

const state = {
  def: {},
  title: '',
  prompts: [],
  active_prompt: {},
  active_prompt_idx: null,
  fetching: false,
  fetched: false
}

const mutations = {
  BUILDER__FETCH_DEF (state, fetching) {
    state.fetching = fetching
  },

  BUILDER__INIT_DEF (state, def) {
    state.def = def

    var prompts = _.get(state, 'def.prompts', [])

    state.title = state.def.title
    state.prompts = _.map(prompts, p => {
      if (p.ui == 'connection-chooser')
        return _.assign(p, { connection_eid: null })

      return p
    })
    state.active_prompt_idx = 0
    state.active_prompt = _.get(state.prompts, '['+state.active_prompt_idx+']', {})
  },

  BUILDER__UPDATE_ACTIVE_ITEM (state, attrs) {
    state.active_prompt = _.assign({}, state.active_prompt, attrs)

    var idx = 0
    state.prompts = _.map(state.prompts, p => {
      if (idx == state.active_prompt_idx) {
        idx++
        return state.active_prompt
      } else {
        idx++
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

