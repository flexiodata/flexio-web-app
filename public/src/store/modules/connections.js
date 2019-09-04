import api from '@/api'
import {
  addItem,
  updateItem,
  removeItem,
  updateMeta,
  removeMeta,
} from '@/store/helpers'
import { OBJECT_STATUS_AVAILABLE } from '@/constants/object-status'
import { sanitizeMasked } from '@/utils'

const CONNECTION_MODE_RESOURCE = 'R'
const CONNECTION_MODE_FUNCTION = 'F'

const getDefaultMeta = () => {
  return {
    is_fetching: false,
    is_fetched: false,
    is_testing: false,
    is_disconnecting: false,
    is_syncing: false,
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

  'CREATED_CONNECTION' (state, item) {
    var meta = _.assign(getDefaultMeta(), { is_fetched: true })
    addItem(state, item, meta)
  },

  'FETCHING_CONNECTIONS' (state, is_fetching) {
    state.is_fetching = is_fetching
    if (is_fetching === true) {
      state.is_fetched = false
    }
  },

  'FETCHED_CONNECTIONS' (state, items) {
    addItem(state, items, getDefaultMeta())
    state.is_fetched = true
  },

  'FETCHED_CONNECTION' (state, item) {
    var meta = _.assign(getDefaultMeta(), { is_fetched: true })
    addItem(state, item, meta)

    // if the item in the store has an error node, remove it;
    // the new fetch will put it back if there are still errors
    removeMeta(state, item, ['error'])
  },

  'UPDATED_CONNECTION' (state, { eid, item }) {
    updateItem(state, eid, item)
  },

  'DELETED_CONNECTION' (state, eid) {
    removeItem(state, eid)
  },

  'TESTING_CONNECTION' (state, { eid, is_testing }) {
    updateMeta(state, eid, { is_testing })
  },

  'TESTED_CONNECTION' (state, { eid, item }) {
    updateItem(state, eid, item)
  },

  'DISCONNECTING_CONNECTION' (state, { eid, is_disconnecting }) {
    updateMeta(state, eid, { is_disconnecting })
  },

  'DISCONNECTED_CONNECTION' (state, { eid, item }) {
    updateItem(state, eid, item)
  },

  'SYNCING_FUNCTION_MOUNT' (state, { eid, is_syncing }) {
    updateMeta(state, eid, { is_syncing })
  },
}

const actions = {
  'create' ({ commit }, { team_name, attrs }) {
    return api.createConnection(team_name, attrs).then(response => {
      commit('CREATED_CONNECTION', response.data)
      return response
    }).catch(error => {
      throw error
    })
  },

  'fetch' ({ commit }, { team_name, eid, name }) {
    if (eid || name) {
      // fetching a single item
      return api.fetchConnection(team_name, eid || name).then(response => {
        commit('FETCHED_CONNECTION', response.data)
        return response
      }).catch(error => {
        throw error
      })
    } else {
      // fetching a collection of items
      commit('FETCHING_CONNECTIONS', true)

      return api.fetchConnections(team_name).then(response => {
        commit('FETCHED_CONNECTIONS', response.data)
        commit('FETCHING_CONNECTIONS', false)
        return response
      }).catch(error => {
        commit('FETCHING_CONNECTIONS', false)
        throw error
      })
    }
  },

  'update' ({ commit }, { team_name, eid, attrs }) {
    // don't POST '*****' values
    if (attrs.connection_info) {
      attrs.connection_info = sanitizeMasked(attrs.connection_info)
    }

    return api.updateConnection(team_name, eid, attrs).then(response => {
      commit('UPDATED_CONNECTION', { eid, item: response.data })
      return response
    }).catch(error => {
      throw error
    })
  },

  'delete' ({ commit, dispatch }, { team_name, eid }) {
    return api.deleteConnection(team_name, eid).then(response => {
      dispatch('mountPurge', { eid })
      commit('DELETED_CONNECTION', eid)
      return response
    }).catch(error => {
      throw error
    })
  },

  'sync' ({ commit, dispatch }, { team_name, eid }) {
    commit('SYNCING_FUNCTION_MOUNT', { eid, is_syncing: true })
    return api.syncConnection(team_name, eid).then(response => {
      dispatch('mountPurge', { eid })
      dispatch('mountPopulate', { items: response.data })
      commit('SYNCING_FUNCTION_MOUNT', { eid, is_syncing: false })
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

    commit('TESTING_CONNECTION', { eid, is_testing: true })

    return api.testConnection(team_name, eid, attrs).then(response => {
      commit('TESTED_CONNECTION', { eid, item: response.data })
      commit('TESTING_CONNECTION', { eid, is_testing: false })
      return response
    }).catch(error => {
      commit('TESTING_CONNECTION', { eid, is_testing: false })
      throw error
    })
  },

  'disconnect' ({ commit }, { team_name, eid, attrs }) {
    commit('DISCONNECTING_CONNECTION', { eid, is_disconnecting: true })

    return api.disconnectConnection(team_name, eid, attrs).then(response => {
      commit('DISCONNECTED_CONNECTION', { eid, item: response.data })
      commit('DISCONNECTING_CONNECTION', { eid, is_disconnecting: false })
      return response
    }).catch(error => {
      commit('DISCONNECTING_CONNECTION', { eid, is_disconnecting: false })
      throw error
    })
  },

  // helper action for purging pipes associated with a function mount
  'mountPurge' ({ commit, state, rootState }, { eid }) {
    var cmode = _.get(state.items, `[${eid}].connection_mode`, '')

    if (cmode == CONNECTION_MODE_FUNCTION) {
      var pipes = _.filter(rootState.pipes.items, pipe => _.get(pipe, 'parent.eid') == eid)
      _.each(pipes, pipe => {
        commit('pipes/DELETED_PIPE', pipe.eid, { root: true })
      })
    }
  },

  // helper action for adding pipes associated with a function mount
  'mountPopulate' ({ commit }, { items }) {
    // since this call simply returns a list of pipes for now, just commit
    // the `pipes/FETCHED_PIPES` mutation to populate the Vuex store
    commit('pipes/FETCHED_PIPES', items, { root: true })
  },
}

const getters = {
  getAllConnections (state) {
    var items = _.sortBy(state.items, [ item => new Date(item.created) ])
    return items.reverse()
  },

  getAvailableConnections (state, getters) {
    return _.filter(getters.getAllConnections, { eid_status: OBJECT_STATUS_AVAILABLE, connection_mode: CONNECTION_MODE_RESOURCE })
  },

  getAvailableFunctionMounts (state, getters) {
    return _.filter(getters.getAllConnections, { eid_status: OBJECT_STATUS_AVAILABLE, connection_mode: CONNECTION_MODE_FUNCTION })
  },
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
}

