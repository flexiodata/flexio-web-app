export const SCHEDULE_STATUS_ACTIVE             = 'A'
export const SCHEDULE_STATUS_INACTIVE           = 'I'

export const SCHEDULE_FREQUENCY_ONE_MINUTE      = 'one-minute'
export const SCHEDULE_FREQUENCY_FIVE_MINUTES    = 'five-minutes'
export const SCHEDULE_FREQUENCY_FIFTEEN_MINUTES = 'fifteen-minutes'
export const SCHEDULE_FREQUENCY_THIRTY_MINUTES  = 'thirty-minutes'
export const SCHEDULE_FREQUENCY_HOURLY          = 'hourly'
export const SCHEDULE_FREQUENCY_DAILY           = 'daily'
export const SCHEDULE_FREQUENCY_WEEKLY          = 'weekly'
export const SCHEDULE_FREQUENCY_MONTHLY         = 'monthly'

export const SCHEDULE_MONTH_DAY_FIRST           = 1
export const SCHEDULE_MONTH_DAY_FIFTEENTH       = 15
export const SCHEDULE_MONTH_DAY_LAST            = 'last'

export const SCHEDULE_WEEK_DAY_MON              = 'mon'
export const SCHEDULE_WEEK_DAY_TUE              = 'tue'
export const SCHEDULE_WEEK_DAY_WED              = 'wed'
export const SCHEDULE_WEEK_DAY_THU              = 'thu'
export const SCHEDULE_WEEK_DAY_FRI              = 'fri'
export const SCHEDULE_WEEK_DAY_SAT              = 'sat'
export const SCHEDULE_WEEK_DAY_SUN              = 'sun'

export const SCHEDULE_DEFAULTS = {
  frequency: SCHEDULE_FREQUENCY_DAILY,
  timezone: 'UTC',
  days: [],
  times: [{ hour: 8, minute: 0 }]
}
