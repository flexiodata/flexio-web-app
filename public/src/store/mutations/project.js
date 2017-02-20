import * as types from '../mutation-types'
import { addProject, updateProject, removeObject } from './helpers'

export default {

  // ----------------------------------------------------------------------- //

  [types.FETCHING_PROJECTS] (state, fetching) {
    state.projects_fetching = fetching
  },

  [types.FETCHED_PROJECTS] (state, projects) {
    addProject(state, projects)

    // set our fetched flag so we know we've queried the backend for projects
    state.projects_fetched = true
  },

  // ----------------------------------------------------------------------- //

  [types.CREATING_PROJECT] (state, { attrs }) {},

  [types.CREATED_PROJECT] (state, { attrs, project }) {
    addProject(state, project, { is_fetched: true })
  },

  [types.FETCHING_PROJECT] (state, { eid, fetching }) {
    // if we're trying to fetch a project that's not
    // in our store, add a very basic project object to the store
    if (fetching === true && !_.has(state.objects, eid))
    {
      addProject(state, eid, { is_fetching: fetching })
    }
     else
    {
      // otherwise, just set the project's fetching flag
      updateProject(state, eid, { is_fetching: fetching })
    }
  },

  [types.FETCHED_PROJECT] (state, project) {
    addProject(state, project, { is_fetched: true })
  },

  [types.UPDATING_PROJECT] (state, { eid }) {},

  [types.UPDATED_PROJECT] (state, { eid, attrs }) {
    updateProject(state, eid, attrs)
  },

  [types.DELETING_PROJECT] (state, { attrs }) {},

  [types.DELETED_PROJECT] (state, { attrs }) {
    removeObject(state, attrs)
  },

  // ----------------------------------------------------------------------- //

  [types.BULK_DELETING_PROJECT_ITEMS] (state, { project_eid, attrs }) {},

  [types.BULK_DELETED_PROJECT_ITEMS] (state, { project_eid, attrs }) {
    removeObject(state, _.get(attrs, 'items'))
  }
}
