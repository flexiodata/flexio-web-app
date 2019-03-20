import Vue from 'vue'
import {
  Alert,
  Breadcrumb,
  BreadcrumbItem,
  Button,
  Checkbox,
  CheckboxButton,
  CheckboxGroup,
  Collapse,
  CollapseItem,
  DatePicker,
  Dialog,
  Dropdown,
  DropdownItem,
  DropdownMenu,
  Form,
  FormItem,
  Icon,
  Input,
  Menu,
  MenuItem,
  Message,
  MessageBox,
  Option,
  Pagination,
  Popover,
  Radio,
  RadioButton,
  RadioGroup,
  Select,
  Switch,
  Table,
  TableColumn,
  Tabs,
  TabPane,
  Tag
} from 'element-ui'
import lang from 'element-ui/lib/locale/lang/en'
import locale from 'element-ui/lib/locale'
import VueScrollTo from 'vue-scrollto'
import App from './components/App.vue'
import router from './router' // VueRouter
import store from './store' // Vuex store
import { fallbackCss } from './utils/dom'
import {
  ROUTE_INITSESSION_PAGE,
  ROUTE_SIGNIN_PAGE,
  ROUTE_SIGNUP_PAGE,
  ROUTE_BUILDER_PAGE,
  ROUTE_PIPE_LIST_PAGE
} from './constants/route'
import {
  CHANGE_ACTIVE_DOCUMENT,
  CHANGE_ROUTED_USER
} from './store/mutation-types'

// fallback css (if there's no Internet connection)

fallbackCss('tachyons-css-test', '/dist/css/tachyons.min.css')

// setup for Element UI

locale.use(lang)

Vue.use(Alert)
Vue.use(Breadcrumb)
Vue.use(BreadcrumbItem)
Vue.use(Button)
Vue.use(Checkbox)
Vue.use(CheckboxButton)
Vue.use(CheckboxGroup)
Vue.use(Collapse)
Vue.use(CollapseItem)
Vue.use(DatePicker)
Vue.use(Dialog)
Vue.use(Dropdown)
Vue.use(DropdownItem)
Vue.use(DropdownMenu)
Vue.use(Form)
Vue.use(FormItem)
Vue.use(Icon)
Vue.use(Input)
Vue.use(Menu)
Vue.use(MenuItem)
Vue.use(Option)
Vue.use(Pagination)
Vue.use(Popover)
Vue.use(Radio)
Vue.use(RadioButton)
Vue.use(RadioGroup)
Vue.use(Select)
Vue.use(Switch)
Vue.use(Table)
Vue.use(TableColumn)
Vue.use(Tabs)
Vue.use(TabPane)
Vue.use(Tag)

Vue.prototype.$message = Message
Vue.prototype.$msgbox = MessageBox
Vue.prototype.$alert = MessageBox.alert
Vue.prototype.$confirm = MessageBox.confirm
Vue.prototype.$prompt = MessageBox.prompt

// setup for VueScrollTo (programmatic scrolling)

Vue.use(VueScrollTo)

/*
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
*/

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

  const tryFetchConnections = () => {
    if (!store.state.connections_fetched && !store.state.connections_fetching) {
      store.dispatch('v2_action_fetchConnections', {}).catch(error => {
        // TODO: add error handling?
      })
    }
  }

  const tryFetchTokens = () => {
    if (!store.state.tokens_fetched && !store.state.tokens_fetching) {
      store.dispatch('v2_action_fetchTokens', {}).catch(error => {
        // TODO: add error handling?
      })
    }
  }

  const redirectToSignIn = () => {
    var redirect_to_signup = to.name == ROUTE_BUILDER_PAGE
    next({
      name: redirect_to_signup ? ROUTE_SIGNUP_PAGE : ROUTE_SIGNIN_PAGE,
      query: { redirect: to.fullPath }
    })
  }

  const goNext = () => {
    var user = store.getters.getActiveUser
    var active_username = _.get(user, 'username', store.state.active_user_eid)

    store.commit(CHANGE_ROUTED_USER, to.params.user_identifier || active_username)

    tryFetchConnections()
    tryFetchTokens()
    next()
  }

  if (store.state.active_user_eid.length > 0)
  {
    // user is signed in; move to the next route
    goNext()
  } else {
    // we're already fetching the user; we're done
    if (store.state.user_fetching) {
      return
    }

    if (to.matched.some(record => record.meta.requiresAuth)) {
      // this route requires authentication; check if the user is signed in...
      store.dispatch('v2_action_fetchCurrentUser').then(response => {
        if (store.state.active_user_eid.length > 0) {
          // user is signed in; move to the next route
          goNext()
        } else {
          // user is not signed in; redirect them to the sign in page
          redirectToSignIn()
        }
      }).catch(error => {
        // error fetching current user; bail out
        redirectToSignIn()
      })
    }
     else
    {
      // the 'init session' route already attempts to fetch the current user; we're done
      if (to.name == ROUTE_INITSESSION_PAGE) {
        next()
        return
      }

      // this route does not require authentication; try to sign in just to make
      // sure we know who the active user is and move to the next route
      store.dispatch('v2_action_fetchCurrentUser').then(response => {
        goNext()
      }).catch(error => {
        goNext()
      })
    }
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
