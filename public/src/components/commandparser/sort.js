import { TASK_TYPE_SORT } from '../../constants/task-type'
import { COMMAND_NAME_SORT }  from '../../constants/command-name'
import { SORT_DIRECTION_ASCENDING, SORT_DIRECTION_DESCENDING } from '../../constants/tasks/sort'

const DEFAULT_JSON = {
  'name': 'Sort',
  'type': TASK_TYPE_SORT,
  'params': {
    'order': []
  },
  'version': 1,
  'description': ''
}

export default {
  getCmd(json) {
    var args = ''

    _.each(_.get(json, 'params.order', []), (val, idx) => {
      args += ' ' + _.get(val, 'expression', '')
      if (_.toLower(_.get(val, 'direction')) == SORT_DIRECTION_DESCENDING)
        args += ' ' + SORT_DIRECTION_DESCENDING
    })

    return COMMAND_NAME_SORT + args
  },

  getJson(args) {
    var order = [].concat(args)

    _.each(order, (val, idx, collection) => {
      var v = _.toLower(val)

      if (v == SORT_DIRECTION_ASCENDING)
      {
        // do nothing
      }
       else if (v == SORT_DIRECTION_DESCENDING)
      {
        if (idx > 0)
        {
          _.set(collection, '['+(idx-1)+'].direction', SORT_DIRECTION_DESCENDING)
          collection[idx] = null
        }
      }
       else
      {
        collection[idx] = {
          'expression': val,
          'direction': SORT_DIRECTION_ASCENDING
        }
      }
    })

    order = _.compact(order)

    var json = _.extend({}, DEFAULT_JSON)
    _.set(json, 'params.order', order)

    return JSON.stringify(json, null, 2)
  }
}
