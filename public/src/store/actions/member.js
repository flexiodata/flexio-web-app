import api from '../../api'
import * as types from '../mutation-types'

// ----------------------------------------------------------------------- //

export const fetchMembers = ({ commit }, project_eid) => {
  commit(types.FETCHING_MEMBERS, { project_eid, fetching: true })

  return api.fetchProjectMembers({ eid: project_eid }).then(response => {
    // success callback

    // make sure we know what projects this user is a member of (we have to
    // do this since we don't want to return this information in the payload
    // due to security concerns)
    var members = response.body
    _.map(members, function(m) {
      return _.extend(m, { following: [project_eid] })
    })

    commit(types.FETCHED_MEMBERS, { project_eid, members: members })
    commit(types.FETCHING_MEMBERS, { project_eid, fetching: false })
    return response
  }, response => {
    // error callback
    commit(types.FETCHING_MEMBERS, { project_eid, fetching: false })
    return response
  })
}

// ----------------------------------------------------------------------- //

export const createMembers = ({ commit }, { project_eid, attrs }) => {
  commit(types.CREATING_MEMBERS, { project_eid, attrs })

  return api.createProjectMembers({ eid: project_eid, attrs }).then(response => {
    // make sure we know what projects this user is a member of (we have to
    // do this since we don't want to return this information in the payload
    // due to security concerns)
    var members = response.body
    _.map(members, function(m) {
      return _.extend(m, { following: [project_eid] })
    })

    // success callback
    commit(types.CREATED_MEMBERS, { project_eid, attrs, members: members })

    return response
  }, response => {
    // error callback
    return response
  })
}

export const deleteMember = ({ commit }, { project_eid, eid }) => {
  commit(types.DELETING_MEMBER, { project_eid, eid })

  return api.deleteProjectMember({ eid: project_eid, member_eid: eid }).then(response => {
    // success callback
    commit(types.DELETED_MEMBER, { project_eid, eid })
    return response
  }, response => {
    // error callback
    return response
  })
}
