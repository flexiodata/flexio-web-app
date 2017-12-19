import { TASK_OP_CONVERT } from '../../constants/task-type'
import { COMMAND_NAME_CONVERT }  from '../../constants/command-name'
import * as connections from '../../constants/connection-info'
import * as convert from '../../constants/tasks/convert'

const CONVERT_SHORTHAND_CSV = 'csv'
const CONVERT_SHORTHAND_TSV = 'tsv'

const DEFAULT_JSON = {
  'name': 'Convert File',
  'type': TASK_OP_CONVERT,
  'params': {
    'input': {
      'format': convert.CONVERT_FORMAT_DELIMITED_TEXT,
      'delimiter': convert.CONVERT_DELIMITER_NONE,
      'text_qualifier': convert.CONVERT_TEXT_QUALIFIER_NONE,
      'header_row': false
    }
  },
  'version': 1,
  'description': ''
}

export default {
  getCmd(json) {
    var format = _.get(json, 'params.input.format', '')
    var delimiter = _.get(json, 'params.input.delimiter', '')
    var text_qualifier = _.get(json, 'params.input.text_qualifier', '')
    var header_row = _.get(json, 'params.input.header_row', '')

    if (delimiter == convert.CONVERT_DELIMITER_COMMA &&
        text_qualifier && convert.CONVERT_TEXT_QUALIFIER_DOUBLE_QUOTE &&
        header_row)
    {
      return COMMAND_NAME_CONVERT + ' ' + CONVERT_SHORTHAND_CSV
    }

    if (delimiter == convert.CONVERT_DELIMITER_TAB &&
        text_qualifier && convert.CONVERT_TEXT_QUALIFIER_DOUBLE_QUOTE &&
        header_row)
    {
      return COMMAND_NAME_CONVERT + ' ' + CONVERT_SHORTHAND_TSV
    }

    if (delimiter.length > 0)
      delimiter = ' ' + delimiter

    if (text_qualifier.length > 0)
      text_qualifier = ' ' + text_qualifier

    if (header_row.length > 0)
      header_row = ' ' + header_row

    return COMMAND_NAME_CONVERT + delimiter + text_qualifier + header_row
  },

  getJson(args) {
    var delimiter = convert.CONVERT_DELIMITER_NONE
    var text_qualifier = convert.CONVERT_TEXT_QUALIFIER_NONE
    var header_row = false

    if (_.nth(args, 0) == CONVERT_SHORTHAND_CSV)
    {
      // 'convert csv' uses CSV file defaults
      delimiter = convert.CONVERT_DELIMITER_COMMA
      text_qualifier = convert.CONVERT_TEXT_QUALIFIER_DOUBLE_QUOTE
      header_row = true
    }
     else if (_.nth(args, 0) == CONVERT_SHORTHAND_TSV)
    {
      // 'convert tsv' uses TSV file defaults
      delimiter = convert.CONVERT_DELIMITER_TAB
      text_qualifier = convert.CONVERT_TEXT_QUALIFIER_DOUBLE_QUOTE
      header_row = true
    }
     else
    {
      // otherwise, it's the open range...
      delimiter = this.findInArgs(args, convert.CONVERT_ALL_DELIMITERS, convert.CONVERT_DELIMITER_NONE)
      text_qualifier = this.findInArgs(args, convert.CONVERT_ALL_TEXT_QUALIFIERS, convert.CONVERT_TEXT_QUALIFIER_NONE)
      header_row = _.includes(args, 'header_row')
    }

    var json = _.assign({}, DEFAULT_JSON)
    _.set(json, 'params.input.delimiter', delimiter)
    _.set(json, 'params.input.text_qualifier', text_qualifier)
    _.set(json, 'params.input.header_row', header_row)

    return JSON.stringify(json, null, 2)
  },

  findInArgs: function(args, to_find, default_val) {
    return _.find(args, function(a) {
      return _.find(to_find, function(t) {
        return _.toLower(t) == _.toLower(a)
      }) !== undefined
    }) || default_val

    return undefined
  }
}
