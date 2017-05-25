import {
  ROUTE_PROJECTPIPES,
  ROUTE_PROJECTMEMBERS,
  ROUTE_PROJECTCONNECTIONS,
  ROUTE_PROJECTTRASH
} from '../constants/route'
import * as types from '../store/mutation-types'
import store from '../store'
import AppHome from '../components/AppHome.vue'
import PipeManager from '../components/PipeManager.vue'
import MemberManager from '../components/MemberManager.vue'
import ConnectionManager from '../components/ConnectionManager.vue'
import TrashManager from '../components/TrashManager.vue'

export const basepath_redirect = {
  path: '/',
  component: AppHome,
  redirect: '/home' // TODO: use 'alias' here instead of 'redirect'?
}

export const home = {
  path: '/home',
  component: AppHome,
  meta: { requiresAuth: true },
  children: [
    {
      // redirect to /home/pipes
      path: '',
      redirect: 'pipes'
    },
    {
      // PipeManager will be rendered inside ProjectHome's <router-view>
      // when /home/pipes is matched
      path: 'pipes',
      name: ROUTE_PROJECTPIPES,
      component: PipeManager
    },
    {
      // MemberManager will be rendered inside ProjectHome's <router-view>
      // when /home/members is matched
      path: 'members',
      name: ROUTE_PROJECTMEMBERS,
      component: MemberManager
    },
    {
      // ConnectionManager will be rendered inside ProjectHome's <router-view>
      // when /home/connections is matched
      path: 'connections',
      name: ROUTE_PROJECTCONNECTIONS,
      component: ConnectionManager
    },
    {
      // TrashManager will be rendered inside ProjectHome's <router-view>
      // when /home/trash is matched
      path: 'trash',
      name: ROUTE_PROJECTTRASH,
      component: TrashManager
    }
  ]
}
