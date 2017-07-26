import api from '../../api'
import * as types from '../mutation-types'

// ----------------------------------------------------------------------- //

export const fetchStream = ({ commit }, { eid }) => {
  commit(types.FETCHING_STREAM, { eid, fetching: true })

  return api.fetchStream({ eid }).then(response => {
    // success callback
    commit(types.FETCHED_STREAM, response.body)
    commit(types.FETCHING_STREAM, { eid, fetching: false })
    return response
  }, response => {
    // error callback
    commit(types.FETCHING_STREAM, { eid, fetching: false })
    return response
  })
}

// ----------------------------------------------------------------------- //

