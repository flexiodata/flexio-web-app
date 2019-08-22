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

  'initializeApp' ({ commit, dispatch, state, getters }, { team_name, force }) {
    var active_username = getters['users/getActiveUsername']
    var active_user_eid = state.users.active_user_eid
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
        commit('appconnections/RESET_STATE')
        commit('apppipes/RESET_STATE')

        // query these objects fresh
        axios.all([
          dispatch('teams/fetch', { team_name: active_username }),
          dispatch('members/fetch', { team_name }),
          dispatch('connections/fetch', { team_name }),
        ])
        .then(axios.spread((teams_response, members_response, connections_response) => {
          dispatch('members/fetchRights', { team_name, eid: active_user_eid, username: active_username }).finally(response => {
            commit('INITIALIZING_APP', false)
          })
        }))
        .catch(() => {
          commit('INITIALIZING_APP', false)
        })
    }
  }
}
