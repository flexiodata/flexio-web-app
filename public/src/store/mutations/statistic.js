import * as types from '../mutation-types'

export default {

  // ----------------------------------------------------------------------- //

  [types.FETCHING_STATISTICS] (state, { type, fetching }) {
    state.statistics_fetching = _.assign({}, state.statistics_fetching, {
      [type]: fetching
    })
  },

  [types.FETCHED_STATISTICS] (state, { type, statistics }) {
    state['statistics'] = _.assign({}, state['statistics'], {
      [type]: [].concat(statistics)
    })

    // set our fetched flag so we know we've queried the backend
    state.statistics_fetched = _.assign({}, state.statistics_fetched, {
      [type]: true
    })
  }

  // ----------------------------------------------------------------------- //

}
