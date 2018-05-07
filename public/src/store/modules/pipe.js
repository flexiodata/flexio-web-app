import _ from 'lodash'
import api from '../../api'

const state = {
  eid: '',
  edit_pipe: {},
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
    state.fetched = true
  },
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

