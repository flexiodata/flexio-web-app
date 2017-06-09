import * as types from '../mutation-types'
import { updateUser, addRight, removeObject } from './helpers'

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

  [types.DELETING_RIGHT] (state, { eid, token_eid }) {},

  [types.DELETED_RIGHT] (state, { eid, token_eid }) {
    removeObject(state, token_eid)
  }

  // ----------------------------------------------------------------------- //

}
