import * as types from '../mutation-types'
import { addProject, updateProject, addMember, updateMember, removeObject } from './helpers'

export default {

  // ----------------------------------------------------------------------- //

  [types.FETCHING_MEMBERS] (state, { project_eid, fetching }) {
    // if we're trying to fetch members for a project that's not
    // in our store, add a very basic project object to the store
    if (fetching === true && !_.has(state.objects, project_eid))
    {
      addProject(state, project_eid, { members_fetching: fetching })
    }
     else
    {
      // otherwise, just set the project's member fetching flag
      updateProject(state, project_eid, { members_fetching: fetching })
    }
  },

  [types.FETCHED_MEMBERS] (state, { project_eid, members }) {
    addMember(state, members)

    // set the project's member fetched flag
    updateProject(state, project_eid, { members_fetched: true })
  },

  // ----------------------------------------------------------------------- //

  [types.CREATING_MEMBERS] (state, { project_eid, attrs }) {},

  [types.CREATED_MEMBERS] (state, { project_eid, attrs, members }) {
    addMember(state, members, { is_fetched: true })
  },

  [types.DELETING_MEMBER] (state, { project_eid, eid }) {},

  [types.DELETED_MEMBER] (state, { project_eid, eid }) {
    if (eid == state.active_user_eid)
    {
      // if the user that is being removed is the active user, this is a user
      // that is leaving a project, so remove the project from the store
      removeObject(state, project_eid)
    }
     else
    {
      // otherwise, remove the user from the store
      removeObject(state, eid)
    }
  }

  // ----------------------------------------------------------------------- //

}
