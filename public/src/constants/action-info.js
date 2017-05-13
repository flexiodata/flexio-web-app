import * as types from './action-type'

/* action info */

const READ = {
  name: 'Read',
  type: types.ACTION_TYPE_READ
}

const WRITE = {
  name: 'Write',
  type: types.ACTION_TYPE_WRITE
}

const EXECUTE = {
  name: 'Execute',
  type: types.ACTION_TYPE_EXECUTE
}

const DELETE = {
  name: 'Delete',
  type: types.ACTION_TYPE_DELETE
}

/* exports */

export const ACTION_INFO_READ = READ
export const ACTION_INFO_WRITE = WRITE
export const ACTION_INFO_EXECUTE = EXECUTE
export const ACTION_INFO_DELETE = DELETE
