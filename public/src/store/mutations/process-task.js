import * as types from '../mutation-types'
import { addProcessTask, updateProcessTask } from './helpers'

export default {

  // ----------------------------------------------------------------------- //

  [types.FETCHING_PROCESS_TASK_INPUT_INFO] (state, { eid, task_eid, fetching }) {
    var process_task_id = eid + '--' + task_eid

    if (fetching === true && !_.has(state.objects, process_task_id))
    {
      addProcessTask(state, process_task_id, { input_fetching: fetching })
    }
     else
    {
      updateProcessTask(state, process_task_id, { input_fetching: fetching })
    }
  },

  [types.FETCHED_PROCESS_TASK_INPUT_INFO] (state, { eid, task_eid, columns }) {
    var process_task_id = eid + '--' + task_eid

    updateProcessTask(state, process_task_id, {
      // TODO: use 'eid' here for now, even though it's a store id; we should
      //       migrate the store to start using 'store_id' and fallback to 'eid'
      eid: process_task_id,
      input_columns: columns,
      input_fetched: true
    })
  },

  [types.FETCHING_PROCESS_TASK_OUTPUT_INFO] (state, { eid, task_eid, fetching }) {
    var process_task_id = eid + '--' + task_eid

    if (fetching === true && !_.has(state.objects, process_task_id))
    {
      addProcessTask(state, process_task_id, { output_fetching: fetching })
    }
     else
    {
      updateProcessTask(state, process_task_id, { output_fetching: fetching })
    }
  },

  [types.FETCHED_PROCESS_TASK_OUTPUT_INFO] (state, { eid, task_eid, columns }) {
    var process_task_id = eid + '--' + task_eid

    updateProcessTask(state, process_task_id, {
      // TODO: use 'eid' here for now, even though it's a store id; we should
      //       migrate the store to start using 'store_id' and fallback to 'eid'
      eid: process_task_id,
      output_columns: columns,
      output_fetched: true
    })
  }

  // ----------------------------------------------------------------------- //

}
