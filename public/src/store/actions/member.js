import api from '../../api'
import * as types from '../mutation-types'

// ----------------------------------------------------------------------- //

export const v2_action_fetchMembers = ({ commit }, { team_name }) => {
  commit(types.FETCHING_MEMBERS, { fetching: true })

  return api.v2_fetchMembers(team_name).then(response => {
    var members = response.data
    commit(types.FETCHED_MEMBERS, { members })
    commit(types.FETCHING_MEMBERS, { fetching: false })
    return response
  }).catch(error => {
    commit(types.FETCHING_MEMBERS, { fetching: false })
    throw error
  })
}

// ----------------------------------------------------------------------- //

export const v2_action_createMember = ({ commit, dispatch }, { team_name, attrs }) => {
  commit(types.CREATING_MEMBER, { attrs })

  return api.v2_createMember(team_name, attrs).then(response => {
    var member = response.data
    commit(types.CREATED_MEMBER, { attrs, member })
    return response
  }).catch(error => {
    throw error
  })
}

export const v2_action_fetchMember = ({ commit }, { team_name, eid }) => {
  commit(types.FETCHING_MEMBER, { eid, fetching: true })

  return api.v2_fetchMember(team_name, eid).then(response => {
    var member = response.data
    commit(types.FETCHED_MEMBER, member)
    commit(types.FETCHING_MEMBER, { eid, fetching: false })
    return response
  }).catch(error => {
    commit(types.FETCHING_MEMBER, { eid, fetching: false })
    throw error
  })
}

export const v2_action_updateMember = ({ commit }, { team_name, eid, attrs }) => {
  commit(types.UPDATING_MEMBER, { eid, attrs })

  return api.v2_updateMember(team_name, eid, attrs).then(response => {
    var attrs = response.data
    commit(types.UPDATED_MEMBER, { eid, attrs })
    return response
  }).catch(error => {
    throw error
  })
}

export const v2_action_deleteMember = ({ commit }, { team_name, eid }) => {
  var attrs = { eid }
  commit(types.DELETING_MEMBER, { attrs })

  return api.v2_deleteMember(team_name, eid).then(response => {
    commit(types.DELETED_MEMBER, { attrs })
    return response
  }).catch(error => {
    throw error
  })
}
