import api from '../../api'
import * as types from '../mutation-types'
import { OBJECT_TYPE_PIPE } from '../../constants/object-type'

// ----------------------------------------------------------------------- //

export const v2_action_fetchPipes = ({ commit }, { user_eid }) => {
  commit(types.FETCHING_PIPES, { fetching: true })

  return api.v2_fetchPipes(user_eid).then(response => {
    var pipes = response.data
    commit(types.FETCHED_PIPES, { pipes })
    commit(types.FETCHING_PIPES, { fetching: false })
    return response
  }).catch(error => {
    commit(types.FETCHING_PIPES, { fetching: false })
    throw error
  })
}

// ----------------------------------------------------------------------- //

export const v2_action_createPipe = ({ commit, dispatch }, { user_eid, attrs }) => {
  commit(types.CREATING_PIPE, { attrs })

  return api.v2_createPipe(user_eid, attrs).then(response => {
    var pipe = response.data
    commit(types.CREATED_PIPE, { attrs, pipe })
    return response
  }).catch(error => {
    throw error
  })
}

export const v2_action_fetchPipe = ({ commit }, { user_eid, eid }) => {
  commit(types.FETCHING_PIPE, { eid, fetching: true })

  return api.v2_fetchPipe(user_eid, eid).then(response => {
    var pipe = response.data
    commit(types.FETCHED_PIPE, pipe)
    commit(types.FETCHING_PIPE, { eid, fetching: false })
    return response
  }).catch(error => {
    /*
    // errors don't include the eid or eid_type in the response body, so  we need
    // to add them manually to ensure the object is serialized in the Vuex store
    var eid_type = OBJECT_TYPE_PIPE
    commit(types.FETCHED_PIPE, _.assign({ eid, eid_type }, response.body))
    commit(types.FETCHING_PIPE, { eid, fetching: false })
    return response
    */
    throw error
  })
}

export const v2_action_updatePipe = ({ commit }, { user_eid, eid, attrs }) => {
  commit(types.UPDATING_PIPE, { eid, attrs })

  return api.v2_updatePipe(user_eid, eid, attrs).then(response => {
    var attrs = response.data
    commit(types.UPDATED_PIPE, { eid, attrs })
    return response
  }).catch(error => {
    throw error
  })
}

export const v2_action_deletePipe = ({ commit }, { user_eid, eid }) => {
  var attrs = { eid }
  commit(types.DELETING_PIPE, { attrs })

  return api.v2_deletePipe(user_eid, eid).then(response => {
    commit(types.DELETED_PIPE, { attrs })
    return response
  }).catch(error => {
    throw error
  })
}
