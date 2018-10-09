import * as types from '../mutation-types'
import { addPipe, updatePipe, addProcess, updateProcess } from './helpers'

export default {

  // ----------------------------------------------------------------------- //

  [types.FETCHING_PROCESSES] (state, { pipe_eid, fetching }) {
    // if we're trying to fetch processes for a pipe that's not
    // in our store, add a very basic pipe object to the store
    if (fetching === true && !_.has(state.objects, pipe_eid))
    {
      addPipe(state, pipe_eid, { processes_fetching: fetching })
    }
     else
    {
      // otherwise, just set the pipe's processes fetching flag
      updatePipe(state, pipe_eid, { processes_fetching: fetching })
    }
  },

  [types.FETCHED_PROCESSES] (state, { pipe_eid, processes }) {
    addProcess(state, processes)

    // set the pipe's process fetched flag
    updatePipe(state, pipe_eid, { processes_fetched: true })
  },

  // ----------------------------------------------------------------------- //

  [types.CREATING_PROCESS] (state, { attrs }) {},

  [types.CREATED_PROCESS] (state, { attrs, process }) {
    addProcess(state, process, { is_fetched: true })
  },

  [types.FETCHING_PROCESS] (state, { eid, fetching }) {
    // if we're trying to fetch a process that's not
    // in our store, add a very basic process object to the store
    if (fetching === true && !_.has(state.objects, eid))
    {
      addProcess(state, eid, { is_fetching: fetching })
    }
     else
    {
      // otherwise, just set the pipe's fetching flag
      updateProcess(state, eid, { is_fetching: fetching })
    }
  },

  [types.FETCHED_PROCESS] (state, process) {
    addProcess(state, process, { is_fetched: true })
  },

  // ----------------------------------------------------------------------- //

  [types.FETCHING_PROCESS_LOG] (state, { eid, fetching }) {
  },

  [types.FETCHED_PROCESS_LOG] (state, process) {
    addProcess(state, process, {})
  },

  // ----------------------------------------------------------------------- //

  [types.FETCHING_PROCESS_SUMMARY] (state, { fetching }) {
  },

  [types.FETCHED_PROCESS_SUMMARY] (state, { items }) {
    var pipes = _.map(items, (item) => {
      return _.assign({}, item.pipe, { stats: item })
    })

    _.each(pipes, (pipe) => {
      if (_.get(pipe, 'eid', '').length == 0) {
        return
      }

      if (!_.has(state.objects, pipe.eid)) {
        //addPipe(state, pipe)
      } else {
        // otherwise, just set the pipe's fetching flag
        updatePipe(state, pipe.eid, { stats: pipe.stats })
      }
    })
  },

  // ----------------------------------------------------------------------- //

  [types.CANCELING_PROCESS] (state, { eid }) {
  },

  [types.CANCELED_PROCESS] (state, process) {
    updateProcess(state, process)
  },

  // ----------------------------------------------------------------------- //

  [types.STARTING_PROCESS] (state, { eid }) {
  },

  [types.STARTED_PROCESS] (state, process) {
    updateProcess(state, process)
  }

  // ----------------------------------------------------------------------- //

}
