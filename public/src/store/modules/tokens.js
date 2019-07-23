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
    is_fetched: false,
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

  'CREATED_ITEM' (state, item) {
    var meta = _.assign(getDefaultMeta(), { is_fetched: true })
    addItem(state, item, meta)
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
  },

  'DELETED_ITEM' (state, eid) {
    removeItem(state, eid)
  },
}

const actions = {
  'create' ({ commit, dispatch }, { team_name }) {
    return api.v2_createToken(team_name).then(response => {
      commit('CREATED_ITEM', response.data)
      return response
    }).catch(error => {
      throw error
    })
  },

  'fetch' ({ commit }, { team_name }) {
    // fetching a collection of items
    commit('FETCHING_ITEMS', true)

    return api.v2_fetchTokens(team_name).then(response => {
      commit('FETCHED_ITEMS', response.data)
      commit('FETCHING_ITEMS', false)
      return response
    }).catch(error => {
      commit('FETCHING_ITEMS', false)
      throw error
    })
  },

  'delete' ({ commit }, { team_name, eid }) {
    return api.v2_deleteToken(team_name, eid).then(response => {
      commit('DELETED_ITEM', eid)
      return response
    }).catch(error => {
      throw error
    })
  },
}

const getters = {
  getAllTokens (state, getters, root_state) {
    var items = _.filter(state.items, t => _.get(t, 'user_eid') == root_state.users.active_user_eid)
    items = _.sortBy(items, [ item => new Date(item.created) ])
    return items.reverse()
  },
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
}

