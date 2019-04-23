import Vue from 'vue'
import Router from 'vue-router'
import Meta from 'vue-meta'

import * as rn from '../constants/route'
import admin_routes from './admin'

import AppPipes from '@comp/AppPipes'
import AppConnections from '@comp/AppConnections'
import AppStorage from '@comp/AppStorage'
import AppActivity from '@comp/AppActivity'
import AppAccount from '@comp/AppAccount'

import PipeDocument from '@comp/PipeDocument'
import BuilderDocument from '@comp/BuilderDocument'

import SignInPage from '@comp/SignInPage'
import SignUpPage from '@comp/SignUpPage'
import ForgotPasswordPage from '@comp/ForgotPasswordPage'
import ResetPasswordPage from '@comp/ResetPasswordPage'
import InitSessionPage from '@comp/InitSessionPage'
import PrototypePage from '@comp/PrototypePage'
import OnboardingPage from '@comp/OnboardingPage'
import PageNotFound from '@comp/PageNotFound'

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
  { path: '/prototype',                                  name: rn.ROUTE_PROTOTYPE_PAGE,       component: PrototypePage             },
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
