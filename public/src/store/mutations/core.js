import * as types from '../mutation-types'

export default {
  [types.CHANGE_ACTIVE_DOCUMENT]: (state, eid) => {
    state.active_document = eid
  },

  [types.CHANGE_ACTIVE_PROJECT]: (state, eid) => {
    state.active_project = eid
  }
}
