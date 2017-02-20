import * as types from '../mutation-types'
import { addProject, updateProject, addConnection, updateConnection, removeObject } from './helpers'

export default {

  // ----------------------------------------------------------------------- //

  [types.FETCHING_CONNECTIONS] (state, { project_eid, fetching }) {
    // if we're trying to fetch connections for a project that's not
    // in our store, add a very basic project object to the store
    if (fetching === true && !_.has(state.objects, project_eid))
    {
      addProject(state, project_eid, { connections_fetching: fetching })
    }
     else
    {
      // otherwise, just set the project's connection fetching flag
      updateProject(state, project_eid, { connections_fetching: fetching })
    }
  },

  [types.FETCHED_CONNECTIONS] (state, { project_eid, connections }) {
    addConnection(state, connections)

    // set the project's connection fetched flag
    updateProject(state, project_eid, { connections_fetched: true })
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
