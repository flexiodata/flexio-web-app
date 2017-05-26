import * as types from '../mutation-types'
import {
  addProject,
  updateProject,
  addTrash,
  removeObject
} from './helpers'

export default {

  // ----------------------------------------------------------------------- //

  [types.FETCHING_TRASH] (state, { fetching }) {
    state.trash_fetching = fetching
  },

  [types.FETCHED_TRASH] (state, { trash }) {
    addTrash(state, trash)

    // set our fetched flag so we know we've queried the backend
    state.trash_fetched = true
  },

  // ----------------------------------------------------------------------- //

  [types.BULK_DELETING_ITEMS] (state, { attrs }) {},

  [types.BULK_DELETED_ITEMS] (state, { attrs }) {
    removeObject(state, _.get(attrs, 'items'))
  }
}
