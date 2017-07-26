import * as types from '../mutation-types'

export default {

  // ----------------------------------------------------------------------- //

  [types.FETCHING_STATISTICS] (state, { type, fetching }) {},

  [types.FETCHED_STATISTICS] (state, { type, statistics }) {
    state['statistics'][type] = statistics
  }

  // ----------------------------------------------------------------------- //

}
