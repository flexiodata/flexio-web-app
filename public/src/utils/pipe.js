export const getSyntaxStr = (team_name, pipe_name, syntax) => {
  var suffix = syntax.length > 0 ? `, ${syntax}` : ''
  return `=FLEX("${team_name}/${pipe_name}"${suffix})`
}

/*
import * as sched from '@/constants/schedule'
//import doctrine from 'doctrine'
import moment from 'moment'

export const getTimeStr = (s) => {
  var times = _.get(s, 'times', [])
  times = _.map(times, (t) => {
    return moment().hour(t.hour).minute(t.minute).format('LT');
  })
  return times.join(', ')
}

export const getDayStr = (s) => {
  var days = _.get(s, 'days', [])
  days = _.map(days, (d) => {
    return moment().isoWeekday(d).format('dddd')
  })
  return days.join(', ')
}

export const getMonthDayStr = (s) => {
  var days = _.get(s, 'days', [])
  days = _.map(days, (d) => {
    switch (d) {
      case 1:
        return 'first day'
      case 15:
        return 'fifteenth day'
      case 'last':
        return 'last day'
    }
  })
  return days.join(', ')
}

export const getIdentifier = (pipe) => {
  var pname = _.get(pipe, 'name', '')
  return pname.length > 0 ? pname : _.get(pipe, 'eid', '')
}

export const getDeployScheduleStr = (s) => {
  if (_.isNil(s)) {
    return ''
  }

  switch (_.get(s, 'frequency', '')) {
    case sched.SCHEDULE_FREQUENCY_ONE_MINUTE:
      return 'Every minute'
    case sched.SCHEDULE_FREQUENCY_FIVE_MINUTES:
      return 'Every 5 minutes'
    case sched.SCHEDULE_FREQUENCY_FIFTEEN_MINUTES:
      return 'Every 15 minutes'
    case sched.SCHEDULE_FREQUENCY_THIRTY_MINUTES:
      return 'Every 30 minutes'
    case sched.SCHEDULE_FREQUENCY_HOURLY:
      return 'Every hour'
    case sched.SCHEDULE_FREQUENCY_DAILY:
      return 'Every day at ' + getTimeStr(s)
    case sched.SCHEDULE_FREQUENCY_WEEKLY:
      return 'Every ' + getDayStr(s) + ' of every week at ' + getTimeStr(s)
    case sched.SCHEDULE_FREQUENCY_MONTHLY:
      return 'On the ' + getMonthDayStr(s) + ' of every month at ' + getTimeStr(s)
  }

  return ''
}

export const getDeployApiUrl = (identifier) => {
  return 'https://api.flex.io/v1/me/pipes/' + identifier + '/run'
}

export const getDeployRuntimeUrl = (eid) => {
  return 'https://' + window.location.hostname + '/app/me/pipes/' + eid + '/run'
}
*/

/*
export const getJsDocObject = (pipe) => {
  var desc = _.get(pipe, 'description', '')
  return doctrine.parse(desc, { unwrap: true })
}

export const getSpreadsheetSyntaxStr = (team_name, pipe, return_html) => {
  function getParamMarkup(param) {
    if (return_html === true) {
      return `<span title="${param.description}">${param.name}</span>`
    } else {
      return `${param.name}`
    }
  }

  var name = pipe.name
  var jsdoc_obj = getJsDocObject(pipe)
  var tags = _.get(jsdoc_obj, 'tags', [])
  var params = _.filter(tags, { title: 'param' })
  var param_names = _.map(params, p => getParamMarkup(p))
  var output_params = [`"${team_name}/${name}"`]
  output_params = output_params.concat(param_names)
  return '=FLEX(' + output_params.join(', ') + ')'
}
*/
