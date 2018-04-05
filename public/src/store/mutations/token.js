import * as types from '../mutation-types'
import { addUser, updateUser, addToken, removeObject } from './helpers'

export default {

  // ----------------------------------------------------------------------- //

  [types.FETCHING_TOKENS] (state, { fetching }) {
    state.tokens_fetching = fetching
  },

  [types.FETCHED_TOKENS] (state, { tokens }) {
    addToken(state, tokens)

    // set our fetched flag so we know we've queried the backend
    state.tokens_fetched = true
  },

  // ----------------------------------------------------------------------- //

  [types.CREATING_TOKEN] (state) {},

  [types.CREATED_TOKEN] (state, { attrs }) {
    addToken(state, attrs, { is_fetched: true })
  },

  [types.DELETING_TOKEN] (state, { eid }) {},

  [types.DELETED_TOKEN] (state, { eid }) {
    removeObject(state, eid)
  }

  // ----------------------------------------------------------------------- //

}
