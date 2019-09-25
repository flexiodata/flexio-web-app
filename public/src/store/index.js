import Vue from 'vue'
import Vuex from 'vuex'
import modules from './modules'
import mutations from './mutations'
import actions from './actions'
import getters from './getters'
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

// add helper methods to global store object

store.track = function(event_name, attrs) {
  attrs = _.assign({}, attrs, { event_name })
  store.dispatch('users/track', attrs)
}

store.getUniqueName = function(name, module) {
  if (!module) {
    throw('getUniqueName: `module` argument is required')
  }

  var is_found = true
  var unique_name = name
  var idx = 2
  while (is_found) {
    is_found = _.find(store.state[module].items, c => c.name == unique_name) ? true : false
    if (is_found) {
      unique_name = name + (idx++)
    }
  }
  return unique_name
}

export default store
