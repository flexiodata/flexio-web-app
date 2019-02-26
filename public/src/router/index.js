import Vue from 'vue'
import Router from 'vue-router'
import Meta from 'vue-meta'

import * as rn from '../constants/route'
import admin_routes from './admin'

import AppPipes from '../components/AppPipes.vue'
import AppConnections from '../components/AppConnections.vue'
import AppStorage from '../components/AppStorage.vue'
import AppActivity from '../components/AppActivity.vue'
import AppAccount from '../components/AppAccount.vue'

import PipeDocument from '../components/PipeDocument.vue'
import BuilderDocument from '../components/BuilderDocument.vue'

import SignInPage from '../components/SignInPage.vue'
import SignUpPage from '../components/SignUpPage.vue'
import ForgotPasswordPage from '../components/ForgotPasswordPage.vue'
import ResetPasswordPage from '../components/ResetPasswordPage.vue'
import InitSessionPage from '../components/InitSessionPage.vue'
import OnboardingPage from '../components/OnboardingPage.vue'
import PageNotFound from '../components/PageNotFound.vue'

// use VueRouter for handling browser history
Vue.use(Router)

// use VueMeta for page metadata
Vue.use(Meta)

// any route containing a 'meta' node below will require authorization
const meta = { requiresAuth: true }

const routes = [
  { path: '/', redirect: '/pipes' }, // base path redirect
  { path: '/signin',                                     name: rn.ROUTE_SIGNIN_PAGE,          component: SignInPage                },
  { path: '/signup',                                     name: rn.ROUTE_SIGNUP_PAGE,          component: SignUpPage                },
  { path: '/forgotpassword',                             name: rn.ROUTE_FORGOTPASSWORD_PAGE,  component: ForgotPasswordPage        },
  { path: '/resetpassword',                              name: rn.ROUTE_RESETPASSWORD_PAGE,   component: ResetPasswordPage         },
  { path: '/initsession',                                name: rn.ROUTE_INITSESSION_PAGE,     component: InitSessionPage           },
  { path: '/:user_identifier?/pipes',                    name: rn.ROUTE_PIPE_LIST_PAGE,       component: AppPipes,            meta },
  { path: '/:user_identifier?/pipes/:identifier/:view?', name: rn.ROUTE_PIPE_PAGE,            component: PipeDocument,        meta },
  { path: '/:user_identifier?/connections',              name: rn.ROUTE_CONNECTION_LIST_PAGE, component: AppConnections,      meta },
  { path: '/:user_identifier?/storage',                  name: rn.ROUTE_STORAGE_PAGE,         component: AppStorage,          meta },
  { path: '/:user_identifier?/activity',                 name: rn.ROUTE_ACTIVITY_LIST_PAGE,   component: AppActivity,         meta },
  { path: '/account/:section?',                          name: rn.ROUTE_ACCOUNT_PAGE,         component: AppAccount,          meta },
  { path: '/builder/:template',                          name: rn.ROUTE_BUILDER_PAGE,         component: BuilderDocument,     meta },
  { path: '/onboard',                                    name: rn.ROUTE_ONBOARD_PAGE,         component: OnboardingPage,      meta },
  admin_routes,
  { path: "*", component: PageNotFound } // 404
]

export default new Router({
  mode: 'history', // use HTML5 history
  base: '/app/', // serve app from /app base folder
  routes
})
