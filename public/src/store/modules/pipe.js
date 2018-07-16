import _ from 'lodash'
import Flexio from 'flexio-sdk-js'
import utilSdkJs from '../../utils/sdk-js'

const state = {
  eid: '',
  orig_pipe: {},
  orig_code: '',
  edit_pipe: {},
  edit_code: '',
  syntax_error: '',
  edit_keys: ['eid', 'name', 'alias', 'description', 'pipe_mode', 'schedule', 'schedule_status', 'task'],
  fetching: false,
  fetched: false
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
    var task = _.get(pipe, 'task', {})

    state.orig_pipe = pipe
    state.edit_pipe = pipe

    try {
      state.orig_code = Flexio.pipe(task).toCode()
      state.edit_code = Flexio.pipe(task).toCode()
    }
    catch (e) {
      var msg = 'Flexio.pipe().echo("There was an error parsing the pipe JSON.")'
      state.orig_code = msg
      state.edit_code = msg
    }

    state.syntax_error = ''
    state.fetched = true
  },

  UPDATE_EDIT_PIPE (state, pipe) {
    var pipe = _.pick(pipe, state.edit_keys)
    var task = _.get(pipe, 'task', {})

    state.edit_pipe = _.assign({}, state.edit_pipe, pipe)
    state.edit_code = Flexio.pipe(task).toCode()
  },

  // task: { index, attrs }
  UPDATE_EDIT_TASK (state, task) {
    var pipe = _.pick(pipe, state.edit_keys)
    _.set(pipe, 'task.items['+ task.index + ']', task.attrs)

    state.edit_pipe = _.assign({}, state.edit_pipe, pipe)
    state.edit_code = Flexio.pipe(_.get(pipe, 'task', {})).toCode()
  }
}

const actions = {}

const getters = {
  getStorePipe (state, getters, root_state) {
    return _.get(root_state, 'objects.' + state.eid, null)
  },
  isCodeChanged (state, getters, root_state) {
    return state.edit_code != state.orig_code
  },
  isChanged (state, getters, root_state) {
    var pipe1 = _.omit(state.edit_pipe, ['task'])
    var pipe2 = _.omit(state.orig_pipe, ['task'])
    return state.edit_code != state.orig_code || !_.isEqual(pipe1, pipe2)
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}

