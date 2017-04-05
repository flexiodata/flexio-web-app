import * as types from './task-type'

/* task info */

const CALC_FIELD = {
  name: 'Calculation',
  type: types.TASK_TYPE_CALC,
  icon: 'flash_on'
}

const CONVERT = {
  name: 'Convert',
  type: types.TASK_TYPE_CONVERT,
  icon: 'settings'
}

const COPY = {
  name: 'Copy',
  type: types.TASK_TYPE_COPY,
  icon: 'content_copy'
}

const CUSTOM = {
  name: 'Custom',
  type: types.TASK_TYPE_CUSTOM,
  icon: 'build'
}

const DISTINCT = {
  name: 'Distinct',
  type: types.TASK_TYPE_DISTINCT,
  icon: 'clear_all'
}

const DUPLICATE = {
  name: 'Duplicate',
  type: types.TASK_TYPE_DUPLICATE,
  icon: 'content_copy'
}

const EMAIL_SEND = {
  name: 'Email',
  type: types.TASK_TYPE_EMAIL_SEND,
  icon: 'mail_outline'
}

const EXECUTE = {
  name: 'Execute',
  type: types.TASK_TYPE_EXECUTE,
  icon: 'code'
}

const FIND_REPLACE = {
  name: 'Find/Replace',
  type: types.TASK_TYPE_FIND_REPLACE,
  icon: 'find_replace'
}

const FILTER = {
  name: 'Filter',
  type: types.TASK_TYPE_FILTER,
  icon: 'filter_list'
}

const GROUP = {
  name: 'Group',
  type: types.TASK_TYPE_GROUP,
  icon: 'functions'
}

const INPUT = {
  name: 'Input',
  type: types.TASK_TYPE_INPUT,
  icon: 'input'
}

const LIMIT = {
  name: 'Limit',
  type: types.TASK_TYPE_LIMIT,
  icon: 'play_for_work'
}

const MERGE = {
  name: 'Merge',
  type: types.TASK_TYPE_MERGE,
  icon: 'call_merge'
}

const NOP = {
  name: 'No Op.',
  type: types.TASK_TYPE_NOP,
  icon: 'build'
}

const OUTPUT = {
  name: 'Output',
  type: types.TASK_TYPE_OUTPUT,
  icon: 'input'
}

const PROMPT = {
  name: 'Prompt',
  type: types.TASK_TYPE_PROMPT,
  icon: 'info_outline'
}

const R = {
  name: 'R',
  type: types.TASK_TYPE_R,
  icon: ''
}

const RENAME = {
  name: 'Rename',
  type: types.TASK_TYPE_RENAME,
  icon: 'edit'
}

const RENAME_COLUMN = {
  name: 'Rename Column',
  type: types.TASK_TYPE_RENAME_COLUMN,
  icon: 'edit'
}

const SEARCH = {
  name: 'Search',
  type: types.TASK_TYPE_SEARCH,
  icon: 'search'
}

const SELECT = {
  name: 'Select',
  type: types.TASK_TYPE_SELECT,
  icon: 'view_carousel'
}

const SORT = {
  name: 'Sort',
  type: types.TASK_TYPE_SORT,
  icon: 'sort_by_alpha'
}

const TRANSFORM = {
  name: 'Transform',
  type: types.TASK_TYPE_TRANSFORM,
  icon: 'transform'
}

/* exports */

export const TASK_INFO_CALC_FIELD    = CALC_FIELD
export const TASK_INFO_CONVERT       = CONVERT
export const TASK_INFO_COPY          = COPY
export const TASK_INFO_CUSTOM        = CUSTOM
export const TASK_INFO_DISTINCT      = DISTINCT
export const TASK_INFO_DUPLICATE     = DUPLICATE
export const TASK_INFO_EMAIL_SEND    = EMAIL_SEND
export const TASK_INFO_EXECUTE       = EXECUTE
export const TASK_INFO_FIND_REPLACE  = FIND_REPLACE
export const TASK_INFO_FILTER        = FILTER
export const TASK_INFO_GROUP         = GROUP
export const TASK_INFO_INPUT         = INPUT
export const TASK_INFO_LIMIT         = LIMIT
export const TASK_INFO_MERGE         = MERGE
export const TASK_INFO_NOP           = NOP
export const TASK_INFO_OUTPUT        = OUTPUT
export const TASK_INFO_PROMPT        = PROMPT
export const TASK_INFO_R             = R
export const TASK_INFO_RENAME        = RENAME
export const TASK_INFO_RENAME_COLUMN = RENAME_COLUMN
export const TASK_INFO_SEARCH        = SEARCH
export const TASK_INFO_SELECT        = SELECT
export const TASK_INFO_SORT          = SORT
export const TASK_INFO_TRANSFORM     = TRANSFORM
