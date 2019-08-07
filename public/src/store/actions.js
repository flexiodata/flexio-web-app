import axios from 'axios'

const files = require.context('./modules', false, /\.js$/)
const modules_names = []

files.keys().forEach(key => {
  if (key === './index.js') return
  modules_names.push(key.replace(/(\.\/|\.js)/g, ''))
})

export default {
  'resetState' ({ commit }) {
    commit('RESET_STATE')
    _.each(modules_names, module => { commit(`${module}/RESET_STATE`) })
  },

  'initializeTeam' ({ commit, dispatch, state, getters }, { team_name, force }) {
    var active_user_name = getters['users/getActiveUsername']
    var old_team_name = state.teams.active_team_name

    if (team_name != old_team_name || force === true) {
        commit('INITIALIZING_APP', true)

        // clear out these modules so we can start over
        commit('teams/RESET_STATE')
        commit('members/RESET_STATE')
        commit('connections/RESET_STATE')
        commit('pipes/RESET_STATE')
        commit('processes/RESET_STATE')
        commit('streams/RESET_STATE')
        commit('pipedocument/RESET_STATE')

        // query these objects fresh
        axios.all([
          dispatch('teams/fetch', { team_name: active_user_name }),
          dispatch('members/fetch', { team_name }),
          dispatch('connections/fetch', { team_name }),
        ])
        .finally(axios.spread(() => {
          commit('INITIALIZING_APP', false)
        }))
    }
  }
}
