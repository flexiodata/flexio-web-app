import api from '../../api'
import * as types from '../mutation-types'
import {
  PROCESS_STATUS_PENDING,
  PROCESS_STATUS_WAITING,
  PROCESS_STATUS_RUNNING
} from '../../constants/process'

// ----------------------------------------------------------------------- //

export const fetchProcesses = ({ commit }, { attrs }) => {
  var pipe_eid = _.get(attrs, 'parent_eid', '')

  commit(types.FETCHING_PROCESSES, { pipe_eid, fetching: true })

  return api.fetchProcesses({ attrs }).then(response => {
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

export const createProcess = ({ commit, dispatch }, { attrs }) => {
  commit(types.CREATING_PROCESS, { attrs })

  return api.createProcess({ attrs }).then(response => {
    // success callback
    commit(types.CREATED_PROCESS, { attrs, process: response.body })

    // the 'run' parameter was specified which means
    // we've started the process; poll for the process
    var process = response.body
    if (attrs.run === true && process.eid)
      dispatch('fetchProcess', { eid: process.eid, poll: true })

    return response
  }, response => {
    // error callback
    return response
  })
}

export const fetchProcess = ({ commit, dispatch }, { eid, poll }) => {
  commit(types.FETCHING_PROCESS, { eid, fetching: true })

  return api.fetchProcess({ eid }).then(response => {
    // success callback
    commit(types.FETCHED_PROCESS, response.body)
    commit(types.FETCHING_PROCESS, { eid, fetching: false })

    if (poll == true)
    {
      // poll the process while it is still running
      var status = _.get(response.body, 'process_status')
      if (_.includes([
            PROCESS_STATUS_PENDING,
            PROCESS_STATUS_WAITING,
            PROCESS_STATUS_RUNNING
          ], status))
      {
        _.delay(function() {
          var eid = _.get(response.body, 'eid', '')
          dispatch('fetchProcess', { eid, poll: true })
        }, 500)
      }
    }

    return response
  }, response => {
    // error callback
    commit(types.FETCHING_PROCESS, { eid, fetching: false })
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

export const cancelProcess = ({ commit, dispatch }, { eid }) => {
  commit(types.CANCELING_PROCESS, { eid, canceling: true })

  return api.cancelProcess({ eid }).then(response => {
    // success callback
    commit(types.CANCELED_PROCESS, { process: response.body })
    commit(types.CANCELING_PROCESS, { eid, canceling: false })
    return response
  }, response => {
    // error callback
    commit(types.CANCELING_PROCESS, { eid, canceling: false })
    return response
  })
}

export const runProcess = ({ commit, dispatch }, { eid, attrs }) => {
  commit(types.STARTING_PROCESS, { eid, starting: true })

  return api.runProcess({ eid, attrs }).then(response => {
    // success callback
    commit(types.STARTED_PROCESS, { process: { eid } })
    commit(types.STARTING_PROCESS, { eid, starting: false })

    dispatch('fetchProcess', { eid, poll: true })

    return response
  }, response => {
    // error callback
    commit(types.STARTING_PROCESS, { eid, starting: false })
    return response
  })
}
