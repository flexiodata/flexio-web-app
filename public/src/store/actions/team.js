import api from '../../api'
import * as types from '../mutation-types'

// ----------------------------------------------------------------------- //

export const v2_action_fetchTeams = ({ commit }, { team_name }) => {
  commit(types.FETCHING_TEAMS, { fetching: true })

  return api.v2_fetchTeams(team_name).then(response => {
    var teams = response.data
    commit(types.FETCHED_TEAMS, { teams })
    commit(types.FETCHING_TEAMS, { fetching: false })
    return response
  }).catch(error => {
    commit(types.FETCHING_TEAMS, { fetching: false })
    throw error
  })
}

// ----------------------------------------------------------------------- //

export const v2_action_createTeam = ({ commit, dispatch }, { team_name, attrs }) => {
  commit(types.CREATING_TEAM, { attrs })

  return api.v2_createTeam(team_name, attrs).then(response => {
    var team = response.data
    commit(types.CREATED_TEAM, { attrs, team })
    return response
  }).catch(error => {
    throw error
  })
}

export const v2_action_fetchTeam = ({ commit }, { team_name, eid }) => {
  commit(types.FETCHING_TEAM, { eid, fetching: true })

  return api.v2_fetchTeam(team_name, eid).then(response => {
    var team = response.data
    commit(types.FETCHED_TEAM, team)
    commit(types.FETCHING_TEAM, { eid, fetching: false })
    return response
  }).catch(error => {
    commit(types.FETCHING_TEAM, { eid, fetching: false })
    throw error
  })
}

export const v2_action_updateTeam = ({ commit }, { team_name, eid, attrs }) => {
  commit(types.UPDATING_TEAM, { eid, attrs })

  return api.v2_updateTeam(team_name, eid, attrs).then(response => {
    var attrs = response.data
    commit(types.UPDATED_TEAM, { eid, attrs })
    return response
  }).catch(error => {
    throw error
  })
}

export const v2_action_deleteTeam = ({ commit }, { team_name, eid }) => {
  var attrs = { eid }
  commit(types.DELETING_TEAM, { attrs })

  return api.v2_deleteTeam(team_name, eid).then(response => {
    commit(types.DELETED_TEAM, { attrs })
    return response
  }).catch(error => {
    throw error
  })
}
