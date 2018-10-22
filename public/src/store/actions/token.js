import api from '../../api'
import * as types from '../mutation-types'

// ----------------------------------------------------------------------- //

export const v2_action_fetchTokens = ({ commit }, { user_eid }) => {
  commit(types.FETCHING_TOKENS, { fetching: true })

  return api.v2_fetchTokens(user_eid).then(response => {
    var tokens = response.data
    commit(types.FETCHED_TOKENS, { tokens })
    commit(types.FETCHING_TOKENS, { fetching: false })
    return response
  }).catch(error => {
    commit(types.FETCHING_TOKENS, { fetching: false })
    return error
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
