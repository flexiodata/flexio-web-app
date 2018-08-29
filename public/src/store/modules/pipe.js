import _ from 'lodash'
import Flexio from 'flexio-sdk-js'
import utilSdkJs from '../../utils/sdk-js'

const state = {
  eid: '',
  orig_pipe: {},
  edit_pipe: {},
  syntax_error: '',
  edit_keys: ['eid', 'name', 'alias', 'description', 'pipe_mode', 'schedule', 'schedule_status', 'task', 'ui'],
  fetching: false,
  fetched: false,
  changed: false
}

const mutations = {
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

