import api from '../../api'
import util from '../../utils'
import * as types from '../mutation-types'

// ----------------------------------------------------------------------- //

export const v2_action_fetchConnections = ({ commit }, { user_eid }) => {
  commit(types.FETCHING_CONNECTIONS, { fetching: true })

  return api.v2_fetchConnections().then(response => {
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

export const createConnection = ({ commit }, { attrs }) => {
  commit(types.CREATING_CONNECTION, { attrs })

  return api.createConnection({ attrs }).then(response => {
    // success callback
    var connection = response.body
    commit(types.CREATED_CONNECTION, { attrs, connection })
    return response
  }, response => {
    // error callback
    return response
  })
}

export const fetchConnection = ({ commit }, { eid }) => {
  commit(types.FETCHING_CONNECTION, { eid, fetching: true })

  return api.fetchConnection({ eid }).then(response => {
    // success callback
    commit(types.FETCHED_CONNECTION, response.body)
    commit(types.FETCHING_CONNECTION, { eid, fetching: false })
    return response
  }, response => {
    // error callback
    commit(types.FETCHING_CONNECTION, { eid, fetching: false })
    return response
  })
}

export const updateConnection = ({ commit }, { eid, attrs }) => {
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

export const deleteConnection = ({ commit }, { attrs }) => {
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

export const testConnection = ({ commit }, { eid, attrs }) => {
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

export const disconnectConnection = ({ commit }, { eid, attrs }) => {
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
