import Vue from 'vue'
import Vuex from 'vuex'
import modules from './modules'
import mutations from './mutations/index'
import actions from './actions/index'
import * as getters from './getters'
import getDefaultState from './state'

// use Vuex for centralized state management
Vue.use(Vuex)

export default new Vuex.Store({
  state: getDefaultState(),
  modules,
  mutations,
  actions,
  getters,
  strict: process.env.NODE_ENV !== 'production'
})
