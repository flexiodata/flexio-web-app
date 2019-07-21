import { addItem, updateItem, removeItem, removeMeta } from '../helpers'

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
    items: {
      /*
      eid: {
        fetching: false,
        fetched: false,
        processes_fetching: false,
        processes_fetched: false,
        object: {}
      }
      */
    },
  }
}

const state = getDefaultState()

const mutations = {
  RESET_STATE (state) {
    _.assign(state, getDefaultState())
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

  CREATING_ITEM (state, item) {},

  CREATED_ITEM (state, item) {
    var meta = _.assign(getDefaultMeta(), { fetched: true })
    addItem(state, item, meta)
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

  UPDATING_ITEM (state, { eid, item }) {},

  UPDATED_ITEM (state, { eid, item }) {
    updateItem(state, eid, item)
  },

  DELETING_ITEM (state, item) {},

  DELETED_ITEM (state, item) {
    removeItem(state, item)
  },
}

const actions = {}

const getters = {}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
}

