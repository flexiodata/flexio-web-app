import _ from 'lodash'
import Flexio from 'flexio-sdk-js'

const state = {
  eid: '',
  edit_pipe: {},
  edit_code: '',
  syntax_error: '',
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
    state.edit_pipe = _.cloneDeep(pipe)
    state.edit_code = Flexio.pipe(_.get(pipe, 'task', {})).toCode()
    state.fetched = true
  },

  UPDATE_EDIT_PIPE (state, attrs) {
    state.edit_pipe = _.cloneDeep(attrs)
    state.edit_code = Flexio.pipe(_.get(attrs, 'task', {})).toCode()
  },

  UPDATE_CODE (state, code) {
    state.edit_code = code

    try {
      // create a function to create the JS SDK code to call
      var fn = (Flexio, callback) => { return eval(code) }

      // get access to pipe object
      var pipe = fn.call(this, Flexio)

      // check pipe syntax
      if (!Flexio.util.isPipeObject(pipe)) {
        throw({ message: 'Invalid pipe syntax. Pipes must start with `Flexio.pipe()`.' })
      }

      // get the pipe task JSON
      var task = _.get(pipe.getJSON(), 'task', { op: 'sequence', params: {} })

      state.edit_pipe = _.assign({}, state.edit_pipe, { task })
      state.syntax_error = ''
    }
    catch(e)
    {
      state.syntax_error = e.message
    }
  }
}

const actions = {}

const getters = {
  getOriginalPipe (state, getters, root_state) {
    return _.get(root_state, 'objects.' + state.eid, null)
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}

