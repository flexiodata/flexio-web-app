import * as types from '../mutation-types'
import {
  updateUser,
  addRight,
  updateRight,
  removeObject
} from './helpers'

export default {

  // ----------------------------------------------------------------------- //

  [types.FETCHING_RIGHTS] (state, { eid, fetching }) {
  },

  [types.FETCHED_RIGHTS] (state, { eid, rights }) {
    addRight(state, rights)
  },

  // ----------------------------------------------------------------------- //

  [types.CREATING_RIGHT] (state, { eid, attrs }) {},

  [types.CREATED_RIGHT] (state, { eid, attrs }) {
    addRight(state, attrs, { is_fetched: true })
  },

  [types.UPDATING_RIGHT] (state, { eid, attrs }) {},

  [types.UPDATED_RIGHT] (state, { eid, attrs }) {
    updateRight(state, eid, attrs)
  },

  [types.DELETING_RIGHT] (state, { attrs }) {},

  [types.DELETED_RIGHT] (state, { attrs }) {
    debugger
    removeObject(state, attrs)
  }

  // ----------------------------------------------------------------------- //

}
