import Vue from 'vue'
import VueRouter from 'vue-router'

import account from './account'
import copypipe from './copypipe'
import dev from './dev'
import embed from './embed'
import home from './home'
import pipes from './pipes'

import {
  ROUTE_HOME,
  ROUTE_SIGNIN,
  ROUTE_SIGNUP,
  ROUTE_FORGOTPASSWORD,
  ROUTE_RESETPASSWORD
} from '../constants/route'

import SignIn from '../components/SignIn.vue'
import SignUp from '../components/SignUp.vue'
import ForgotPassword from '../components/ForgotPassword.vue'
import ResetPassword from '../components/ResetPassword.vue'
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
  copypipe,
  dev,
  embed,
  home,
  pipes,
  { path: '/signin',         name: ROUTE_SIGNIN,         component: SignIn         },
  { path: '/signup',         name: ROUTE_SIGNUP,         component: SignUp         },
  { path: '/forgotpassword', name: ROUTE_FORGOTPASSWORD, component: ForgotPassword },
  { path: '/resetpassword',  name: ROUTE_RESETPASSWORD,  component: ResetPassword  },
  { path: "*",                                           component: PageNotFound   }
]

export default new VueRouter({
  mode: 'history', // use HTML5 history
  base: '/app/', // serve app from /app base folder
  routes
})
