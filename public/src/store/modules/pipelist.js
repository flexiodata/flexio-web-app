import api from '../../api'
import { GET_PROJECT_PIPES, HAS_PROJECT_PIPES } from '../action-types'
import { PIPES_FETCHING, PIPES_RECEIVED } from '../mutation-types'

const state = {
  is_fetching: false,
  items: []
}

const mutations = {
  [PIPES_FETCHING] (state, is_fetching) {
    state.is_fetching = is_fetching
  },
  [PIPES_RECEIVED] (state, items) {
    state.items = items
  }
}

const actions = {
  // use argument destructuring below instead of passing 'context'
  // as the argument and then calling 'context.commit', etc.
  [GET_PROJECT_PIPES]: ({ commit, dispatch, rootState }) => {
    commit(PIPES_FETCHING, true)

    api.fetchProjectPipes(rootState.active_project).then(response => {
      commit(PIPES_RECEIVED, response.body)
      commit(PIPES_FETCHING, false)
    }, response => {
      commit(PIPES_FETCHING, false)
    })
  }
}

const getters = {
  [HAS_PROJECT_PIPES]: () => {
    return state.items.length > 0
  }
}

export default {
  state,
  mutations,
  actions,
  getters
}
