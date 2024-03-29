import * as ops from './task-op'

/* step info */

const EXECUTE = {
  op: ops.TASK_OP_EXECUTE,
  name: 'Execute',
  description: 'Build a spreadsheet function using a custom Python or Node.js script, either inline or remote.',
  icon: 'code',
  bg_color: 'bg-task-purple'
}

const EXTRACT = {
  op: ops.TASK_OP_EXTRACT,
  name: 'Extract',
  description: 'Build a spreadsheet function that extracts a data set from a table, file or database.',
  icon: 'launch',
  bg_color: 'bg-task-orange'
}

const LOOKUP = {
  op: ops.TASK_OP_LOOKUP,
  name: 'Lookup',
  description: 'Build a spreadsheet function that creates a lookup based on a key/value pair from a table, file or database.',
  icon: 'find_in_page',
  bg_color: 'bg-task-blue'
}

/* exports */

// go out of alphabetical order here so the order is correct in the step chooser builder item
export const TASK_INFO_EXTRACT       = EXTRACT
export const TASK_INFO_LOOKUP        = LOOKUP
export const TASK_INFO_EXECUTE       = EXECUTE

/*
-------------------- old cruft --------------------

const CALC_FIELD = {
  name: 'Calculation',
  description: '',
  op: ops.TASK_OP_CALC,
  icon: 'flash_on',
  bg_color: 'bg-task-green'
}

const COMMENT = {
  name: 'Comment',
  description: '',
  op: ops.TASK_OP_COMMENT,
  icon: 'comment',
  bg_color: 'bg-task-blue'
}

const CONNECT = {
  op: ops.TASK_OP_CONNECT,
  name: 'Connect',
  description: 'Create or add an external connection (OAuth, token, etc.) to call from your function.',
  icon: 'repeat',
  bg_color: 'bg-task-blue'
}

const CONVERT = {
  op: ops.TASK_OP_CONVERT,
  name: 'Convert',
  description: 'Convert the output of the previous step in your function to a different format (e.g., JSON to CSV)',
  icon: 'settings',
  bg_color: 'bg-task-blue'
}

const COPY = {
  op: ops.TASK_OP_COPY,
  name: 'Copy',
  description: 'Copy files or directories from one storage-type connection to a different storage-type connection.',
  icon: 'content_copy',
  bg_color: 'bg-task-blue'
}

const CUSTOM = {
  name: 'Custom',
  description: '',
  op: ops.TASK_OP_CUSTOM,
  icon: 'build',
  bg_color: 'bg-task-orange'
}

const DISTINCT = {
  name: 'Distinct',
  description: '',
  op: ops.TASK_OP_DISTINCT,
  icon: 'clear_all',
  bg_color: 'bg-task-green'
}

const DUPLICATE = {
  name: 'Duplicate',
  description: '',
  op: ops.TASK_OP_DUPLICATE,
  icon: 'content_copy',
  bg_color: 'bg-task-green'
}

const ECHO = {
  op: ops.TASK_OP_ECHO,
  name: 'Echo',
  description: 'Echo a message or variable to the next step in your function.',
  icon: 'settings_remote',
  bg_color: 'bg-task-blue'
}

const EMAIL_SEND = {
  op: ops.TASK_OP_EMAIL_SEND,
  name: 'Email',
  description: 'Email a notification, variable and/or attachments using Flex.io or your own SMTP email service.',
  icon: 'mail_outline',
  bg_color: 'bg-task-blue'
}

const EXTRACT = {
  op: ops.TASK_OP_EXTRACT,
  name: 'Extract',
  description: 'Extract data from a dataset.',
  icon: 'launch',
  bg_color: 'bg-task-orange'
}

const FIND_REPLACE = {
  name: 'Find/Replace',
  description: '',
  op: ops.TASK_OP_FIND_REPLACE,
  icon: 'find_replace',
  bg_color: 'bg-task-orange'
}

const FILTER = {
  name: 'Filter',
  description: '',
  op: ops.TASK_OP_FILTER,
  icon: 'filter_list',
  bg_color: 'bg-task-green'
}

const GROUP = {
  name: 'Group',
  description: '',
  op: ops.TASK_OP_GROUP,
  icon: 'functions',
  bg_color: 'bg-task-green'
}

const INPUT = {
  name: 'Input',
  description: '',
  op: ops.TASK_OP_INPUT,
  icon: 'input',
  bg_color: 'bg-task-blue'
}

const LIMIT = {
  name: 'Limit',
  description: '',
  op: ops.TASK_OP_LIMIT,
  icon: 'play_for_work',
  bg_color: 'bg-task-green'
}

const MERGE = {
  name: 'Merge',
  description: '',
  op: ops.TASK_OP_MERGE,
  icon: 'call_merge',
  bg_color: 'bg-task-green'
}

const NOP = {
  name: 'No Op.',
  description: '',
  op: ops.TASK_OP_NOP,
  icon: 'build',
  bg_color: 'bg-task-orange'
}

const OAUTH = {
  op: ops.TASK_OP_OAUTH,
  name: 'OAuth',
  description: 'Set up an OAuth-type connection and output an execute step that returns your OAuth token.',
  icon: 'security',
  bg_color: 'bg-task-purple'
}

const OUTPUT = {
  name: 'Output',
  description: '',
  op: ops.TASK_OP_OUTPUT,
  icon: 'input',
  bg_color: 'bg-task-blue'
}

const PROMPT = {
  name: 'Prompt',
  description: '',
  op: ops.TASK_OP_PROMPT,
  icon: 'info_outline',
  bg_color: 'bg-task-blue'
}

const R = {
  name: 'R',
  description: '',
  op: ops.TASK_OP_R,
  icon: '',
  bg_color: 'bg-task-purple'
}

const READ = {
  op: ops.TASK_OP_READ,
  name: 'Read',
  description: 'Read files from storage-type connections (e.g., Dropbox, MySQL, GitHub).',
  icon: 'input',
  bg_color: 'bg-task-blue'
}

const RENAME = {
  name: 'Rename',
  description: '',
  op: ops.TASK_OP_RENAME,
  icon: 'edit',
  bg_color: 'bg-task-orange'
}

const RENDER = {
  name: 'Render',
  description: '',
  op: ops.TASK_OP_RENDER,
  icon: 'photo',
  bg_color: 'bg-task-orange'
}

const REQUEST = {
  op: ops.TASK_OP_REQUEST,
  name: 'Request',
  description: 'Make an HTTP request; output will be sent to the next step of your function.',
  icon: 'http',
  bg_color: 'bg-task-blue'
}

const SEARCH = {
  name: 'Search',
  description: '',
  op: ops.TASK_OP_SEARCH,
  icon: 'search',
  bg_color: 'bg-task-green'
}

const SELECT = {
  name: 'Select',
  description: '',
  op: ops.TASK_OP_SELECT,
  icon: 'view_carousel',
  bg_color: 'bg-task-orange'
}

const SORT = {
  name: 'Sort',
  description: '',
  op: ops.TASK_OP_SORT,
  icon: 'sort_by_alpha',
  bg_color: 'bg-task-green'
}

const TRANSFORM = {
  name: 'Transform',
  description: '',
  op: ops.TASK_OP_TRANSFORM,
  icon: 'transform',
  bg_color: 'bg-task-orange'
}

const WRITE = {
  op: ops.TASK_OP_WRITE,
  name: 'Write',
  description: 'Write files to storage-type connections (e.g., Dropbox, MySQL, GitHub).',
  icon: 'input',
  bg_color: 'bg-task-blue'
}

export const TASK_INFO_CALC_FIELD    = CALC_FIELD
export const TASK_INFO_COMMENT       = COMMENT
export const TASK_INFO_CONNECT       = CONNECT
export const TASK_INFO_CONVERT       = CONVERT
export const TASK_INFO_COPY          = COPY
export const TASK_INFO_CUSTOM        = CUSTOM
export const TASK_INFO_DISTINCT      = DISTINCT
export const TASK_INFO_DUPLICATE     = DUPLICATE
export const TASK_INFO_ECHO          = ECHO
export const TASK_INFO_EMAIL_SEND    = EMAIL_SEND
export const TASK_INFO_FIND_REPLACE  = FIND_REPLACE
export const TASK_INFO_FILTER        = FILTER
export const TASK_INFO_GROUP         = GROUP
export const TASK_INFO_INPUT         = INPUT
export const TASK_INFO_LIMIT         = LIMIT
export const TASK_INFO_MERGE         = MERGE
export const TASK_INFO_NOP           = NOP
export const TASK_INFO_OAUTH         = OAUTH
export const TASK_INFO_OUTPUT        = OUTPUT
export const TASK_INFO_PROMPT        = PROMPT
export const TASK_INFO_R             = R
export const TASK_INFO_READ          = READ
export const TASK_INFO_RENAME        = RENAME
export const TASK_INFO_RENDER        = RENDER
export const TASK_INFO_REQUEST       = REQUEST
export const TASK_INFO_SEARCH        = SEARCH
export const TASK_INFO_SELECT        = SELECT
export const TASK_INFO_SORT          = SORT
export const TASK_INFO_TRANSFORM     = TRANSFORM
export const TASK_INFO_WRITE         = WRITE
*/
