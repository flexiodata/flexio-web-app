import api from '@/api'
import {
  addItem,
  updateItem,
  removeItem,
  updateMeta,
  removeMeta,
} from '@/store/helpers'
import {OBJECT_STATUS_AVAILABLE } from '@/constants/object-status'
import { sanitizeMasked } from '@/utils'

const getDefaultMeta = () => {
  return {
    is_fetching: false,
    is_fetched: false,
    is_testing: false,
    is_disconnecting: false,
    /*error: {},*/
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

  'FETCHED_ITEM' (state, item) {
    var meta = _.assign(getDefaultMeta(), { is_fetched: true })
    addItem(state, item, meta)

    // if the item in the store has an error node, remove it;
    // the new fetch will put it back if there are still errors
    removeMeta(state, item, ['error'])
  },

  'UPDATED_ITEM' (state, { eid, item }) {
    updateItem(state, eid, item)
  },

  'DELETED_ITEM' (state, eid) {
    removeItem(state, eid)
  },

  'TESTING_ITEM' (state, { eid, is_testing }) {
    updateMeta(state, eid, { is_testing })
  },

  'TESTED_ITEM' (state, { eid, item }) {
    updateItem(state, eid, item)
  },

  'DISCONNECTING_ITEM' (state, { eid, is_disconnecting }) {
    updateMeta(state, eid, { is_disconnecting })
  },

  'DISCONNECTED_ITEM' (state, { eid, item }) {
    updateItem(state, eid, item)
  }
}

const actions = {
  'create' ({ commit, dispatch }, { team_name, attrs }) {
    return api.v2_createConnection(team_name, attrs).then(response => {
      commit('CREATED_ITEM', response.data)
      return response
    }).catch(error => {
      throw error
    })
  },

  'fetch' ({ commit }, { team_name, eid, name }) {
    if (eid || name) {
      // fetching a single item
      return api.v2_fetchConnection(team_name, eid || name).then(response => {
        commit('FETCHED_ITEM', response.data)
        return response
      }).catch(error => {
        throw error
      })
    } else {
      // fetching a collection of items
      commit('FETCHING_ITEMS', true)

      return api.v2_fetchConnections(team_name).then(response => {
        commit('FETCHED_ITEMS', response.data)
        commit('FETCHING_ITEMS', false)
        return response
      }).catch(error => {
        commit('FETCHING_ITEMS', false)
        throw error
      })
    }
  },

  'update' ({ commit }, { team_name, eid, attrs }) {
    // don't POST '*****' values
    if (attrs.connection_info) {
      attrs.connection_info = sanitizeMasked(attrs.connection_info)
    }

    return api.v2_updateConnection(team_name, eid, attrs).then(response => {
      commit('UPDATED_ITEM', { eid, item: response.data })
      return response
    }).catch(error => {
      throw error
    })
  },

  'delete' ({ commit }, { team_name, eid }) {
    return api.v2_deleteConnection(team_name, eid).then(response => {
      commit('DELETED_ITEM', eid)
      return response
    }).catch(error => {
      throw error
    })
  },

  'test' ({ commit }, { team_name, eid, attrs }) {
    // don't POST '*****' values
    if (attrs.connection_info) {
      attrs.connection_info = sanitizeMasked(attrs.connection_info)
    }

    commit('TESTING_ITEM', { eid, is_testing: true })

    return api.v2_testConnection(team_name, eid, attrs).then(response => {
      commit('TESTED_ITEM', { eid, item: response.data })
      commit('TESTING_ITEM', { eid, is_testing: false })
      return response
    }).catch(error => {
      commit('TESTING_ITEM', { eid, is_testing: false })
      throw error
    })
  },

  'disconnect' ({ commit }, { team_name, eid, attrs }) {
    commit('DISCONNECTING_ITEM', { eid, is_disconnecting: true })

    return api.v2_disconnectConnection(team_name, eid, attrs).then(response => {
      commit('DISCONNECTED_ITEM', { eid, item: response.data })
      commit('DISCONNECTING_ITEM', { eid, is_disconnecting: false })
      return response
    }).catch(error => {
      commit('DISCONNECTING_ITEM', { eid, is_disconnecting: false })
      throw error
    })
  },
}

const getters = {
  getAllConnections (state) {
    var items = _.sortBy(state.items, [ function(p) { return new Date(p.created) } ])
    return items.reverse()
  },

  getAvailableConnections (state, getters) {
    return _.filter(getters.getAllConnections, { eid_status: OBJECT_STATUS_AVAILABLE })
  },
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
}

