import * as types from '../mutation-types'
import { addProject, updateProject, addPipe, updatePipe, removeObject } from './helpers'

export default {

  // ----------------------------------------------------------------------- //

  [types.FETCHING_PIPES] (state, { project_eid, fetching }) {
    // if we're trying to fetch pipes for a project that's not
    // in our store, add a very basic project object to the store
    if (fetching === true && !_.has(state.objects, project_eid))
    {
      addProject(state, project_eid, { pipes_fetching: fetching })
    }
     else
    {
      // otherwise, just set the project's pipe fetching flag
      updateProject(state, project_eid, { pipes_fetching: fetching })
    }
  },

  [types.FETCHED_PIPES] (state, { project_eid, pipes }) {
    addPipe(state, pipes)

    // set the project's pipe fetched flag
    updateProject(state, project_eid, { pipes_fetched: true })
  },

  // ----------------------------------------------------------------------- //

  [types.CREATING_PIPE] (state, { attrs }) {},

  [types.CREATED_PIPE] (state, { attrs, pipe }) {
    addPipe(state, pipe, { is_fetched: true })
  },

  [types.FETCHING_PIPE] (state, { eid, fetching }) {
    // if we're trying to fetch a pipe that's not
    // in our store, add a very basic pipe object to the store
    if (fetching === true && !_.has(state.objects, eid))
    {
      addPipe(state, eid, { is_fetching: fetching })
    }
     else
    {
      // otherwise, just set the pipe's fetching flag
      updatePipe(state, eid, { is_fetching: fetching })
    }
  },

  [types.FETCHED_PIPE] (state, pipe) {
    addPipe(state, pipe, { is_fetched: true })
  },

  [types.UPDATING_PIPE] (state, { eid, attrs }) {},

  [types.UPDATED_PIPE] (state, { eid, attrs }) {
    updatePipe(state, eid, attrs)
  },

  [types.DELETING_PIPE] (state, { attrs }) {},

  [types.DELETED_PIPE] (state, { attrs }) {
    removeObject(state, attrs)
  }

  // ----------------------------------------------------------------------- //

}
