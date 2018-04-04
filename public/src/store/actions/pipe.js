import api from '../../api'
import * as types from '../mutation-types'
import { OBJECT_TYPE_PIPE } from '../../constants/object-type'

// ----------------------------------------------------------------------- //

export const fetchPipes = ({ commit }) => {
  commit(types.FETCHING_PIPES, { fetching: true })

  return api.fetchPipes().then(response => {
    // success callback
    commit(types.FETCHED_PIPES, { pipes: response.body })
    commit(types.FETCHING_PIPES, { fetching: false })
    return response
  }, response => {
    // error callback
    commit(types.FETCHING_PIPES, { fetching: false })
    return response
  })
}

// ----------------------------------------------------------------------- //

export const createPipe = ({ commit, dispatch }, { attrs }) => {
  commit(types.CREATING_PIPE, { attrs })

  return api.createPipe({ attrs }).then(response => {
    // success callback
    var pipe = response.body

    commit(types.CREATED_PIPE, { attrs, pipe })

    return response
  }, response => {
    // error callback
    return response
  })
}

export const fetchPipe = ({ commit }, { eid }) => {
  commit(types.FETCHING_PIPE, { eid, fetching: true })

  return api.fetchPipe({ eid }).then(response => {
    // success callback
    commit(types.FETCHED_PIPE, response.body)
    commit(types.FETCHING_PIPE, { eid, fetching: false })
    return response
  }, response => {
    // error callback

    // errors don't include the eid or eid_type in the response body, so  we need
    // to add them manually to ensure the object is serialized in the Vuex store
    var eid_type = OBJECT_TYPE_PIPE
    commit(types.FETCHED_PIPE, _.assign({ eid, eid_type }, response.body))
    commit(types.FETCHING_PIPE, { eid, fetching: false })
    return response
  })
}

export const updatePipe = ({ commit }, { eid, attrs }) => {
  commit(types.UPDATING_PIPE, { eid, attrs })

  return api.updatePipe({ eid, attrs }).then(response => {
    // success callback
    commit(types.UPDATED_PIPE, { eid, attrs: response.body })
    return response
  }, response => {
    // error callback
    return response
  })
}

export const deletePipe = ({ commit }, { attrs }) => {
  commit(types.DELETING_PIPE, { attrs })

  var eid = _.get(attrs, 'eid', '')
  return api.deletePipe({ eid }).then(response => {
    // success callback
    commit(types.DELETED_PIPE, { attrs })
    return response
  }, response => {
    // error callback
    return response
  })
}
