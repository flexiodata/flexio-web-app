import { TASK_OP_LIMIT } from '../../constants/task-type'
import { COMMAND_NAME_LIMIT }  from '../../constants/command-name'

const DEFAULT_JSON = {
  'name': 'Limit',
  'type': TASK_OP_LIMIT,
  'params': {
    'rows': 100
  },
  'version': 1,
  'description': ''
}

export default {
  getCmd(json) {
    var rows = _.get(json, 'params.rows', '')
    return COMMAND_NAME_LIMIT + ' ' + rows
  },

  getJson(args) {
    var rows = _.parseInt(_.nth(args, 0)) || 100

    var json = _.assign({}, DEFAULT_JSON)
    _.set(json, 'params.rows', rows)

    return JSON.stringify(json, null, 2)
  }
}
