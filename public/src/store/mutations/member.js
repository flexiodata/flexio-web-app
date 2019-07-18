import * as types from '../mutation-types'
import {
  addMember,
  updateMember,
  removeObject,
  removeObjectKeys
} from './helpers'

export default {

  // ----------------------------------------------------------------------- //

  [types.FETCHING_MEMBERS] (state, { fetching }) {
    state.members_fetching = fetching
  },

  [types.FETCHED_MEMBERS] (state, { members }) {
    addMember(state, members)

    // set our fetched flag so we know we've queried the backend
    state.members_fetched = true
  },

  // ----------------------------------------------------------------------- //

  [types.CREATING_MEMBER] (state, { attrs }) {},

  [types.CREATED_MEMBER] (state, { attrs, member }) {
    addMember(state, member, { is_fetched: true })
  },

  [types.FETCHING_MEMBER] (state, { eid, fetching }) {
    // if we're trying to fetch a member that's not
    // in our store, add a very basic member object to the store
    if (fetching === true && !_.has(state.objects, eid)) {
      addMember(state, eid, { is_fetching: fetching })
    } else {
      // otherwise, just set the member's fetching flag
      updateMember(state, eid, { is_fetching: fetching })
    }
  },

  [types.FETCHED_MEMBER] (state, member) {
    // if the member in the store has an error node, remove it; the new fetch will
    // put it back if there are still errors
    removeObjectKeys(state, member, ['error'])

    addMember(state, member, { is_fetched: true })
  },

  [types.UPDATING_MEMBER] (state, { eid, attrs }) {},

  [types.UPDATED_MEMBER] (state, { eid, attrs }) {
    updateMember(state, eid, attrs)
  },

  [types.DELETING_MEMBER] (state, { attrs }) {},

  [types.DELETED_MEMBER] (state, { attrs }) {
    removeObject(state, attrs)
  }

  // ----------------------------------------------------------------------- //

}
