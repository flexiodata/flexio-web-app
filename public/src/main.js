import Vue from 'vue'
import KeenUi from 'keen-ui'
import {
  Button,
  Dialog,
  Dropdown,
  DropdownItem,
  DropdownMenu,
  Icon,
  Input,
  MessageBox,
  Option,
  Select
} from 'element-ui'
import lang from 'element-ui/lib/locale/lang/en'
import locale from 'element-ui/lib/locale'
import VeeValidate from 'vee-validate'
import VueScrollTo from 'vue-scrollto'
import App from './components/App.vue'
import router from './router' // VueRouter
import store from './store' // Vuex store
import util from './utils'
import { ROUTE_SIGNIN } from './constants/route'
import { CHANGE_ACTIVE_DOCUMENT } from './store/mutation-types'
import './stylesheets/style.less' // common styles

// fallback css (if there's no Internet connection)

util.fallbackCss('tachyons-css-test', '/dist/css/tachyons.min.css')

// setup for VeeValidate (form validation)

Vue.use(VeeValidate)

// setup for KeenUi

Vue.use(KeenUi)

// setup for Element UI

locale.use(lang)

Vue.use(Button)
Vue.use(Dialog)
Vue.use(Dropdown)
Vue.use(DropdownItem)
Vue.use(DropdownMenu)
Vue.use(Icon)
Vue.use(Input)
Vue.use(Option)
Vue.use(Select)

Vue.prototype.$msgbox = MessageBox
Vue.prototype.$alert = MessageBox.alert
Vue.prototype.$confirm = MessageBox.confirm
Vue.prototype.$prompt = MessageBox.prompt

// setup for VueScrollTo (programmatic scrolling)

Vue.use(VueScrollTo)

// global directives (move to a new file if we get too many here)

Vue.directive('focus', {
  // when the bound element is inserted into the DOM...
  inserted: function(el) {
    // ...focus the element
    el.focus()
  }
})

Vue.directive('deferred-focus', {
  // when the bound element is inserted into the DOM...
  inserted: function(el, binding) {
    // ...focus the element after a delay (in milliseconds)
    var delay = typeof binding.value == 'number' ? binding.value : 10
    setTimeout(() => { el.focus() }, delay)
  }
})

Vue.directive('select-all', {
  // when the bound element is inserted into the DOM...
  inserted: function(el, binding) {
    // ...select all text after a delay (in milliseconds)
    var delay = typeof binding.value == 'number' ? binding.value : 10
    setTimeout(() => {
      el.selectionStart = 0
      el.selectionEnd = el.value.length
      el.focus()
    }, delay)
  }
})

// add helper `track` method to global store object

store.track = function(event_name, attrs) {
  attrs = _.assign({}, attrs, { event_name })
  store.dispatch('analyticsTrack', attrs)
}

// global route guards

router.afterEach((to, from) => {
  // log each route as a separate page view
  setTimeout(() => { analytics.page() }, 100)
})

router.beforeEach((to, from, next) => {
  // update the active document in the store
  store.commit(CHANGE_ACTIVE_DOCUMENT, to.params.eid || to.name)

  if (store.state.active_user_eid.length > 0)
  {
    // user is signed in; move to the next route
    store.dispatch('fetchConnections')
    next()
  }
   else if (to.matched.some(record => record.meta.requiresAuth))
  {
    // this route requires authentication

    if (store.state.user_fetching)
      return

    // check if the user is signed in
    store.dispatch('fetchCurrentUser').then(response => {
      if (store.state.active_user_eid.length > 0)
      {
        // user is signed in; move to the next route
        store.dispatch('fetchConnections')
        next()
      }
       else
      {
        // user is not signed in; redirect them to the sign in page
        next({
          name: ROUTE_SIGNIN,
          query: { redirect: to.fullPath }
        })
      }
    }, response => {
      // error fetching current user; bail out
      next({
        name: ROUTE_SIGNIN,
        query: { redirect: to.fullPath }
      })
    })
  }
   else
  {
    // we're already fetching the user; done
    if (store.state.user_fetching)
      return

    // this route does not require authentication; try to sign in just to make
    // sure we know who the active user is and move to the next route
    store.dispatch('fetchCurrentUser').then(response => {
      next()
    }, response => {
      next()
    })
  }
})

// global clipboard access

var copy = new Clipboard('.clipboardjs, [data-clipboard-text], [data-clipboard-target]')

copy.on('success', function(evt) {
  var t = evt.trigger
  var old = t.getAttribute('aria-label')

  t.setAttribute('hint--always', '')
  t.setAttribute('aria-label', 'Copied!')

  setTimeout(() => {
    t.removeAttribute('hint--always')

    if (old)
      t.setAttribute('aria-label', old)
       else
      t.removeAttribute('aria-label')
  }, 4000)
})

copy.on('error', function(evt) {
  var t = evt.trigger
  var old = t.getAttribute('aria-label')

  t.setAttribute('hint--always', '')
  t.setAttribute('aria-label', 'Press Ctrl+C to copy')

  setTimeout(() => {
    t.removeAttribute('hint--always')

    if (old)
      t.setAttribute('aria-label', old)
       else
      t.removeAttribute('aria-label')
  }, 4000)
})

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
