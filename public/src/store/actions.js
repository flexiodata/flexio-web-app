export default {
  'resetState' ({ commit }) {
    commit('RESET_STATE')
    commit('connections/RESET_STATE')
    commit('members/RESET_STATE')
    commit('pipedocument/RESET_STATE')
    commit('pipes/RESET_STATE')
    commit('processes/RESET_STATE')
    commit('streams/RESET_STATE')
    commit('teams/RESET_STATE')
    commit('tokens/RESET_STATE')
    commit('users/RESET_STATE')
  },
}
