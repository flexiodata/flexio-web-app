import api from '../../api'
import * as types from '../mutation-types'

// ----------------------------------------------------------------------- //

export const v2_action_fetchStream = ({ commit }, { team_name, eid }) => {
  commit(types.FETCHING_STREAM, { eid, fetching: true })

  return api.v2_fetchStream(team_name, eid).then(response => {
    var stream = response.data
    commit(types.FETCHED_STREAM, stream)
    commit(types.FETCHING_STREAM, { eid, fetching: false })
    return response
  }).catch(error => {
    commit(types.FETCHING_STREAM, { eid, fetching: false })
    throw error
  })
}

// ----------------------------------------------------------------------- //
