import Vue from 'vue'
import Vuex from 'vuex'
import * as getters from './getters'
import mutations from './mutations/index'
import actions from './actions/index'
import initialState from './state'

// use Vuex for centralized state management
Vue.use(Vuex)

export default new Vuex.Store({
  state: initialState(),
  getters,
  actions,
  mutations,
  strict: process.env.NODE_ENV !== 'production'
})
