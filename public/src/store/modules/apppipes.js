const getDefaultState = () => {
  return {
    eid: '',
    orig_pipe: {},
    edit_pipe: {},
    syntax_error: '',
    edit_keys: [
      'eid',
      'name',
      'title',
      'syntax',
      'description',
      'schedule',
      'deploy_mode',
      'deploy_schedule',
      'deploy_email',
      'deploy_api',
      'deploy_ui',
      'task'
    ],
    is_fetching: false,
    is_fetched: false,
    is_changed: false
  }
}

const state = getDefaultState()

const mutations = {
  'RESET_STATE' (state) {
    _.assign(state, getDefaultState())
  },

  'FETCHING_PIPE' (state, is_fetching) {
    state.is_fetching = is_fetching

    if (is_fetching === true) {
      state.is_fetched = false
    }
  },

  'INIT_PIPE' (state, pipe) {
    state.eid = _.get(pipe, 'eid', '')

    var pipe = _.pick(pipe, state.edit_keys)
    state.orig_pipe = pipe
    state.edit_pipe = pipe

    state.syntax_error = ''
    state.is_fetched = true
    state.is_changed = false
  },

  'UPDATE_EDIT_PIPE' (state, pipe) {
    var pipe = _.pick(pipe, state.edit_keys)
    state.edit_pipe = _.assign({}, state.edit_pipe, pipe)
    state.is_changed = true
  }
}

const actions = {}

const getters = {}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
}

