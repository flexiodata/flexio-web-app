import Vue from 'vue'
import VueScrollTo from 'vue-scrollto'
import App from './components/App.vue'
import router from './router' // VueRouter
import store from './store' // Vuex store
import { fallbackCss } from './utils/dom'

import element from './bootstrap/element-ui' // Element UI
import directives from './bootstrap/directives' // Vue directives
import clipboard from './bootstrap/clipboard' // clipboard.js access
import guards from './bootstrap/guards' // VueRouter guards

// fallback css (if there's no Internet connection)

fallbackCss('tachyons-css-test', '/dist/css/tachyons.min.css')

// setup for VueScrollTo (programmatic scrolling)

Vue.use(VueScrollTo)

// add helper `track` method to global store object

store.track = function(event_name, attrs) {
  attrs = _.assign({}, attrs, { event_name })
  store.dispatch('analyticsTrack', attrs)
}

// initial view necessary to begin using Vue

const app = new Vue({
  el: '#app',
  // provide the router using the 'router' option.
  // this will inject the router to make the whole app router-aware.
  router,
  // provide the store using the 'store' option.
  // this will inject the store instance to all child components.
  store,
  render: h => h(App)
})
