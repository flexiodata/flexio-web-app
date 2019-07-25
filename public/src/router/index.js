import Vue from 'vue'
import Router from 'vue-router'
import Meta from 'vue-meta'

import * as rn from '@/constants/route'
import admin_routes from './admin'

import AppAccount from '@/components/AppAccount'
import AppPipes from '@/components/AppPipes'
import AppConnections from '@/components/AppConnections'
import AppMembers from '@/components/AppMembers'

import PipeDocument from '@/components/PipeDocument'

import SignInPage from '@/components/SignInPage'
import SignUpPage from '@/components/SignUpPage'
import ForgotPasswordPage from '@/components/ForgotPasswordPage'
import ResetPasswordPage from '@/components/ResetPasswordPage'
import InitSessionPage from '@/components/InitSessionPage'
import VerifyPage from '@/components/VerifyPage'
import PageNotFound from '@/components/PageNotFound'

// use VueRouter for handling browser history
Vue.use(Router)

// use VueMeta for page metadata
Vue.use(Meta)

// any route containing a 'meta' node below will require authorization
const meta = { requiresAuth: true }

const routes = [
  { path: '/', redirect: '/pipes' }, // base path redirect
  { path: '/signin',                                name: rn.ROUTE_SIGNIN_PAGE,         component: SignInPage                },
  { path: '/signup/:action?',                       name: rn.ROUTE_SIGNUP_PAGE,         component: SignUpPage                },
  { path: '/forgotpassword',                        name: rn.ROUTE_FORGOTPASSWORD_PAGE, component: ForgotPasswordPage        },
  { path: '/resetpassword',                         name: rn.ROUTE_RESETPASSWORD_PAGE,  component: ResetPasswordPage         },
  { path: '/initsession',                           name: rn.ROUTE_INITSESSION_PAGE,    component: InitSessionPage           },
  { path: '/verify',                                name: rn.ROUTE_VERIFY_PAGE,         component: VerifyPage                },
  { path: '/account/:action?',                      name: rn.ROUTE_APP_ACCOUNT,         component: AppAccount,          meta },
  { path: '/:team_name?/members/:action?',          name: rn.ROUTE_APP_MEMBERS,         component: AppMembers,          meta },
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
