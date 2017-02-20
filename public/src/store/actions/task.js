import api from '../../api'
import * as types from '../mutation-types'

// ----------------------------------------------------------------------- //

export const createPipeTask = ({ commit }, { eid, attrs }) => {
  var insert_idx = _.get(attrs, 'index', -1)

  commit(types.CREATING_TASK, { eid, attrs, insert_idx })

  return api.createPipeTask({ eid, attrs }).then(response => {
    // success callback
    commit(types.CREATED_TASK, { eid, attrs: response.body, insert_idx })
    return response
  }, response => {
    // error callback
    return response
  })
}

export const updatePipeTask = ({ commit }, { eid, task_eid, attrs }) => {
  commit(types.UPDATING_TASK, { eid, task_eid, attrs })

  return api.updatePipeTask({ eid, task_eid, attrs }).then(response => {
    // success callback
    commit(types.UPDATED_TASK, { eid, task_eid, attrs: response.body })
    return response
  }, response => {
    // error callback
    return response
  })
}

export const deletePipeTask = ({ commit }, { eid, task_eid }) => {
  commit(types.DELETING_TASK, { eid, task_eid })

  return api.deletePipeTask({ eid, task_eid }).then(response => {
    // success callback
    commit(types.DELETED_TASK, { eid, task_eid })
    return response
  }, response => {
    // error callback
    return response
  })
}
