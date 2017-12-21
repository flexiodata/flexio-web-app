import { TASK_OP_EXECUTE } from '../../constants/task-op'
import { COMMAND_NAME_EXECUTE }  from '../../constants/command-name'

const DEFAULT_JSON = {
  'name': 'Execute',
  'op': TASK_OP_EXECUTE,
  'params': {
    'lang': '',
    'code': ''
  },
  'version': 1,
  'description': ''
}

export default {
  getCmd(json) {
    var lang = _.get(json, 'params.lang', '')
    var code = _.get(json, 'params.code', '')

    if (lang.length > 0)
      lang = ' ' + lang

    // decode base64
    if (code.length > 0)
      code = ' "' + atob(code) + '"'

    return COMMAND_NAME_EXECUTE + lang + code
  },

  getJson(args) {
    var lang = _.defaultTo(_.nth(args, 0), '')
    var code = _.defaultTo(_.nth(args, 1), '')

    // encode as base64
    if (code.length > 0)
      code = btoa(code)

    var json = _.assign({}, DEFAULT_JSON)
    _.set(json, 'params.lang', lang)
    _.set(json, 'params.code', code)

    return JSON.stringify(json, null, 2)
  }
}
