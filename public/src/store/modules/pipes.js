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

  // this is problematic since we key on `eid`, but can now do fetching calls using `name`
  /*
  'FETCHING_ITEM' (state, { eid, name, is_fetching }) {
    var meta = _.assign(getDefaultMeta(), { is_fetching: true })

    // if we're trying to fetch an item that's not
    // in our store, add a placeholder item to the store
    if (is_fetching === true && !_.find(state.items, p => name ? p.name == name : p.eid == eid)) {
      addItem(state, { eid, name }, meta)
    } else {
      // otherwise, just set the item's `is_fetching` flag
      updateMeta(state, { eid, name }, { is_fetching: true })
    }
  },
  */

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

  'fetch' ({ commit }, { team_name, eid, name }) {
    if (eid || name) {
      // fetching a single item
      return api.v2_fetchPipe(team_name, eid || name).then(response => {
        commit('FETCHED_ITEM', response.data)
        return response
      }).catch(error => {
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
  getAllPipes (state) {
    var items = _.sortBy(state.items, [ item => new Date(item.created) ])
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

