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

export const createConnection = ({ commit }, { user_eid, attrs }) => {
  commit(types.CREATING_CONNECTION, { attrs })

  return api.createConnection({ attrs }).then(response => {
    var connection = response.body
    commit(types.CREATED_CONNECTION, { attrs, connection })
    return response
  }, response => {
    return response
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

export const updateConnection = ({ commit }, { user_eid, eid, attrs }) => {
  // don't POST '*****' values
  if (attrs.connection_info)
    attrs.connection_info = util.sanitizeMasked(attrs.connection_info)

  commit(types.UPDATING_CONNECTION, { eid, attrs })

  return api.updateConnection({ eid, attrs }).then(response => {
    // success callback
    commit(types.UPDATED_CONNECTION, { eid, attrs: response.body })
    return response
  }, response => {
    // error callback
    return response
  })
}

export const deleteConnection = ({ commit }, { user_eid, attrs }) => {
  commit(types.DELETING_CONNECTION, { attrs })

  var eid = _.get(attrs, 'eid', '')
  return api.deleteConnection({ eid }).then(response => {
    // success callback
    commit(types.DELETED_CONNECTION, { attrs })
    return response
  }, response => {
    // error callback
    return response
  })
}

// ----------------------------------------------------------------------- //

export const testConnection = ({ commit }, { user_eid, eid, attrs }) => {
  // don't POST '*****' values
  if (attrs.connection_info)
    attrs.connection_info = util.sanitizeMasked(attrs.connection_info)

  commit(types.TESTING_CONNECTION, { eid, testing: true })

  return api.testConnection({ eid, attrs }).then(response => {
    // success callback
    commit(types.TESTED_CONNECTION, { eid, attrs: response.body })
    commit(types.TESTING_CONNECTION, { eid, testing: false })
    return response
  }, response => {
    // error callback
    return response
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
