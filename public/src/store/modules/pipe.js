import api from '@/api'
import {
  addItem,
  updateItem,
  removeItem,
  removeMeta,
} from '../helpers'

const getDefaultMeta = () => {
  return {
    fetching: false,
    fetched: false,
    processes_fetching: false,
    processes_fetched: false,
    /*error: {},*/
  }
}

const getDefaultState = () => {
  return {
    fetching: false,
    fetched: false,
    items: {},
  }
}

const state = getDefaultState()

const mutations = {
  RESET_STATE (state) {
    _.assign(state, getDefaultState())
  },

  CREATED_ITEM (state, item) {
    var meta = _.assign(getDefaultMeta(), { fetched: true })
    addItem(state, item, meta)
  },

  FETCHING_ITEMS (state, fetching) {
    state.fetching = fetching
    if (fetching === true) {
      state.fetched = false
    }
  },

  FETCHED_ITEMS (state, items) {
    addItem(state, items, getDefaultMeta())
    state.fetched = true
  },

  FETCHING_ITEM (state, { eid, fetching }) {
    var meta = _.assign(getDefaultMeta(), { fetching: true })

    // if we're trying to fetch an item that's not
    // in our store, add a placeholder item to the store
    if (fetching === true && !_.has(state.items, eid)) {
      addItem(state, eid, meta)
    } else {
      // otherwise, just set the pipe's fetching flag
      updateItem(state, eid, { fetching: true })
    }
  },

  FETCHED_ITEM (state, item) {
    // if the item in the store has an error node, remove it;
    // the new fetch will put it back if there are still errors
    removeMeta(state, item, ['error'])

    addItem(state, item, { is_fetched: true })
  },

  UPDATED_ITEM (state, { eid, item }) {
    updateItem(state, eid, item)
  },

  DELETED_ITEM (state, eid) {
    removeItem(state, eid)
  },
}

const actions = {

  'create' ({ commit, dispatch }, { team_name, attrs }) {
    return api.v2_createPipe(team_name, attrs).then(response => {
      commit('pipe/CREATED_ITEM', response.data)
      return response
    }).catch(error => {
      throw error
    })
  },

  'fetch' ({ commit }, { team_name, eid }) {
    if (eid) {
      // fetching a single item
      commit('pipe/FETCHING_ITEM', { eid, fetching: true })

      return api.v2_fetchPipe(team_name, eid).then(response => {
        commit('pipe/FETCHED_ITEM', response.data)
        commit('pipe/FETCHING_ITEM', { eid, fetching: false })
        return response
      }).catch(error => {
        commit('pipe/FETCHING_ITEM', { eid, fetching: false })
        throw error
      })
    } else {
      // fetching a collection of items
      commit('pipe/FETCHING_ITEMS', true)

      return api.v2_fetchPipes(team_name).then(response => {
        commit('pipe/FETCHED_ITEMS', response.data)
        commit('pipe/FETCHING_ITEMS', false)
        return response
      }).catch(error => {
        commit('pipe/FETCHING_ITEMS', false)
        throw error
      })
    }
  },

  'update' ({ commit }, { team_name, eid, attrs }) {
    return api.v2_updatePipe(team_name, eid, attrs).then(response => {
      commit('pipe/UPDATED_ITEM', { eid, item: response.data })
      return response
    }).catch(error => {
      throw error
    })
  },

  'delete' ({ commit }, { team_name, eid }) {
    return api.v2_deletePipe(team_name, eid).then(response => {
      commit('pipe/DELETED_ITEM', eid)
      return response
    }).catch(error => {
      throw error
    })
  },
}

const getters = {}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
}

