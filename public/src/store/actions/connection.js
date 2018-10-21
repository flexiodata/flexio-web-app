import api from '../../api'
import util from '../../utils'
import * as types from '../mutation-types'

// ----------------------------------------------------------------------- //

export const v2_action_fetchConnections = ({ commit }, { user_eid }) => {
  commit(types.FETCHING_CONNECTIONS, { fetching: true })

  return api.v2_fetchConnections(user_eid).then(response => {
    var connections = response.data
    commit(types.FETCHED_CONNECTIONS, { connections })
    commit(types.FETCHING_CONNECTIONS, { fetching: false })
    return response
  }).catch(error => {
    commit(types.FETCHING_CONNECTIONS, { fetching: false })
    return error
  })
}

// ----------------------------------------------------------------------- //

export const v2_action_createConnection = ({ commit }, { user_eid, attrs }) => {
  commit(types.CREATING_CONNECTION, { attrs })

  return api.v2_createConnection(user_eid, attrs).then(response => {
    var connection = response.data
    commit(types.CREATED_CONNECTION, { attrs, connection })
    return response
  }).catch(error => {
    return error
  })
}

export const v2_action_fetchConnection = ({ commit }, { user_eid, eid }) => {
  commit(types.FETCHING_CONNECTION, { eid, fetching: true })

  return api.v2_fetchConnection(user_eid, eid).then(response => {
    var connection = response.data
    commit(types.FETCHED_CONNECTION, connection)
    commit(types.FETCHING_CONNECTION, { eid, fetching: false })
    return response
  }).catch(error => {
    commit(types.FETCHING_CONNECTION, { eid, fetching: false })
    return error
  })
}

export const v2_action_updateConnection = ({ commit }, { user_eid, eid, attrs }) => {
  // don't POST '*****' values
  if (attrs.connection_info) {
    attrs.connection_info = util.sanitizeMasked(attrs.connection_info)
  }

  commit(types.UPDATING_CONNECTION, { eid, attrs })

  return api.v2_updateConnection(user_eid, eid, attrs).then(response => {
    var attrs = response.data
    commit(types.UPDATED_CONNECTION, { eid, attrs })
    return response
  }).catch(error => {
    return error
  })
}

export const v2_action_deleteConnection = ({ commit }, { user_eid, eid }) => {
  var attrs = { eid }
  commit(types.DELETING_CONNECTION, { attrs })

  return api.v2_deleteConnection(user_eid, eid).then(response => {
    commit(types.DELETED_CONNECTION, { attrs })
  }).catch(error => {
    return error
  })
}

// ----------------------------------------------------------------------- //

export const v2_action_testConnection = ({ commit }, { user_eid, eid, attrs }) => {
  // don't POST '*****' values
  if (attrs.connection_info) {
    attrs.connection_info = util.sanitizeMasked(attrs.connection_info)
  }

  commit(types.TESTING_CONNECTION, { eid, testing: true })

  return api.v2_testConnection(user_eid, eid, attrs).then(response => {
    var attrs = response.data
    commit(types.TESTED_CONNECTION, { eid, attrs })
    commit(types.TESTING_CONNECTION, { eid, testing: false })
    return response
  }).catch(error => {
    return error
  })
}

export const disconnectConnection = ({ commit }, { user_eid, eid, attrs }) => {
  commit(types.TESTING_CONNECTION, { eid, disconnecting: true })

  return api.disconnectConnection({ eid, attrs }).then(response => {
    // success callback
    commit(types.DISCONNECTED_CONNECTION, { eid, attrs: response.body })
    commit(types.TESTING_CONNECTION, { eid, disconnecting: false })
    return response
  }, response => {
    // error callback
    return response
  })
}
