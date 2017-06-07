import * as types from '../mutation-types'

export default {
  [types.CHANGE_ACTIVE_DOCUMENT]: (state, eid) => {
    state.active_document_eid = eid
  }
}
