import api from '@/api'
import {
  addItem,
  updateItem,
  removeItem,
  updateMeta,
  removeMeta,
} from '@/store/helpers'
import { getFullName } from '@/utils'

const getDefaultMeta = () => {
  return {
    is_fetching: false,
    is_fetched: false
  }
}

const getDefaultState = () => {
  return {
    active_team_name: '',
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

  'FETCHING_TEAMS' (state, is_fetching) {
    state.is_fetching = is_fetching
    if (is_fetching === true) {
      state.is_fetched = false
    }
  },

  'FETCHED_TEAMS' (state, items) {
    addItem(state, items, getDefaultMeta())
    state.is_fetched = true
  },

  'CHANGE_ACTIVE_TEAM' (state, team_name) {
    state.active_team_name = team_name
  },
}

const actions = {
  'fetch' ({ commit }, { team_name, eid }) {
    // fetching a collection of items
    commit('FETCHING_TEAMS', true)

    return api.fetchTeams(team_name).then(response => {
      commit('FETCHED_TEAMS', response.data)
      commit('FETCHING_TEAMS', false)
      return response
    }).catch(error => {
      commit('FETCHING_TEAMS', false)
      throw error
    })
  },
  'changeActiveTeam' ({ commit, dispatch, state, rootState }, { team_name }) {
    if (team_name != state.active_team_name) {
      // if the team name changes, we need to reset the state on
      // a number of modules (pipes, connections, members, etc.)
      dispatch('initializeApp', { team_name, force: false }, { root: true })

      // now update the active team in this module
      commit('CHANGE_ACTIVE_TEAM', team_name)
    }
  },
}

const getters = {
  getAllTeams (state) {
    return _.sortBy(state.items, [ item => new Date(item.created) ])
  },

  getActiveTeam (state, getters) {
    var team_name = state.active_team_name
    return _.find(getters.getAllTeams, t => t.eid == team_name || t.username == team_name)
  },

  getActiveTeamLabel (state, getters)  {
    var team = getters.getActiveTeam
    var team_name = state.active_team_name
    return team ? getFullName(team) + "'s team" : team_name.length > 0 ? team_name + "'s team" : ''
  },
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
}

