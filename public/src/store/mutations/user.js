import * as types from '../mutation-types'
import { addUser, updateUser, resetState } from './helpers'

export default {

  // ----------------------------------------------------------------------- //

  [types.CREATING_USER] (state, { attrs }) {},

  [types.CREATED_USER] (state, { attrs, user }) {
    addUser(state, user, { is_fetched: true })
  },

  [types.FETCHING_USER]: (state, fetching) => {
    state.user_fetching = fetching
  },

  [types.FETCHED_USER]: (state, user) => {
    addUser(state, user, { is_fetched: true })

    // set our fetched flag so we know we've queried the backend for the active user
    state.user_fetched = true

    // store the active user eid
    state.active_user_eid = user.eid
  },

  [types.UPDATING_USER] (state, { eid }) {},

  [types.UPDATED_USER] (state, { eid, attrs }) {
    updateUser(state, eid, attrs)
  },

  [types.DELETING_USER] (state, { eid, attrs }) {},

  [types.DELETED_USER] (state, { eid, attrs }) {
    resetState(state)
  },

  // ----------------------------------------------------------------------- //

  [types.CHANGING_PASSWORD]: (state) => {},

  [types.CHANGED_PASSWORD]: (state) => {},

  [types.SIGNING_IN]: (state) => {},

  [types.SIGNED_IN]: (state, user) => {
    addUser(state, user, { is_fetched: true })

    // set our fetched flag so we know we've queried the backend for the active user
    state.user_fetched = true

    // store the active user eid
    state.active_user_eid = user.eid
  },

  [types.SIGNING_OUT]: (state) => {},

  [types.SIGNED_OUT]: (state) => {
    resetState(state)
  },

  [types.SIGNING_UP]: (state) => {},

  [types.SIGNED_UP]: (state) => {}

  // ----------------------------------------------------------------------- //

}
