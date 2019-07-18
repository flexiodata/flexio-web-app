import Vue from 'vue'
import Router from 'vue-router'
import Meta from 'vue-meta'

import * as rn from '../constants/route'
import admin_routes from './admin'

import AppAccount from '@comp/AppAccount'
import AppPipes from '@comp/AppPipes'
import AppConnections from '@comp/AppConnections'
import AppMembers from '@comp/AppMembers'

import PipeDocument from '@comp/PipeDocument'
import BuilderDocument from '@comp/BuilderDocument'

import SignInPage from '@comp/SignInPage'
import SignUpPage from '@comp/SignUpPage'
import ForgotPasswordPage from '@comp/ForgotPasswordPage'
import ResetPasswordPage from '@comp/ResetPasswordPage'
import InitSessionPage from '@comp/InitSessionPage'
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
  { path: '/signin',                                name: rn.ROUTE_SIGNIN_PAGE,         component: SignInPage                },
  { path: '/signup',                                name: rn.ROUTE_SIGNUP_PAGE,         component: SignUpPage                },
  { path: '/forgotpassword',                        name: rn.ROUTE_FORGOTPASSWORD_PAGE, component: ForgotPasswordPage        },
  { path: '/resetpassword',                         name: rn.ROUTE_RESETPASSWORD_PAGE,  component: ResetPasswordPage         },
  { path: '/initsession',                           name: rn.ROUTE_INITSESSION_PAGE,    component: InitSessionPage           },
  { path: '/onboard',                               name: rn.ROUTE_ONBOARD_PAGE,        component: OnboardingPage,      meta },
  { path: '/builder/:template',                     name: rn.ROUTE_APP_BUILDER,         component: BuilderDocument,     meta },
  { path: '/account/:section?',                     name: rn.ROUTE_APP_ACCOUNT,         component: AppAccount,          meta },
  { path: '/:team_name?/members',                   name: rn.ROUTE_APP_MEMBERS,         component: AppMembers,          meta },
  { path: '/:team_name?/connections/:object_name?', name: rn.ROUTE_APP_CONNECTIONS,     component: AppConnections,      meta },
  { path: '/:team_name?/pipes/:object_name?',       name: rn.ROUTE_APP_PIPES,           component: AppPipes,            meta },
  admin_routes,
  { path: "*", component: PageNotFound } // 404
]

export default new Router({
  mode: 'history', // use HTML5 history
  base: '/app/', // serve app from /app base folder
  routes
})
