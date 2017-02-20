import * as types from '../mutation-types'
import { addStream, updateStream } from './helpers'

export default {
  [types.FETCHING_STREAM] (state, { eid, fetching }) {
    // if we're trying to fetch a stream that's not
    // in our store, add a very basic stream object to the store
    if (fetching === true && !_.has(state.streams, eid))
    {
      addStream(state, eid, { is_fetching: fetching })
    }
     else
    {
      // otherwise, just set the stream's fetching flag
      updateStream(state, eid, { is_fetching: fetching })
    }
  },

  [types.FETCHED_STREAM] (state, stream) {
    addStream(state, stream, { is_fetched: true })
  }
}
