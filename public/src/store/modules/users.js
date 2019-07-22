const getDefaultState = () => {
  return {
    fetching: false,
    fetched: false
  }
}

const state = getDefaultState()

const mutations = {
  RESET_STATE (state) {
    Object.assign(state, getDefaultState())
  },

  FETCHING (state, fetching) {
    state.fetching = fetching

    if (fetching === true) {
      state.fetched = false
    }
  },
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
