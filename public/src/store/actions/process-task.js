import api from '../../api'
import * as types from '../mutation-types'

// ----------------------------------------------------------------------- //

export const fetchProcessTaskInputInfo = ({ commit }, { eid, task_eid }) => {
  commit(types.FETCHING_PROCESS_TASK_INPUT_INFO, { eid, task_eid, fetching: true })

  return api.fetchProcessTaskInputInfo({ eid, task_eid }).then(response => {
    // success callback
    commit(types.FETCHED_PROCESS_TASK_INPUT_INFO, { eid, task_eid, columns: response.body })
    commit(types.FETCHING_PROCESS_TASK_INPUT_INFO, { eid, task_eid, fetching: false })
    return response
  }, response => {
    // error callback
    commit(types.FETCHING_PROCESS_TASK_INPUT_INFO, { eid, task_eid, fetching: false })
    return response
  })
}

export const fetchProcessTaskOutputInfo = ({ commit }, { eid, task_eid }) => {
  commit(types.FETCHING_PROCESS_TASK_OUTPUT_INFO, { eid, task_eid, fetching: true })

  return api.fetchProcessTaskOutputInfo({ eid, task_eid }).then(response => {
    // success callback
    commit(types.FETCHED_PROCESS_TASK_OUTPUT_INFO, { eid, task_eid, columns: response.body })
    commit(types.FETCHING_PROCESS_TASK_OUTPUT_INFO, { eid, task_eid, fetching: false })
    return response
  }, response => {
    // error callback
    commit(types.FETCHING_PROCESS_TASK_OUTPUT_INFO, { eid, task_eid, fetching: false })
    return response
  })
}

// ----------------------------------------------------------------------- //

