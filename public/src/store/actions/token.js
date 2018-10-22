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

export const v2_action_createToken = ({ commit, dispatch }, { user_eid }) => {
  commit(types.CREATING_TOKEN)

  return api.v2_createToken(user_eid).then(response => {
    var attrs = response.data
    commit(types.CREATED_TOKEN, { attrs })
    return response
  }).catch(error => {
    return error
  })
}

export const v2_action_deleteToken = ({ commit }, { user_eid, eid }) => {
  commit(types.DELETING_TOKEN, { eid })

  return api.v2_deleteToken(user_eid, eid).then(response => {
    commit(types.DELETED_TOKEN, { eid })
    return response
  }).catch(error => {
    return error
  })
}
