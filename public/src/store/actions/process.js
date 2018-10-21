import api from '../../api'
import * as types from '../mutation-types'
import {
  PROCESS_STATUS_PENDING,
  PROCESS_STATUS_WAITING,
  PROCESS_STATUS_RUNNING
} from '../../constants/process'

// ----------------------------------------------------------------------- //

export const v2_action_fetchProcesses = ({ commit }, { user_eid, attrs }) => {
  var pipe_eid = _.get(attrs, 'parent_eid', '')
  commit(types.FETCHING_PROCESSES, { pipe_eid, fetching: true })

  return api.v2_fetchProcesses(user_eid).then(response => {
    var processes = response.data
    commit(types.FETCHED_PROCESSES, { pipe_eid, processes })
    commit(types.FETCHING_PROCESSES, { pipe_eid, fetching: false })
    return response
  }).catch(error => {
    commit(types.FETCHING_PROCESSES, { pipe_eid, fetching: false })
    return error
  })
}

// ----------------------------------------------------------------------- //

export const v2_action_createProcess = ({ commit, dispatch }, { user_eid, attrs }) => {
  commit(types.CREATING_PROCESS, { attrs })

  return api.v2_createProcess(user_eid, attrs).then(response => {
    var process = response.data
    commit(types.CREATED_PROCESS, { attrs, process })

    // the 'run' parameter was specified which means
    // we've started the process; poll for the process
    if (_.get(attrs, 'run', false) === true && process.eid) {
      dispatch('v2_action_fetchProcess', { user_eid, eid: process.eid, poll: true })
    }

    return response
  }).catch(error => {
    return error
  })
}

export const v2_action_fetchProcess = ({ commit, dispatch }, { user_eid, eid, poll }) => {
  commit(types.FETCHING_PROCESS, { eid, fetching: true })

  return api.v2_fetchProcess(user_eid, eid).then(response => {
    var process = response.data
    commit(types.FETCHED_PROCESS, process)
    commit(types.FETCHING_PROCESS, { eid, fetching: false })

    if (poll == true) {
      // poll the process while it is still running
      var status = _.get(process, 'process_status')
      if (_.includes([
            PROCESS_STATUS_PENDING,
            PROCESS_STATUS_WAITING,
            PROCESS_STATUS_RUNNING
          ], status)) {
        _.delay(function() {
          dispatch('v2_action_fetchProcess', { user_eid, eid, poll: true })
        }, 500)
      }
    }

    return response
  }).catch(error => {
    commit(types.FETCHING_PROCESS, { eid, fetching: false })
    return error
  })
}

// ----------------------------------------------------------------------- //

export const cancelProcess = ({ commit, dispatch }, { eid }) => {
  commit(types.CANCELING_PROCESS, { eid })

  return api.cancelProcess({ eid }).then(response => {
    // success callback
    commit(types.CANCELED_PROCESS, { process: response.body })
    return response
  }, response => {
    // error callback
    return response
  })
}

export const runProcess = ({ commit, dispatch }, { user_eid, eid, attrs }) => {
  commit(types.STARTING_PROCESS, { eid })

  dispatch('v2_action_fetchProcess', { user_eid, eid, poll: true })

  return api.runProcess({ eid, attrs }).then(response => {
    // success callback
    commit(types.STARTED_PROCESS, { process: { eid, process_status: PROCESS_STATUS_RUNNING } })
    return response
  }, response => {
    // error callback
    dispatch('v2_action_fetchProcess', { user_eid, eid })
    return response
  })
}

// ----------------------------------------------------------------------- //

export const fetchAdminProcesses = ({ commit }, attrs) => {
  var pipe_eid = _.get(attrs, 'parent_eid', '')

  commit(types.FETCHING_PROCESSES, { pipe_eid, fetching: true })

  return api.fetchAdminProcesses({ attrs }).then(response => {
    // success callback
    commit(types.FETCHED_PROCESSES, { pipe_eid, processes: response.body })
    commit(types.FETCHING_PROCESSES, { pipe_eid, fetching: false })
    return response
  }, response => {
    // error callback
    commit(types.FETCHING_PROCESSES, { pipe_eid, fetching: false })
    return response
  })
}

// ----------------------------------------------------------------------- //

export const fetchProcessLog = ({ commit, dispatch }, { eid }) => {
  commit(types.FETCHING_PROCESS_LOG, { eid, fetching: true })

  return api.fetchProcessLog({ eid }).then(response => {
    // success callback
    commit(types.FETCHED_PROCESS_LOG, { eid, log: response.body })
    commit(types.FETCHING_PROCESS_LOG, { eid, fetching: false })

    return response
  }, response => {
    // error callback
    commit(types.FETCHING_PROCESS_LOG, { eid, fetching: false })
    return response
  })
}

// ----------------------------------------------------------------------- //

export const fetchProcessSummary = ({ commit, dispatch }) => {
  commit(types.FETCHING_PROCESS_SUMMARY, { fetching: true })

  return api.fetchProcessSummary().then(response => {
    // success callback
    commit(types.FETCHED_PROCESS_SUMMARY, { items: response.body })
    commit(types.FETCHING_PROCESS_SUMMARY, { fetching: false })

    return response
  }, response => {
    // error callback
    commit(types.FETCHING_PROCESS_SUMMARY, { fetching: false })
    return response
  })
}
