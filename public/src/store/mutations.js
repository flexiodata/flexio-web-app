import getDefaultState from './state'

export default {
  'CHANGE_ACTIVE_DOCUMENT' (state, identifier) {
    state.active_document_identifier = identifier
  },

  'RESET_STATE' (state) {
    Object.assign(state, getDefaultState())
  }
}
