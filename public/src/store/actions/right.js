import api from '../../api'
import * as types from '../mutation-types'

// ----------------------------------------------------------------------- //

export const fetchRights = ({ commit }, { objects }) => {
  commit(types.FETCHING_RIGHTS, { fetching: true })

  return api.fetchRights({ attrs: { objects } }).then(response => {
    // success callback
    commit(types.FETCHED_RIGHTS, { rights: response.body })
    commit(types.FETCHING_RIGHTS, { fetching: false })
    return response
  }, response => {
    // error callback
    commit(types.FETCHING_RIGHTS, { fetching: false })
    return response
  })
}

// ----------------------------------------------------------------------- //

export const createRights = ({ commit }, { attrs }) => {
  _.each(_.get(attrs, 'rights', []), (attrs) => {
    commit(types.CREATING_RIGHT, { attrs })
  })

  return api.createRights({ attrs }).then(response => {
    // success callback
    _.each(_.get(response, 'body', []), (attrs) => {
      commit(types.CREATED_RIGHT, { attrs })
    })
    return response
  }, response => {
    // error callback
    return response
  })
}

export const updateRight = ({ commit }, { eid, attrs }) => {
  commit(types.UPDATING_RIGHT, { eid, attrs })

  return api.updateRight({ eid, attrs }).then(response => {
    // success callback
    commit(types.UPDATED_RIGHT, { eid, attrs: response.body })
    return response
  }, response => {
    // error callback
    return response
  })
}

export const deleteRight = ({ commit }, { attrs }) => {
  commit(types.DELETING_RIGHT, { attrs })

  var eid = _.get(attrs, 'eid', '')
  return api.deleteRight({ eid }).then(response => {
    // success callback
    commit(types.DELETED_RIGHT, { attrs })
    return response
  }, response => {
    // error callback
    return response
  })
}
