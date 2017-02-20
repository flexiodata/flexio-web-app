import * as types from '../mutation-types'

export default {

  // ----------------------------------------------------------------------- //

  [types.CREATING_TASK] (state, { eid, attrs, insert_idx }) {},

  [types.CREATED_TASK] (state, { eid, attrs, insert_idx }) {
    // update the task if it exists in the store
    var task_eid = _.get(attrs, 'eid', '')
    var task_arr = _.get(state, 'objects.'+eid+'.task', [])
    var task_idx = _.findIndex(task_arr, { eid: task_eid })

    if (task_idx >= 0)
    {
      // update the existing task that we found; NOTE: we really shouldn't
      // ever be here if we're really creating a task...
      task_arr[task_idx] = attrs
      state.objects[eid].task = [].concat(task_arr)
    }
     else
    {
      // ...otherwise, insert the task at the specified index
      if (_.isNil(insert_idx) || insert_idx == -1 || insert_idx >= task_arr.length)
      {
        state.objects[eid].task = task_arr.concat([attrs])
      }
       else
      {
        task_arr.splice(insert_idx, 0, attrs)
        state.objects[eid].task = [].concat(task_arr)
      }
    }
  },

  [types.UPDATING_TASK] (state, { eid, task_eid, attrs }) {},

  [types.UPDATED_TASK] (state, { eid, task_eid, attrs }) {
    // update the task if it exists in the store
    var task_arr = _.get(state, 'objects.'+eid+'.task', [])
    var task_idx = _.findIndex(task_arr, { eid: task_eid })

    if (task_idx >= 0)
    {
      // update the existing task that we found
      task_arr[task_idx] = attrs
      state.objects[eid].task = [].concat(task_arr)
    }
     else
    {
      // ...otherwise, create the task and add it to the end of the task array;
      // NOTE: we really shouldn't ever be here if we're really editing a task...
      state.objects[eid].task = task_arr.concat([attrs])
    }
  },

  [types.DELETING_TASK] (state, { eid, task_eid }) {},

  [types.DELETED_TASK] (state, { eid, task_eid }) {
    // remove the task from the store
    var task_arr = _.get(state, 'objects.'+eid+'.task', [])

    _.remove(task_arr, function(t) {
      return t.eid == task_eid
    })

    state.objects[eid].task = [].concat(task_arr)
  }
}
