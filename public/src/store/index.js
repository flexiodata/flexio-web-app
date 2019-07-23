import Vue from 'vue'
import Vuex from 'vuex'
import modules from './modules'
import mutations from './mutations/index'
import actions from './actions/index'
import * as getters from './getters'
import getDefaultState from './state'

// use Vuex for centralized state management
Vue.use(Vuex)

const store = new Vuex.Store({
  state: getDefaultState(),
  modules,
  mutations,
  actions,
  getters,
  strict: process.env.NODE_ENV !== 'production'
})

// add helper `track` method to global store object

store.track = function(event_name, attrs) {
  attrs = _.assign({}, attrs, { event_name })
  store.dispatch('users/track', attrs)
}

export default store
