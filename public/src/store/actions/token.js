import api from '../../api'
import * as types from '../mutation-types'

// ----------------------------------------------------------------------- //

export const fetchTokens = ({ commit }) => {
  commit(types.FETCHING_TOKENS, { fetching: true })

  return api.fetchTokens().then(response => {
    // success callback
    commit(types.FETCHED_TOKENS, { tokens: response.body })
    commit(types.FETCHING_TOKENS, { fetching: false })
    return response
  }, response => {
    // error callback
    commit(types.FETCHING_TOKENS, { fetching: false })
    return response
  })
}

// ----------------------------------------------------------------------- //

export const createToken = ({ commit, dispatch }) => {
  commit(types.CREATING_TOKEN)

  return api.createToken().then(response => {
    // success callback
    commit(types.CREATED_TOKEN, { attrs: response.body })
    return response
  }, response => {
    // error callback
    return response
  })
}

export const deleteToken = ({ commit }, { eid }) => {
  commit(types.DELETING_TOKEN, { eid })

  return api.deleteToken({ eid }).then(response => {
    // success callback
    commit(types.DELETED_TOKEN, { eid })
    return response
  }, response => {
    // error callback
    return response
  })
}
