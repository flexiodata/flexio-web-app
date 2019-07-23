import api from '@/api'
import {
  addItem,
  updateItem,
  removeItem,
  updateMeta,
  removeMeta,
} from '@/store/helpers'

const getDefaultMeta = () => {
  return {
    is_fetching: false,
    is_fetched: false
  }
}

const getDefaultState = () => {
  return {
    is_fetching: false,
    is_fetched: false,
    items: {},
  }
}

const state = getDefaultState()

const mutations = {
  'RESET_STATE' (state) {
    _.assign(state, getDefaultState())
  },

  'FETCHING_ITEMS' (state, is_fetching) {
    state.is_fetching = is_fetching
    if (is_fetching === true) {
      state.is_fetched = false
    }
  },

  'FETCHED_ITEMS' (state, items) {
    addItem(state, items, getDefaultMeta())
    state.is_fetched = true
  }
}

const actions = {
  'fetch' ({ commit }, { team_name, eid, name }) {
    // fetching a collection of items
    commit('FETCHING_ITEMS', true)

    return api.v2_fetchTeams(team_name).then(response => {
      commit('FETCHED_ITEMS', response.data)
      commit('FETCHING_ITEMS', false)
      return response
    }).catch(error => {
      commit('FETCHING_ITEMS', false)
      throw error
    })
  },
}

const getters = {
  getAllTeams (state) {
    var items = _.sortBy(state.items, [ function(p) { return new Date(p.created) } ])
    return items.reverse()
  },

  getActiveTeam (state, getters, root_state) {
    var team_name = root_state.active_team_name
    return _.find(getters.getAllTeams, t => t.eid == team_name || t.username == team_name)
  },

  getActiveTeamLabel (state, getters)  {
    var team = getters.getActiveTeam
    var first_name = _.get(team, 'first_name', '')
    var last_name = _.get(team, 'last_name', '')
    return `${first_name} ${last_name}` + "'s team"
  },
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
}

