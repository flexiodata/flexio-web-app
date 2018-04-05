import api from '../../api'
import * as types from '../mutation-types'

// ----------------------------------------------------------------------- //

export const fetchAdminInfo = ({ commit }, { type, action }) => {
  commit(types.FETCHING_STATISTICS, { type: 'admin-'+type+'-'+action, fetching: true })

  return api.fetchAdminInfo({ type, action }).then(response => {
    // success callback
    commit(types.FETCHED_STATISTICS, { type: 'admin-'+type+'-'+action, statistics: response.body })
    commit(types.FETCHING_STATISTICS, { type: 'admin-'+type+'-'+action, fetching: false })
    return response
  }, response => {
    // error callback
    commit(types.FETCHING_STATISTICS, { type: 'admin-'+type+'-'+action, fetching: false })
    return response
  })
}

export const fetchStatistics = ({ commit }, { type, action }) => {
  commit(types.FETCHING_STATISTICS, { type: type+'-'+action, fetching: true })

  return api.fetchStatistics({ type, action }).then(response => {
    // success callback
    commit(types.FETCHED_STATISTICS, { type: type+'-'+action, statistics: response.body })
    commit(types.FETCHING_STATISTICS, { type: type+'-'+action, fetching: false })
    return response
  }, response => {
    // error callback
    commit(types.FETCHING_STATISTICS, { type: type+'-'+action, fetching: false })
    return response
  })
}

// ----------------------------------------------------------------------- //

