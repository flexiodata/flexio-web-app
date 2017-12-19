import { TASK_OP_FIND_REPLACE } from '../../constants/task-type'
import { COMMAND_NAME_FIND_REPLACE }  from '../../constants/command-name'

import {
  FIND_REPLACE_LOCATION_ANY,
  FIND_REPLACE_LOCATION_LEADING,
  FIND_REPLACE_LOCATION_TRAILING,
  FIND_REPLACE_LOCATION_LEADING_TRAILING,
  FIND_REPLACE_LOCATION_WHOLE
} from '../../constants/tasks/find-replace'

const MATCH_CASE_FLAG = 'match_case'
const ALL_COLUMNS = '*'

const DEFAULT_JSON = {
  'name': 'Find and Replace',
  'type': TASK_OP_FIND_REPLACE,
  'params': {
    'find': '',
    'columns': [ALL_COLUMNS],
    'replace': '',
    'location': FIND_REPLACE_LOCATION_ANY,
    'match_case': false
  },
  'version': 1,
  'description': ''
}

export default {
  getCmd(json) {
    var find_val = _.get(json, 'params.find', '')
    var replace_val = _.get(json, 'params.replace', '')
    var match_case = _.get(json, 'params.match_case', false)
    var location = _.get(json, 'params.location', FIND_REPLACE_LOCATION_ANY)
    var columns = _.get(json, 'params.columns', [ALL_COLUMNS])

    if (find_val.length > 0)
      find_val = ' ' + find_val

    if (replace_val.length > 0)
      replace_val = ' ' + replace_val

    // only show if match_case is true
    if (match_case)
      match_case = ' ' + MATCH_CASE_FLAG
       else
      match_case = ''

    // if location is 'any', don't output anything
    if (location != FIND_REPLACE_LOCATION_ANY)
      location = ' ' + 'loc:' +  location
       else
      location = ''

    if (columns.length == 1 && _.nth(columns, 0) == ALL_COLUMNS)
    {
      // if all columns, don't output anything
      columns = ''
    }
     else
    {
      columns = _.map(columns, (col) => {
        return _.includes(col, ' ') ? '"'+col+'"' : col
      })
      columns = ' ' + 'cols:' + columns.join(',')
    }

    return COMMAND_NAME_FIND_REPLACE + find_val + replace_val + match_case + location + columns
  },

  getJson(args) {
    var find = _.nth(args, 0) || ''
    var replace = _.nth(args, 1) || ''
    var cols = this.parseCols(args) || [ALL_COLUMNS]
    var location = this.parseLocation(args) || FIND_REPLACE_LOCATION_ANY
    var match_case = _.includes(args, MATCH_CASE_FLAG)

    var json = _.assign({}, DEFAULT_JSON)
    _.set(json, 'params.find', find)
    _.set(json, 'params.replace', replace)
    _.set(json, 'params.columns', cols)
    _.set(json, 'params.location', location)
    _.set(json, 'params.match_case', match_case)

    return JSON.stringify(json, null, 2)
  },

  parseCols: function(args) {
    var cols = _.find(args, function(a) {
      return _.includes(a, 'cols:')
    })

    if (_.isNil(cols))
      return null

    cols = cols.substring(5)
    cols = cols.split(',')
    cols = _.map(cols, function(t) { return _.trim(t, '"') })
    return cols
  },

  parseLocation: function(args) {
    var loc = _.find(args, function(a) {
      return _.includes(a, 'loc:')
    })

    if (_.isNil(loc))
      return null

    loc = loc.substring(4)

    var locations = [
      FIND_REPLACE_LOCATION_ANY,
      FIND_REPLACE_LOCATION_LEADING,
      FIND_REPLACE_LOCATION_TRAILING,
      FIND_REPLACE_LOCATION_LEADING_TRAILING,
      FIND_REPLACE_LOCATION_WHOLE
    ]

    return _.find(locations, function(t) {
      return _.toLower(t) == _.toLower(loc)
    })

    return null
  }
}
