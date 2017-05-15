import * as types from './member-type'

/* member info */

const OWNER = {
  name: 'Owner',
  type: types.MEMBER_TYPE_OWNER
}

const MEMBER = {
  name: 'Members',
  type: types.MEMBER_TYPE_MEMBER
}

const PUBLIC = {
  name: 'Everyone',
  type: types.MEMBER_TYPE_PUBLIC
}

/* exports */

export const MEMBER_INFO_OWNER  = OWNER
export const MEMBER_INFO_MEMBER = MEMBER
export const MEMBER_INFO_PUBLIC = PUBLIC
