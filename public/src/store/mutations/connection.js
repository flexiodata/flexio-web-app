import * as types from '../mutation-types'
import { addConnection, updateConnection, removeObject } from './helpers'

export default {

  // ----------------------------------------------------------------------- //

  [types.FETCHING_CONNECTIONS] (state, { fetching }) {
    state.connections_fetching = fetching
  },

  [types.FETCHED_CONNECTIONS] (state, { connections }) {
    addConnection(state, connections)

    // set our fetched flag so we know we've queried the backend
    state.connections_fetched = true
  },

  // ----------------------------------------------------------------------- //

  [types.CREATING_CONNECTION] (state, { attrs }) {},

  [types.CREATED_CONNECTION] (state, { attrs, connection }) {
    addConnection(state, connection, { is_fetched: true })
  },

  [types.FETCHING_CONNECTION] (state, { eid, fetching }) {
    // if we're trying to fetch a connection that's not
    // in our store, add a very basic connection object to the store
    if (fetching === true && !_.has(state.objects, eid))
    {
      addConnection(state, eid, { is_fetching: fetching })
    }
     else
    {
      // otherwise, just set the connection's fetching flag
      updateConnection(state, eid, { is_fetching: fetching })
    }
  },

  [types.FETCHED_CONNECTION] (state, connection) {
    addConnection(state, connection, { is_fetched: true })
  },

  [types.UPDATING_CONNECTION] (state, { eid, attrs }) {},

  [types.UPDATED_CONNECTION] (state, { eid, attrs }) {
    updateConnection(state, eid, attrs)
  },

  [types.DELETING_CONNECTION] (state, { attrs }) {},

  [types.DELETED_CONNECTION] (state, { attrs }) {
    removeObject(state, attrs)
  },

  // ----------------------------------------------------------------------- //

  [types.TESTING_CONNECTION] (state, { eid, testing }) {
    updateConnection(state, eid, { is_testing: testing })
  },

  [types.TESTED_CONNECTION] (state, { eid, attrs }) {
    updateConnection(state, eid, attrs)
  },

  [types.DISCONNECTING_CONNECTION] (state, { eid, disconnecting }) {
    updateConnection(state, eid, { is_disconnecting: disconnecting })
  },

  [types.DISCONNECTED_CONNECTION] (state, { eid, attrs }) {
    updateConnection(state, eid, attrs)
  }

  // ----------------------------------------------------------------------- //

}
