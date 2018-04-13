import Vue from 'vue'
import VueRouter from 'vue-router'

import account from './account'
import admin from './admin'
import builder from './builder'
import home from './home'
import pipes from './pipes'

import {
  ROUTE_BUILDER,
  ROUTE_HOME,
  ROUTE_SIGNIN,
  ROUTE_SIGNUP,
  ROUTE_FORGOTPASSWORD,
  ROUTE_RESETPASSWORD
} from '../constants/route'

import SignInPage from '../components/SignInPage.vue'
import SignUpPage from '../components/SignUpPage.vue'
import ForgotPasswordPage from '../components/ForgotPasswordPage.vue'
import ResetPasswordPage from '../components/ResetPasswordPage.vue'
import PageNotFound from '../components/PageNotFound.vue'

// use VueRouter for handling browser history
Vue.use(VueRouter)

const basepath_redirect = {
  path: '/',
  redirect: {
    name: ROUTE_HOME
  }
}

const routes = [
  basepath_redirect,
  account,
  admin,
  builder,
  home,
  pipes,
  { path: '/signin',         name: ROUTE_SIGNIN,         component: SignInPage         },
  { path: '/signup',         name: ROUTE_SIGNUP,         component: SignUpPage         },
  { path: '/forgotpassword', name: ROUTE_FORGOTPASSWORD, component: ForgotPasswordPage },
  { path: '/resetpassword',  name: ROUTE_RESETPASSWORD,  component: ResetPasswordPage  },
  { path: "*",                                           component: PageNotFound       }
]

export default new VueRouter({
  mode: 'history', // use HTML5 history
  base: '/app/', // serve app from /app base folder
  routes
})
