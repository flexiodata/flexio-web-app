import Vue from 'vue'
import App from '@comp/App'
import router from '@/router' // VueRouter
import store from '@/store' // Vuex store
import { fallbackCss } from '@/utils/dom'

import '@/bootstrap/element-ui' // Element UI includes
import '@/bootstrap/plugins'    // Vue plugins
import '@/bootstrap/directives' // Vue directives
import '@/bootstrap/guards'     // VueRouter guards
import '@/bootstrap/clipboard'  // clipboard.js access

// fallback css (if there's no Internet connection)
fallbackCss('tachyons-css-test', '/dist/css/tachyons.min.css')

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
