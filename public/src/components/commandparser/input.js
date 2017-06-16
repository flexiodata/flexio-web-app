import { TASK_TYPE_INPUT } from '../../constants/task-type'
import { COMMAND_NAME_INPUT }  from '../../constants/command-name'
import * as connections from '../../constants/connection-info'

import {
  CONNECTION_TYPE_UNKNOWN,
  CONNECTION_TYPE_HTTP,
  CONNECTION_TYPE_RSS,
  CONNECTION_TYPE_UPLOAD
} from '../../constants/connection-type'

const DEFAULT_JSON = {
  'name': 'Input',
  'type': TASK_TYPE_INPUT,
  'params': {
    'items': [],
    'connection': {
      'connection_type': CONNECTION_TYPE_UNKNOWN
    }
  },
  'version': 1,
  'description': ''
}

export default {
  getCmd(json) {
    var items = ''

    var connection_eid = _.get(json, 'params.connection.eid', '')
    var connection = _.find(connections, { connection_type: _.get(json, 'params.connection.connection_type') })
    var cmd_alias = _.get(connection, 'cmd_alias', '')

    _.each(_.get(json, 'params.items', []), (val, idx) => {
      items += ' ' + _.get(val, 'path', '')
    })

    if (cmd_alias.length > 0)
      cmd_alias = ' ' + cmd_alias

    if (connection_eid.length > 0)
      connection_eid = ' ' + connection_eid

    return COMMAND_NAME_INPUT + cmd_alias + connection_eid + items
  },

  getJson(args) {
    var connection = _.find(connections, { cmd_alias: _.toLower(_.nth(args, 0)) })
    var connection_type = _.get(connection, 'connection_type', CONNECTION_TYPE_UNKNOWN)

    var connection_eid
    var items_start = 1

    if (this.isEidConnection(connection_type))
    {
      connection_eid = _.nth(args, 1)
      items_start++
    }

    var items = _.map(_.slice(args, items_start), function(item) {
      var path = item.replace(/\\\\/g, '\\')
      var name = path.replace(/\\/g, '/')
      var idx = Math.max(name.lastIndexOf('/'), name.lastIndexOf('?'))

      return {
        name: idx+1 == name.length ? name : name.substring(idx+1),
        path: path
      }
    })

    var json = _.assign({}, DEFAULT_JSON)
    _.set(json, 'params.items', items)
    _.set(json, 'params.connection.eid', connection_eid)
    _.set(json, 'params.connection.connection_type', connection_type)

    return JSON.stringify(json, null, 2)
  },

  isEidConnection(ctype) {
    return _.includes([
      CONNECTION_TYPE_HTTP,
      CONNECTION_TYPE_RSS,
      CONNECTION_TYPE_UPLOAD
    ], ctype) ? false : true
  }
}
