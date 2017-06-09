import * as types from './action-type'

/* action info */

const READ = {
  name: 'Can View',
  type: types.ACTION_TYPE_READ
}

const WRITE = {
  name: 'Can Edit',
  type: types.ACTION_TYPE_WRITE
}

const EXECUTE = {
  name: 'Can Run',
  type: types.ACTION_TYPE_EXECUTE
}

const DELETE = {
  name: 'Can Delete',
  type: types.ACTION_TYPE_DELETE
}

/* exports */

export const ACTION_INFO_READ = READ
export const ACTION_INFO_WRITE = WRITE
export const ACTION_INFO_EXECUTE = EXECUTE
export const ACTION_INFO_DELETE = DELETE
