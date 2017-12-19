import * as task_types from '../../constants/task-type'
import * as command_names  from '../../constants/command-name'
import convert from './convert'
import execute from './execute'
import findreplace from './find-replace'
import input from './input'
import limit from './limit'
import selectcolumn from './selectcolumn'
import sort from './sort'

export default {
  cmdToJson: function(cmd) {
    if (!_.isString(cmd) || cmd.length == 0)
      return

    var parts = this.splitBySpaces(cmd)
    if (parts.length > 0)
    {
      var first = _.toLower(parts[0])
      var rest = _.slice(parts, 1)

      switch (first)
      {
        case command_names.COMMAND_NAME_CONVERT:       return convert.getJson(rest)
        case command_names.COMMAND_NAME_EXECUTE:       return execute.getJson(rest)
        case command_names.COMMAND_NAME_FIND_REPLACE:  return findreplace.getJson(rest)
        case command_names.COMMAND_NAME_INPUT:         return input.getJson(rest)
        case command_names.COMMAND_NAME_LIMIT:         return limit.getJson(rest)
        case command_names.COMMAND_NAME_SELECT_COLUMN: return selectcolumn.getJson(rest)
        case command_names.COMMAND_NAME_SORT:          return sort.getJson(rest)
      }
    }
  },

  jsonToCmd: function(json) {
    if (!_.isObject(json))
      return ''

    switch (_.get(json, 'type'))
    {
      case task_types.TASK_OP_CONVERT:       return convert.getCmd(json)
      case task_types.TASK_OP_EXECUTE:       return execute.getCmd(json)
      case task_types.TASK_OP_FIND_REPLACE:  return findreplace.getCmd(json)
      case task_types.TASK_OP_INPUT:         return input.getCmd(json)
      case task_types.TASK_OP_LIMIT:         return limit.getCmd(json)
      case task_types.TASK_OP_SELECT:        return selectcolumn.getCmd(json)
      case task_types.TASK_OP_SORT:          return sort.getCmd(json)
    }

    return ''
  },

  splitBySpaces: function(str) {
    // split by spaces, ignoring strings in double quotes
    // ref. option 1: http://stackoverflow.com/questions/16261635/javascript-split-string-by-space-but-ignore-space-in-quotes-notice-not-to-spli
    // ref. option 2: http://stackoverflow.com/questions/17465099/regex-how-to-split-string-with-space-and-double-quote
    var parts = str.match(/(?:[^\s"]+|"[^"]*")+/g)
    parts = _.map(parts, function(t) { return _.trim(t, '"') })
    return parts
  }
}
