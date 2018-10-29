import _ from 'lodash'

const getDefaultState = () => {
  return {
    eid: '',
    orig_pipe: {},
    edit_pipe: {},
    syntax_error: '',
    edit_keys: [
      'eid',
      'name',
      'alias',
      'description',
      'schedule',
      'deploy_mode',
      'deploy_schedule',
      'deploy_email',
      'deploy_api',
      'deploy_ui',
      'task',
      'ui'
    ],
    fetching: false,
    fetched: false,
    changed: false
  }
}

const state = getDefaultState()

const mutations = {
  RESET_STATE (state) {
    _.assign(state, getDefaultState())
  },

  FETCHING_PIPE (state, fetching) {
    state.fetching = fetching

    if (fetching === true) {
      state.fetched = false
    }
  },

  INIT_PIPE (state, pipe) {
    state.eid = _.get(pipe, 'eid', '')

    var pipe = _.pick(pipe, state.edit_keys)
    state.orig_pipe = pipe
    state.edit_pipe = pipe

    state.syntax_error = ''
    state.fetched = true
    state.changed = false
  },

  UPDATE_EDIT_PIPE (state, pipe) {
    var pipe = _.pick(pipe, state.edit_keys)
    state.edit_pipe = _.assign({}, state.edit_pipe, pipe)
    state.changed = true
  }
}

const actions = {}

const getters = {
  getStorePipe (state, getters, root_state) {
    return _.get(root_state, 'objects.' + state.eid, null)
  },
  isChanged (state, getters, root_state) {
    return state.changed
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}

