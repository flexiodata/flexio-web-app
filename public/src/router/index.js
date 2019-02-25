import Vue from 'vue'
import Router from 'vue-router'
import Meta from 'vue-meta'

import * as rn from '../constants/route'

import account from './account'
import admin from './admin'
import builder from './builder'
import home from './home'
import byuser from './byuser'
import pipes from './pipes'
import onboard from './onboard'

import AppPipes from '../components/AppPipes.vue'
import AppConnections from '../components/AppConnections.vue'
import AppStorage from '../components/AppStorage.vue'
import AppActivity from '../components/AppActivity.vue'

import SignInPage from '../components/SignInPage.vue'
import SignUpPage from '../components/SignUpPage.vue'
import ForgotPasswordPage from '../components/ForgotPasswordPage.vue'
import ResetPasswordPage from '../components/ResetPasswordPage.vue'
import InitSessionPage from '../components/InitSessionPage.vue'
import PageNotFound from '../components/PageNotFound.vue'

const basepath_redirect = {
  path: '/',
  redirect: '/pipes'
}

const page_not_found = {
  path: "*",
  component: PageNotFound
}

// use VueRouter for handling browser history
Vue.use(Router)

// use VueMeta for page metadata
Vue.use(Meta)

// any route containing a 'meta' node below will require authorization
const meta = { requiresAuth: true }

const routes = [
  basepath_redirect,
  { path: '/signin',         name: rn.ROUTE_SIGNIN_PAGE,          component: SignInPage              },
  { path: '/signup',         name: rn.ROUTE_SIGNUP_PAGE,          component: SignUpPage              },
  { path: '/forgotpassword', name: rn.ROUTE_FORGOTPASSWORD_PAGE,  component: ForgotPasswordPage      },
  { path: '/resetpassword',  name: rn.ROUTE_RESETPASSWORD_PAGE,   component: ResetPasswordPage       },
  { path: '/initsession',    name: rn.ROUTE_INITSESSION_PAGE,     component: InitSessionPage         },
  { path: '/pipes',          name: rn.ROUTE_PIPE_LIST_PAGE,       component: AppPipes,          meta },
  { path: '/connections',    name: rn.ROUTE_CONNECTION_LIST_PAGE, component: AppConnections,    meta },
  { path: '/storage',        name: rn.ROUTE_STORAGE_PAGE,         component: AppStorage,        meta },
  { path: '/activity',       name: rn.ROUTE_ACTIVITY_LIST_PAGE,   component: AppActivity,       meta },
  onboard,
  account,
  admin,
  builder,
  pipes,
  byuser,
  page_not_found
]

export default new Router({
  mode: 'history', // use HTML5 history
  base: '/app/', // serve app from /app base folder
  routes
})
