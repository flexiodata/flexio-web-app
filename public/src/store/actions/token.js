import api from '../../api'
import * as types from '../mutation-types'

// ----------------------------------------------------------------------- //

export const fetchTokens = ({ commit }, { eid }) => {
  commit(types.FETCHING_TOKENS, { eid, fetching: true })

  return api.fetchTokens({ eid }).then(response => {
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

export const createToken = ({ commit, dispatch }, { eid, attrs }) => {
  commit(types.CREATING_TOKEN, { eid, attrs })

  return api.createToken({ eid, attrs }).then(response => {
    // success callback
    commit(types.CREATED_TOKEN, { eid, attrs: response.body })

    dispatch('analyticsTrack', { event_name: 'Created API Key' })

    return response
  }, response => {
    // error callback
    return response
  })
}

export const deleteToken = ({ commit }, { eid, token_eid }) => {
  commit(types.DELETING_TOKEN, { eid, token_eid })

  return api.deleteToken({ eid, token_eid }).then(response => {
    // success callback
    commit(types.DELETED_TOKEN, { eid, token_eid })
    return response
  }, response => {
    // error callback
    return response
  })
}
