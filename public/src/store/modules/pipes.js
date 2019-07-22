import api from '@/api'
import {
  addItem,
  updateItem,
  removeItem,
  removeMeta,
} from '../helpers'

const getDefaultMeta = () => {
  return {
    is_fetching: false,
    is_fetched: false,
    is_processes_fetching: false,
    is_processes_fetched: false,
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

  'FETCHING_ITEM' (state, { eid, is_fetching }) {
    var meta = _.assign(getDefaultMeta(), { is_fetching: true })

    // if we're trying to fetch an item that's not
    // in our store, add a placeholder item to the store
    if (is_fetching === true && !_.has(state.items, eid)) {
      addItem(state, eid, meta)
    } else {
      // otherwise, just set the pipe's is_fetching flag
      updateItem(state, eid, { is_fetching: true })
    }
  },

  'FETCHED_ITEM' (state, item) {
    // if the item in the store has an error node, remove it;
    // the new fetch will put it back if there are still errors
    removeMeta(state, item, ['error'])

    addItem(state, item, { is_is_fetched: true })
  },

  'UPDATED_ITEM' (state, { eid, item }) {
    updateItem(state, eid, item)
  },

  'DELETED_ITEM' (state, eid) {
    removeItem(state, eid)
  },
}

const actions = {
  'create' ({ commit, dispatch }, { team_name, attrs }) {
    return api.v2_createPipe(team_name, attrs).then(response => {
      commit('CREATED_ITEM', response.data)
      return response
    }).catch(error => {
      throw error
    })
  },

  'fetch' ({ commit }, { team_name, eid }) {
    if (eid) {
      // fetching a single item
      commit('FETCHING_ITEM', { eid, is_fetching: true })

      return api.v2_fetchPipe(team_name, eid).then(response => {
        commit('FETCHED_ITEM', response.data)
        commit('FETCHING_ITEM', { eid, is_fetching: false })
        return response
      }).catch(error => {
        commit('FETCHING_ITEM', { eid, is_fetching: false })
        throw error
      })
    } else {
      // fetching a collection of items
      commit('FETCHING_ITEMS', true)

      return api.v2_fetchPipes(team_name).then(response => {
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
    return api.v2_updatePipe(team_name, eid, attrs).then(response => {
      commit('UPDATED_ITEM', { eid, item: response.data })
      return response
    }).catch(error => {
      throw error
    })
  },

  'delete' ({ commit }, { team_name, eid }) {
    return api.v2_deletePipe(team_name, eid).then(response => {
      commit('DELETED_ITEM', eid)
      return response
    }).catch(error => {
      throw error
    })
  },
}

const getters = {
  'getAll' (state) {
    var items = _.sortBy(state.items, [ function(p) { return new Date(p.created) } ])
    return items.reverse()
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
}

