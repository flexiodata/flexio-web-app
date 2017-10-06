import Vue from 'vue'
import VueRouter from 'vue-router'

import account from './accounthome'
import copypipe from './copypipe'
import dev from './devhome'
import embed from './embedhome'
import home from './home'
import pipe from './pipehome'

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
  pipe,
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
