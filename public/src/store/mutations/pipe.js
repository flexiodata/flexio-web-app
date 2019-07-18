import * as types from '../mutation-types'
import {
  addPipe,
  updatePipe,
  removeObject,
  removeObjectKeys
} from './helpers'

export default {

  // ----------------------------------------------------------------------- //

  [types.FETCHING_PIPES] (state, { fetching }) {
    state.pipes_fetching = fetching
  },

  [types.FETCHED_PIPES] (state, { pipes }) {
    addPipe(state, pipes)

    // set our fetched flag so we know we've queried the backend
    state.pipes_fetched = true
  },

  // ----------------------------------------------------------------------- //

  [types.CREATING_PIPE] (state, { attrs }) {},

  [types.CREATED_PIPE] (state, { attrs, pipe }) {
    addPipe(state, pipe, { is_fetched: true })
  },

  [types.FETCHING_PIPE] (state, { eid, fetching }) {
    // if we're trying to fetch a pipe that's not
    // in our store, add a very basic pipe object to the store
    if (fetching === true && !_.has(state.objects, eid)) {
      addPipe(state, eid, { is_fetching: fetching })
    } else {
      // otherwise, just set the pipe's fetching flag
      updatePipe(state, eid, { is_fetching: fetching })
    }
  },

  [types.FETCHED_PIPE] (state, pipe) {
    // if the pipe in the store has an error node, remove it; the new fetch will
    // put it back if there are still errors
    removeObjectKeys(state, pipe, ['error'])

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
