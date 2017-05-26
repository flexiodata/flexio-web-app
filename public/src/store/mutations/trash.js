import * as types from '../mutation-types'
import {
  addProject,
  updateProject,
  addTrash
} from './helpers'

export default {

  // ----------------------------------------------------------------------- //

  [types.FETCHING_TRASH] (state, { project_eid, fetching }) {
    state.trash_fetching = fetching
  },

  [types.FETCHED_TRASH] (state, { project_eid, trash }) {
    addTrash(state, trash)

    // set our fetched flag so we know we've queried the backend
    state.trash_fetched = true
  }

  // ----------------------------------------------------------------------- //

}
