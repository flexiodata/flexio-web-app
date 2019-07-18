import api from '../../api'
import * as types from '../mutation-types'

// ----------------------------------------------------------------------- //

export const v2_action_fetchTokens = ({ commit }, { team_name }) => {
  commit(types.FETCHING_TOKENS, { fetching: true })

  return api.v2_fetchTokens(team_name).then(response => {
    var tokens = response.data
    commit(types.FETCHED_TOKENS, { tokens })
    commit(types.FETCHING_TOKENS, { fetching: false })
    return response
  }).catch(error => {
    commit(types.FETCHING_TOKENS, { fetching: false })
    throw error
  })
}

// ----------------------------------------------------------------------- //

export const v2_action_createToken = ({ commit, dispatch }, { team_name }) => {
  commit(types.CREATING_TOKEN)

  return api.v2_createToken(team_name).then(response => {
    var attrs = response.data
    commit(types.CREATED_TOKEN, { attrs })
    return response
  }).catch(error => {
    throw error
  })
}

export const v2_action_deleteToken = ({ commit }, { team_name, eid }) => {
  commit(types.DELETING_TOKEN, { eid })

  return api.v2_deleteToken(team_name, eid).then(response => {
    commit(types.DELETED_TOKEN, { eid })
    return response
  }).catch(error => {
    throw error
  })
}
