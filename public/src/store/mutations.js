import getDefaultState from './state'

export default {
  'CHANGE_ACTIVE_DOCUMENT' (state, identifier) {
    state.active_document_identifier = identifier
  },

  'INITIALIZING_APP' (state, is_initializing) {
    state.is_initializing = is_initializing
    if (is_initializing === true) {
      state.is_initialized = false
    }
  },

  'RESET_STATE' (state) {
    Object.assign(state, getDefaultState())
  }
}
