import Vue from 'vue'
import Router from 'vue-router'
import Meta from 'vue-meta'

import account from './account'
import activity from './activity'
import admin from './admin'
import builder from './builder'
import home from './home'
import pipes from './pipes'

import {
  ROUTE_BUILDER,
  ROUTE_HOME,
  ROUTE_SIGNIN,
  ROUTE_SIGNUP,
  ROUTE_ONBOARD,
  ROUTE_FORGOTPASSWORD,
  ROUTE_RESETPASSWORD
} from '../constants/route'

import SignInPage from '../components/SignInPage.vue'
import SignUpPage from '../components/SignUpPage.vue'
import ForgotPasswordPage from '../components/ForgotPasswordPage.vue'
import OnboardingPage from '../components/OnboardingPage.vue'
import ResetPasswordPage from '../components/ResetPasswordPage.vue'
import PageNotFound from '../components/PageNotFound.vue'

// use VueRouter for handling browser history
Vue.use(Router)

// use VueMeta for page metadata
Vue.use(Meta)

const basepath_redirect = {
  path: '/',
  redirect: {
    name: ROUTE_HOME
  }
}

const routes = [
  basepath_redirect,
  account,
  activity,
  admin,
  builder,
  home,
  pipes,
  { path: '/onboard',        name: ROUTE_ONBOARD,        component: OnboardingPage     },
  { path: '/signin',         name: ROUTE_SIGNIN,         component: SignInPage         },
  { path: '/signup',         name: ROUTE_SIGNUP,         component: SignUpPage         },
  { path: '/forgotpassword', name: ROUTE_FORGOTPASSWORD, component: ForgotPasswordPage },
  { path: '/resetpassword',  name: ROUTE_RESETPASSWORD,  component: ResetPasswordPage  },
  { path: "*",                                           component: PageNotFound       }
]

export default new Router({
  mode: 'history', // use HTML5 history
  base: '/app/', // serve app from /app base folder
  routes
})
