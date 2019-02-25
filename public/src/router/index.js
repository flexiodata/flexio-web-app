import Vue from 'vue'
import Router from 'vue-router'
import Meta from 'vue-meta'

import account from './account'
import admin from './admin'
import builder from './builder'
import home from './home'
import byuser from './byuser'
import pipes from './pipes'
import onboard from './onboard'

import {
  ROUTE_SIGNIN_PAGE,
  ROUTE_SIGNUP_PAGE,
  ROUTE_FORGOTPASSWORD_PAGE,
  ROUTE_RESETPASSWORD_PAGE,
  ROUTE_INITSESSION_PAGE
} from '../constants/route'

import SignInPage from '../components/SignInPage.vue'
import SignUpPage from '../components/SignUpPage.vue'
import ForgotPasswordPage from '../components/ForgotPasswordPage.vue'
import ResetPasswordPage from '../components/ResetPasswordPage.vue'
import InitSessionPage from '../components/InitSessionPage.vue'
import PageNotFound from '../components/PageNotFound.vue'

const basepath_redirect =   {
  path: '/',
  redirect: '/pipes'
}

// use VueRouter for handling browser history
Vue.use(Router)

// use VueMeta for page metadata
Vue.use(Meta)

const routes = [
  basepath_redirect,
  account,
  admin,
  builder,
  home,
  pipes,
  byuser,
  onboard,
  { path: '/signin',         name: ROUTE_SIGNIN_PAGE,         component: SignInPage         },
  { path: '/signup',         name: ROUTE_SIGNUP_PAGE,         component: SignUpPage         },
  { path: '/forgotpassword', name: ROUTE_FORGOTPASSWORD_PAGE, component: ForgotPasswordPage },
  { path: '/resetpassword',  name: ROUTE_RESETPASSWORD_PAGE,  component: ResetPasswordPage  },
  { path: '/initsession',    name: ROUTE_INITSESSION_PAGE,    component: InitSessionPage    },
  { path: "*",                                                component: PageNotFound       }
]

export default new Router({
  mode: 'history', // use HTML5 history
  base: '/app/', // serve app from /app base folder
  routes
})
