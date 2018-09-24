import * as types from '../mutation-types'
import getDefaultState from '../state'

export default {
  [types.CHANGE_ACTIVE_DOCUMENT]: (state, eid) => {
    state.active_document_eid = eid
  },

  [types.RESET_STATE]: (state) => {
    Object.assign(state, getDefaultState())
  }
}
