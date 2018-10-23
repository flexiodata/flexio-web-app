import api from '../../api'
import * as types from '../mutation-types'
import {
  PROCESS_STATUS_PENDING,
  PROCESS_STATUS_WAITING,
  PROCESS_STATUS_RUNNING
} from '../../constants/process'

// ----------------------------------------------------------------------- //

export const v2_action_fetchAdminProcesses = ({ commit }, { attrs }) => {
  var pipe_eid = _.get(attrs, 'parent_eid', '')
  commit(types.FETCHING_PROCESSES, { pipe_eid, fetching: true })

  return api.v2_fetchAdminProcesses(attrs).then(response => {
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

export const v2_action_fetchProcesses = ({ commit }, { user_eid, attrs }) => {
  var pipe_eid = _.get(attrs, 'parent_eid', '')
  commit(types.FETCHING_PROCESSES, { pipe_eid, fetching: true })

  return api.v2_fetchProcesses(user_eid, attrs).then(response => {
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
      dispatch('v2_action_fetchProcess', { user_eid, eid: process.eid, poll: true }).catch(error => {
        // TODO: add error handling?
      })
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
          dispatch('v2_action_fetchProcess', { user_eid, eid, poll: true }).catch(error => {
            // TODO: add error handling?
          })
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

export const v2_action_cancelProcess = ({ commit, dispatch }, { user_eid, eid }) => {
  commit(types.CANCELING_PROCESS, { eid })

  return api.v2_cancelProcess(user_eid, eid).then(response => {
    var process = response.data
    commit(types.CANCELED_PROCESS, { process })
    return response
  }).catch(error => {
    return error
  })
}

export const v2_action_runProcess = ({ commit, dispatch }, { user_eid, eid, cfg }) => {
  commit(types.STARTING_PROCESS, { eid })

  dispatch('v2_action_fetchProcess', { user_eid, eid, poll: true }).catch(error => {
    // TODO: add error handling?
  })

  return api.v2_runProcess(user_eid, eid, cfg).then(response => {
    var process = { eid, process_status: PROCESS_STATUS_RUNNING }
    commit(types.STARTED_PROCESS, { process })
    return response
  }).catch(error => {
    dispatch('v2_action_fetchProcess', { user_eid, eid }).catch(error => {
      // TODO: add error handling?
    })
    return error
  })
}

// ----------------------------------------------------------------------- //

export const v2_action_fetchProcessLog = ({ commit, dispatch }, { user_eid, eid }) => {
  commit(types.FETCHING_PROCESS_LOG, { eid, fetching: true })

  return api.v2_fetchProcessLog(user_eid, eid).then(response => {
    var log = response.data
    commit(types.FETCHED_PROCESS_LOG, { eid, log })
    commit(types.FETCHING_PROCESS_LOG, { eid, fetching: false })
    return response
  }).catch(error => {
    commit(types.FETCHING_PROCESS_LOG, { eid, fetching: false })
    return error
  })
}

// ----------------------------------------------------------------------- //

export const v2_action_fetchProcessSummary = ({ commit, dispatch }, { user_eid }) => {
  commit(types.FETCHING_PROCESS_SUMMARY, { fetching: true })

  return api.v2_fetchProcessSummary(user_eid).then(response => {
    var items = response.data
    commit(types.FETCHED_PROCESS_SUMMARY, { items })
    commit(types.FETCHING_PROCESS_SUMMARY, { fetching: false })
    return response
  }).catch(error => {
    commit(types.FETCHING_PROCESS_SUMMARY, { fetching: false })
    return error
  })
}
