import Vue from 'vue'
import VueRouter from 'vue-router'
import { basepath_redirect, home } from './home'
import projecthome from './projecthome'
import pipehome from './pipehome'
import embedhome from './embedhome'
import devhome from './devhome'

import {
  ROUTE_ACCOUNT,
  ROUTE_SIGNIN,
  ROUTE_SIGNUP,
  ROUTE_FORGOTPASSWORD,
  ROUTE_RESETPASSWORD
} from '../constants/route'

import Account from '../components/Account.vue'
import SignIn from '../components/SignIn.vue'
import SignUp from '../components/SignUp.vue'
import ForgotPassword from '../components/ForgotPassword.vue'
import ResetPassword from '../components/ResetPassword.vue'

// use VueRouter for handling browser history
Vue.use(VueRouter)

const routes = [
  basepath_redirect,
  home,
  projecthome,
  pipehome,
  embedhome,
  devhome,
  { path: '/account',        name: ROUTE_ACCOUNT,        component: Account        },
  { path: '/signin',         name: ROUTE_SIGNIN,         component: SignIn         },
  { path: '/signup',         name: ROUTE_SIGNUP,         component: SignUp         },
  { path: '/forgotpassword', name: ROUTE_FORGOTPASSWORD, component: ForgotPassword },
  { path: '/resetpassword',  name: ROUTE_RESETPASSWORD,  component: ResetPassword  }
]

export default new VueRouter({
  mode: 'history', // use HTML5 history
  base: '/app/', // serve app from /app base folder
  routes
})
