import api from '../../api'
import * as types from '../mutation-types'

// ----------------------------------------------------------------------- //

export const fetchAdminStatistics = ({ commit }, { type }) => {
  commit(types.FETCHING_STATISTICS, { type, fetching: true })

  return api.fetchAdminStatistics({ type }).then(response => {
    // success callback
    commit(types.FETCHED_STATISTICS, { type, statistics: response.body })
    commit(types.FETCHING_STATISTICS, { type, fetching: false })
    return response
  }, response => {
    // error callback
    commit(types.FETCHING_STATISTICS, { type, fetching: false })
    return response
  })
}

// ----------------------------------------------------------------------- //

