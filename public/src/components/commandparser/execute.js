import { TASK_OP_EXECUTE } from '../../constants/task-op'
import { COMMAND_NAME_EXECUTE }  from '../../constants/command-name'

const DEFAULT_JSON = {
  'name': 'Execute',
  'type': TASK_OP_EXECUTE,
  'params': {
    'type': '',
    'code': ''
  },
  'version': 1,
  'description': ''
}

export default {
  getCmd(json) {
    var type = _.get(json, 'params.type', '')
    var code = _.get(json, 'params.code', '')

    if (type.length > 0)
      type = ' ' + type

    // decode base64
    if (code.length > 0)
      code = ' "' + atob(code) + '"'

    return COMMAND_NAME_EXECUTE + type + code
  },

  getJson(args) {
    var type = _.defaultTo(_.nth(args, 0), '')
    var code = _.defaultTo(_.nth(args, 1), '')

    // encode as base64
    if (code.length > 0)
      code = btoa(code)

    var json = _.assign({}, DEFAULT_JSON)
    _.set(json, 'params.type', type)
    _.set(json, 'params.code', code)

    return JSON.stringify(json, null, 2)
  }
}
