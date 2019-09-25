import axios from 'axios'
import yaml from 'js-yaml'

const getDefaultState = () => {
  return {
    is_fetching: false,
    is_fetched: false,
    items: [],
  }
}

const state = getDefaultState()

const mutations = {
  'RESET_STATE' (state) {
    _.assign(state, getDefaultState())
  },

  'FETCHING_INTEGRATIONS' (state, is_fetching) {
    state.is_fetching = is_fetching
    if (is_fetching === true) {
      state.is_fetched = false
    }
  },

  'FETCHED_INTEGRATIONS' (state, items) {
    state.items = [].concat(items)
    state.is_fetched = true
  },
}

const actions = {
  'fetch' ({ commit }) {
    // fetching a collection of items
    commit('FETCHING_INTEGRATIONS', true)

    return axios.get('https://static.flex.io/integrations/integrations.yml').then(response => {
      var items = []
      try {
        items = yaml.safeLoad(response.data)
      }
      catch (e) {
        items = []
      }

      commit('FETCHED_INTEGRATIONS', items)
      commit('FETCHING_INTEGRATIONS', false)
      return response
    }).catch(error => {
      commit('FETCHING_INTEGRATIONS', false)
      throw error
    })
  },
}

const getters = {
  getAllIntegrations (state) {
    return _.sortBy(state.items, ['title'])
  },

  getProductionIntegrations (state, getters) {
    return _.filter(getters.getAllIntegrations, { in_production: true })
  },
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
}

