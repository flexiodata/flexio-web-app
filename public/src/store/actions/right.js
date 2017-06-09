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

export const deleteRights = ({ commit }, { attrs }) => {

}
