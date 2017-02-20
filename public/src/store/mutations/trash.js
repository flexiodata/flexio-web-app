import * as types from '../mutation-types'
import { addProject, updateProject, addTrash } from './helpers'

export default {
  [types.FETCHING_TRASH] (state, { project_eid, fetching }) {
    // if we're trying to fetch trash for a project that's not
    // in our store, add a very basic project object to the store
    if (fetching === true && !_.has(state.objects, project_eid))
    {
      addProject(state, project_eid, { trash_fetching: fetching })
    }
     else
    {
      // otherwise, just set the project's pipe fetching flag
      updateProject(state, project_eid, { trash_fetching: fetching })
    }
  },

  [types.FETCHED_TRASH] (state, { project_eid, trash }) {
    addTrash(state, trash)

    // set the project's pipe fetched flag
    updateProject(state, project_eid, { trash_fetched: true })
  }
}
