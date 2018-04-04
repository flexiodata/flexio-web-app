import api from '../../api'
import * as types from '../mutation-types'

// ----------------------------------------------------------------------- //

export const fetchAdminInfo = ({ commit }, { type }) => {
  commit(types.FETCHING_STATISTICS, { type: 'admin-'+type, fetching: true })

  return api.fetchAdminInfo({ type }).then(response => {
    // success callback
    commit(types.FETCHED_STATISTICS, { type: 'admin-'+type, statistics: response.body })
    commit(types.FETCHING_STATISTICS, { type: 'admin-'+type, fetching: false })
    return response
  }, response => {
    // error callback
    commit(types.FETCHING_STATISTICS, { type: 'admin-'+type, fetching: false })
    return response
  })
}

export const fetchStatistics = ({ commit }, { type }) => {
  commit(types.FETCHING_STATISTICS, { type, fetching: true })

  return api.fetchStatistics({ type }).then(response => {
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

