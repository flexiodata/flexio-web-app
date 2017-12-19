import { TASK_OP_SELECT }  from '../../constants/task-type'
import { COMMAND_NAME_SELECT_COLUMN }  from '../../constants/command-name'

const DEFAULT_JSON = {
  'name': 'Select Columns',
  'type': TASK_OP_SELECT,
  'params': {
    'columns': []
  },
  'version': 1,
  'description': ''
}

export default {
  getCmd(json) {
    var cols = _.get(json, 'params.columns', [])
    cols = cols.join(' ')

    return COMMAND_NAME_SELECT_COLUMN + ' ' + cols
  },

  getJson(args) {
    var items = args || []

    var json = _.assign({}, DEFAULT_JSON)
    _.set(json, 'params.columns', items)

    return JSON.stringify(json, null, 2)
  }
}
