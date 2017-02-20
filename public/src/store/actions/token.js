import api from '../../api'
import * as types from '../mutation-types'

// ----------------------------------------------------------------------- //

export const fetchUserTokens = ({ commit }, { eid }) => {
  commit(types.FETCHING_TOKENS, { eid, fetching: true })

  return api.fetchUserTokens({ eid }).then(response => {
    // success callback
    commit(types.FETCHED_TOKENS, { eid, tokens: response.body })
    commit(types.FETCHING_TOKENS, { eid, fetching: false })
    return response
  }, response => {
    // error callback
    commit(types.FETCHING_TOKENS, { eid, fetching: false })
    return response
  })
}

// ----------------------------------------------------------------------- //

export const createUserToken = ({ commit }, { eid, attrs }) => {
  commit(types.CREATING_TOKEN, { eid, attrs })

  return api.createUserToken({ eid, attrs }).then(response => {
    // success callback
    commit(types.CREATED_TOKEN, { eid, attrs: response.body })
    return response
  }, response => {
    // error callback
    return response
  })
}

export const deleteUserToken = ({ commit }, { eid, token_eid }) => {
  commit(types.DELETING_TOKEN, { eid, token_eid })

  return api.deleteUserToken({ eid, token_eid }).then(response => {
    // success callback
    commit(types.DELETED_TOKEN, { eid, token_eid })
    return response
  }, response => {
    // error callback
    return response
  })
}
