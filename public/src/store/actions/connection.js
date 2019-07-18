import api from '../../api'
import { sanitizeMasked } from '../../utils'
import * as types from '../mutation-types'

// ----------------------------------------------------------------------- //

export const v2_action_fetchConnections = ({ commit }, { team_name }) => {
  commit(types.FETCHING_CONNECTIONS, { fetching: true })

  return api.v2_fetchConnections(team_name).then(response => {
    var connections = response.data
    commit(types.FETCHED_CONNECTIONS, { connections })
    commit(types.FETCHING_CONNECTIONS, { fetching: false })
    return response
  }).catch(error => {
    commit(types.FETCHING_CONNECTIONS, { fetching: false })
    throw error
  })
}

// ----------------------------------------------------------------------- //

export const v2_action_createConnection = ({ commit }, { team_name, attrs }) => {
  commit(types.CREATING_CONNECTION, { attrs })

  return api.v2_createConnection(team_name, attrs).then(response => {
    var connection = response.data
    commit(types.CREATED_CONNECTION, { attrs, connection })
    return response
  }).catch(error => {
    throw error
  })
}

export const v2_action_fetchConnection = ({ commit }, { team_name, eid }) => {
  commit(types.FETCHING_CONNECTION, { eid, fetching: true })

  return api.v2_fetchConnection(team_name, eid).then(response => {
    var connection = response.data
    commit(types.FETCHED_CONNECTION, connection)
    commit(types.FETCHING_CONNECTION, { eid, fetching: false })
    return response
  }).catch(error => {
    commit(types.FETCHING_CONNECTION, { eid, fetching: false })
    throw error
  })
}

export const v2_action_updateConnection = ({ commit }, { team_name, eid, attrs }) => {
  // don't POST '*****' values
  if (attrs.connection_info) {
    attrs.connection_info = sanitizeMasked(attrs.connection_info)
  }

  commit(types.UPDATING_CONNECTION, { eid, attrs })

  return api.v2_updateConnection(team_name, eid, attrs).then(response => {
    var attrs = response.data
    commit(types.UPDATED_CONNECTION, { eid, attrs })
    return response
  }).catch(error => {
    throw error
  })
}

export const v2_action_deleteConnection = ({ commit }, { team_name, eid }) => {
  var attrs = { eid }
  commit(types.DELETING_CONNECTION, { attrs })

  return api.v2_deleteConnection(team_name, eid).then(response => {
    commit(types.DELETED_CONNECTION, { attrs })
  }).catch(error => {
    throw error
  })
}

// ----------------------------------------------------------------------- //

export const v2_action_testConnection = ({ commit }, { team_name, eid, attrs }) => {
  // don't POST '*****' values
  if (attrs.connection_info) {
    attrs.connection_info = sanitizeMasked(attrs.connection_info)
  }

  commit(types.TESTING_CONNECTION, { eid, testing: true })

  return api.v2_testConnection(team_name, eid, attrs).then(response => {
    var attrs = response.data
    commit(types.TESTED_CONNECTION, { eid, attrs })
    commit(types.TESTING_CONNECTION, { eid, testing: false })
    return response
  }).catch(error => {
    throw error
  })
}

export const v2_action_disconnectConnection = ({ commit }, { team_name, eid, attrs }) => {
  commit(types.TESTING_CONNECTION, { eid, disconnecting: true })

  return api.v2_disconnectConnection(team_name, eid, attrs).then(response => {
    var attrs = response.data
    commit(types.DISCONNECTED_CONNECTION, { eid, attrs })
    commit(types.TESTING_CONNECTION, { eid, disconnecting: false })
    return response
  }).catch(error => {
    throw error
  })
}
