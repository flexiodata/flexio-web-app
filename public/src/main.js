import Vue from 'vue'
import KeenUi from 'keen-ui'
import VeeValidate from 'vee-validate'
import VueScrollTo from 'vue-scrollto'
import App from './components/App.vue'
import router from './router' // VueRouter
import store from './store' // Vuex store
import { CHANGE_ACTIVE_DOCUMENT } from './store/mutation-types'
import './stylesheets/style.less' // common styles

// setup for VeeValidate (form validation)

Vue.use(VeeValidate)

// setup for KeenUi

Vue.use(KeenUi)

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

// global route guards

router.beforeEach((to, from, next) => {
  // update the active document in the store
  store.commit(CHANGE_ACTIVE_DOCUMENT, to.params.eid || to.name)

  // move to the next route
  next()
})

// global clipboard access

var copy = new Clipboard('.clipboardjs, [data-clipboard-text]')

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

// for now, we'll issue an XHR every time a page in Flex.io
// is hard-loaded to identify the active user; in the future,
// we should figure out how to store this in the localStorage
// or in the PHP session variables

store.dispatch('fetchCurrentUser')

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
