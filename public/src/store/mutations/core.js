import * as types from '../mutation-types'
import getDefaultState from '../state'

export default {
  [types.CHANGE_ACTIVE_DOCUMENT]: (state, identifier) => {
    state.active_document_identifier = identifier
  },

  [types.CHANGE_ACTIVE_TEAM]: (state, name) => {
    state.active_team_name = name
  },

  [types.RESET_STATE]: (state) => {
    Object.assign(state, getDefaultState())
  }
}
