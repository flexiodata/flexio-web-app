import api from '../../api'
import * as types from '../mutation-types'

// ----------------------------------------------------------------------- //

export const v2_action_fetchMembers = ({ commit }, { user_eid }) => {
  commit(types.FETCHING_MEMBERS, { fetching: true })

  return api.v2_fetchMembers(user_eid).then(response => {
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

export const v2_action_createMember = ({ commit, dispatch }, { user_eid, attrs }) => {
  commit(types.CREATING_MEMBER, { attrs })

  return api.v2_createMember(user_eid, attrs).then(response => {
    var member = response.data
    commit(types.CREATED_MEMBER, { attrs, member })
    return response
  }).catch(error => {
    throw error
  })
}

export const v2_action_fetchMember = ({ commit }, { user_eid, eid }) => {
  commit(types.FETCHING_MEMBER, { eid, fetching: true })

  return api.v2_fetchMember(user_eid, eid).then(response => {
    var member = response.data
    commit(types.FETCHED_MEMBER, member)
    commit(types.FETCHING_MEMBER, { eid, fetching: false })
    return response
  }).catch(error => {
    commit(types.FETCHING_MEMBER, { eid, fetching: false })
    throw error
  })
}

export const v2_action_updateMember = ({ commit }, { user_eid, eid, attrs }) => {
  commit(types.UPDATING_MEMBER, { eid, attrs })

  return api.v2_updateMember(user_eid, eid, attrs).then(response => {
    var attrs = response.data
    commit(types.UPDATED_MEMBER, { eid, attrs })
    return response
  }).catch(error => {
    throw error
  })
}

export const v2_action_deleteMember = ({ commit }, { user_eid, eid }) => {
  var attrs = { eid }
  commit(types.DELETING_MEMBER, { attrs })

  return api.v2_deleteMember(user_eid, eid).then(response => {
    commit(types.DELETED_MEMBER, { attrs })
    return response
  }).catch(error => {
    throw error
  })
}
