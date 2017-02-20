import api from '../../api'
import { GET_PROJECTS, HAS_PROJECTS } from '../action-types'
import { PROJECTS_FETCHING, PROJECTS_RECEIVED } from '../mutation-types'

const state = {
  is_fetching: false,
  items: []
}

const mutations = {
  [PROJECTS_FETCHING] (state, is_fetching) {
    state.is_fetching = is_fetching
  },
  [PROJECTS_RECEIVED] (state, items) {
    state.items = items
  }
}

const actions = {
  // use argument destructuring below instead of passing 'context'
  // as the argument and then calling 'context.commit', etc.
  [GET_PROJECTS]: ({ commit, dispatch }) => {
    commit(PROJECTS_FETCHING, true)

    api.fetchProjects().then(response => {
      commit(PROJECTS_RECEIVED, response.body)
      commit(PROJECTS_FETCHING, false)
    }, response => {
      commit(PROJECTS_FETCHING, false)
    })
  }
}

const getters = {
  [HAS_PROJECTS]: () => {
    return state.items.length > 0
  }
}

export default {
  state,
  mutations,
  actions,
  getters
}
