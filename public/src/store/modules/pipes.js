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

  'CREATED_PIPE' (state, item) {
    var meta = _.assign(getDefaultMeta(), { is_fetched: true })
    addItem(state, item, meta)
  },

  'FETCHING_PIPES' (state, is_fetching) {
    state.is_fetching = is_fetching
    if (is_fetching === true) {
      state.is_fetched = false
    }
  },

  'FETCHED_PIPES' (state, items) {
    addItem(state, items, getDefaultMeta())
    state.is_fetched = true
  },

  // this is problematic since we key on `eid`, but can now do fetching calls using `name`
  /*
  'FETCHING_PIPE' (state, { eid, name, is_fetching }) {
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

  'FETCHED_PIPE' (state, item) {
    var meta = _.assign(getDefaultMeta(), { is_fetched: true })
    addItem(state, item, meta)

    // if the item in the store has an error node, remove it;
    // the new fetch will put it back if there are still errors
    removeMeta(state, item, ['error'])
  },

  'UPDATED_PIPE' (state, { eid, item }) {
    updateItem(state, eid, item)
  },

  'DELETED_PIPE' (state, eid) {
    removeItem(state, eid)
  },
}

const actions = {
  'create' ({ commit, dispatch }, { team_name, attrs }) {
    return api.createPipe(team_name, attrs).then(response => {
      commit('CREATED_PIPE', response.data)
      return response
    }).catch(error => {
      throw error
    })
  },

  'fetch' ({ commit }, { team_name, eid, name }) {
    if (eid || name) {
      // fetching a single item
      return api.fetchPipe(team_name, eid || name).then(response => {
        commit('FETCHED_PIPE', response.data)
        return response
      }).catch(error => {
        throw error
      })
    } else {
      // fetching a collection of items
      commit('FETCHING_PIPES', true)

      return api.fetchPipes(team_name).then(response => {
        commit('FETCHED_PIPES', response.data)
        commit('FETCHING_PIPES', false)
        return response
      }).catch(error => {
        commit('FETCHING_PIPES', false)
        throw error
      })
    }
  },

  'update' ({ commit }, { team_name, eid, attrs }) {
    return api.updatePipe(team_name, eid, attrs).then(response => {
      commit('UPDATED_PIPE', { eid, item: response.data })
      return response
    }).catch(error => {
      throw error
    })
  },

  'delete' ({ commit }, { team_name, eid }) {
    return api.deletePipe(team_name, eid).then(response => {
      commit('DELETED_PIPE', eid)
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

