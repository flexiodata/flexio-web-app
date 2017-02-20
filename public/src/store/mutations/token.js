import * as types from '../mutation-types'
import { addUser, updateUser, addToken, removeObject } from './helpers'

export default {

  // ----------------------------------------------------------------------- //

  [types.FETCHING_TOKENS] (state, { eid, fetching }) {
    // if we're trying to fetch tokens for a user that's not
    // in our store, add a very basic user object to the store
    if (fetching === true && !_.has(state.objects, eid))
    {
      addUser(state, eid, { tokens_fetching: fetching })
    }
     else
    {
      // otherwise, just set the user's token fetching flag
      updateUser(state, eid, { tokens_fetching: fetching })
    }
  },

  [types.FETCHED_TOKENS] (state, { eid, tokens }) {
    addToken(state, tokens)

    // set the user's token fetched flag
    updateUser(state, eid, { tokens_fetched: true })
  },

  // ----------------------------------------------------------------------- //

  [types.CREATING_TOKEN] (state, { eid, attrs }) {},

  [types.CREATED_TOKEN] (state, { eid, attrs }) {
    addToken(state, attrs, { is_fetched: true })
  },

  [types.DELETING_TOKEN] (state, { eid, token_eid }) {},

  [types.DELETED_TOKEN] (state, { eid, token_eid }) {
    removeObject(state, token_eid)
  }

  // ----------------------------------------------------------------------- //

}
