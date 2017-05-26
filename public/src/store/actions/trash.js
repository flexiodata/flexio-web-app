import api from '../../api'
import * as types from '../mutation-types'

/* **** PIPE ACTIONS **** */

export const fetchTrash = ({ commit }) => {
  commit(types.FETCHING_TRASH, { fetching: true })

  return api.fetchTrash().then(response => {
    // success callback
    commit(types.FETCHED_TRASH, { trash: response.body })
    commit(types.FETCHING_TRASH, { fetching: false })
    return response
  }, response => {
    // error callback
    commit(types.FETCHING_TRASH, { fetching: false })
    return response
  })
}

// DEPRECATED
export const fetchProjectTrash = ({ commit }, project_eid) => {
  commit(types.FETCHING_TRASH, { project_eid, fetching: true })

  return api.fetchProjectTrash({ eid: project_eid }).then(response => {
    // success callback
    commit(types.FETCHED_TRASH, { project_eid, trash: response.body })
    commit(types.FETCHING_TRASH, { project_eid, fetching: false })
    return response
  }, response => {
    // error callback
    commit(types.FETCHING_TRASH, { project_eid, fetching: false })
    return response
  })
}
