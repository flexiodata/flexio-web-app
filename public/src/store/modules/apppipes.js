import _ from 'lodash'

const getDefaultState = () => {
  return {
    pipes: [],
    fetching: false,
    fetched: false
  }
}

const state = getDefaultState()

const mutations = {
  RESET_STATE (state) {
    _.assign(state, getDefaultState())
  },

  FETCHING_PIPES (state, fetching) {
    state.fetching = fetching

    if (fetching === true) {
      state.fetched = false
    }
  },

  FETCHED_PIPES (state, fetched) {
    state.fetched = fetched
  },

  INIT_PIPES (state, pipes) {
    state.pipes = _.map(pipes, p => {
      return _.assign({}, p, { is_selected: false })
    })
  }
}

const actions = {}

const getters = {}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}

