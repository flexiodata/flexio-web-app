import api from '../../api'
import * as types from '../mutation-types'

/* **** PIPE ACTIONS **** */

export const fetchTrash = ({ commit }, project_eid) => {
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
