import api from '../../api'
import * as types from '../mutation-types'

// ----------------------------------------------------------------------- //

export const createPipeTask = ({ commit, state }, { eid, attrs }) => {
  var pipe = _.find(state.objects, { eid }, {})
  var task = _.get(pipe, 'task', { op: 'sequence', params: {} })
  var items = _.get(pipe, 'task.params.items', [])
  var insert_idx = _.get(attrs, 'index', task.length)
  var task_attrs = _.omit(attrs, ['index'])

  items = [].concat(items)
  items.splice(insert_idx, 0, task_attrs)

  task = _.cloneDeep(task)
  _.set(task, 'params.items', items)

  var attrs = { task }

  //commit(types.CREATING_TASK, { eid, attrs, insert_idx })
  commit(types.UPDATING_PIPE, { eid, attrs })

  return api.updatePipe({ eid, attrs }).then(response => {
    // success callback
    //commit(types.CREATED_TASK, { eid, attrs: response.body, insert_idx })
    commit(types.UPDATED_PIPE, { eid, attrs: response.body })
    return response
  }, response => {
    // error callback
    return response
  })
}

export const updatePipeTask = ({ commit, state }, { eid, task_eid, attrs }) => {
  var pipe = _.find(state.objects, { eid }, {})
  var task = _.get(pipe, 'task', [])
  var replace_idx = _.findIndex(task, { eid: task_eid })

  if (replace_idx == -1)
    return

  task.splice(replace_idx, 1, attrs)

  var attrs = { task }

  //commit(types.UPDATING_TASK, { eid, task_eid, attrs })
  commit(types.UPDATING_PIPE, { eid, attrs })

  return api.updatePipe({ eid, attrs }).then(response => {
    // success callback
    //commit(types.UPDATED_TASK, { eid, task_eid, attrs: response.body })
    commit(types.UPDATED_PIPE, { eid, attrs: response.body })
    return response
  }, response => {
    // error callback
    return response
  })
}

export const deletePipeTask = ({ commit, state }, { eid, task_eid }) => {
  var pipe = _.find(state.objects, { eid }, {})
  var task = _.get(pipe, 'task', [])
  var delete_idx = _.findIndex(task, { eid: task_eid })

  if (delete_idx == -1)
    return

  task.splice(delete_idx, 1)

  var attrs = { task }

  //commit(types.DELETING_TASK, { eid, task_eid })
  commit(types.UPDATING_PIPE, { eid, attrs })

  return api.updatePipe({ eid, attrs }).then(response => {
    // success callback
    //commit(types.DELETED_TASK, { eid, task_eid })
    commit(types.UPDATED_PIPE, { eid, attrs: response.body })
    return response
  }, response => {
    // error callback
    return response
  })
}
