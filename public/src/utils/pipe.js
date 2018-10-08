import * as sched from '../constants/schedule'
import moment from 'moment'

const getTimeStr = (s) => {
  var times = _.get(s, 'times', [])
  times = _.map(times, (t) => {
    return moment().hour(t.hour).minute(t.minute).format('LT');
  })
  return times.join(', ')
}

const getDayStr = (s) => {
  var days = _.get(s, 'days', [])
  days = _.map(days, (d) => {
    return moment().isoWeekday(d).format('dddd')
  })
  return days.join(', ')
}

const getMonthDayStr = (s) => {
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

const getDeployScheduleStr = (s) => {
  switch (s.frequency) {
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
}

const getDeployApiUrl = (identifier) => {
  return 'https://api.flex.io/v1/me/pipes/' + identifier
}

const getDeployRuntimeUrl = (eid) => {
  return 'https://' + window.location.hostname + '/app/pipes/' + eid + '/run'
}

export default {
  getDeployScheduleStr,
  getDeployApiUrl,
  getDeployRuntimeUrl
}
