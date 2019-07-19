import * as types from '../mutation-types'
import {
  addTeam,
  updateTeam,
  removeObject,
  removeObjectKeys
} from './helpers'

export default {

  // ----------------------------------------------------------------------- //

  [types.FETCHING_TEAMS] (state, { fetching }) {
    state.teams_fetching = fetching
  },

  [types.FETCHED_TEAMS] (state, { teams }) {
    addTeam(state, teams)

    // set our fetched flag so we know we've queried the backend
    state.teams_fetched = true
  },

  // ----------------------------------------------------------------------- //

  [types.CREATING_TEAM] (state, { attrs }) {},

  [types.CREATED_TEAM] (state, { attrs, team }) {
    addTeam(state, team, { is_fetched: true })
  },

  [types.FETCHING_TEAM] (state, { eid, fetching }) {
    // if we're trying to fetch a team that's not
    // in our store, add a very basic team object to the store
    if (fetching === true && !_.has(state.objects, eid)) {
      addTeam(state, eid, { is_fetching: fetching })
    } else {
      // otherwise, just set the team's fetching flag
      updateTeam(state, eid, { is_fetching: fetching })
    }
  },

  [types.FETCHED_TEAM] (state, team) {
    // if the team in the store has an error node, remove it; the new fetch will
    // put it back if there are still errors
    removeObjectKeys(state, team, ['error'])

    addTeam(state, team, { is_fetched: true })
  },

  [types.UPDATING_TEAM] (state, { eid, attrs }) {},

  [types.UPDATED_TEAM] (state, { eid, attrs }) {
    updateTeam(state, eid, attrs)
  },

  [types.DELETING_TEAM] (state, { attrs }) {},

  [types.DELETED_TEAM] (state, { attrs }) {
    removeObject(state, attrs)
  }

  // ----------------------------------------------------------------------- //

}
