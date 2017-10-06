import {
  ROUTE_HOME_PIPES,
  ROUTE_HOME_MEMBERS,
  ROUTE_HOME_CONNECTIONS,
  ROUTE_HOME_TRASH
} from '../constants/route'
import * as types from '../store/mutation-types'
import store from '../store'
import ProjectHome from '../components/ProjectHome.vue'
import PipeManager from '../components/PipeManager.vue'
import MemberManager from '../components/MemberManager.vue'
import StorageManager from '../components/StorageManager.vue'
import TrashManager from '../components/TrashManager.vue'

export default {
  path: '/project/:eid',
  component: ProjectHome,
  meta: { requiresAuth: true },
  children: [
    {
      // redirect to /project/:eid/pipes
      path: '',
      redirect: 'pipes'
    },
    {
      // PipeManager will be rendered inside ProjectHome's <router-view>
      // when /project/:eid/pipes is matched
      path: 'pipes',
      name: ROUTE_HOME_PIPES,
      component: PipeManager
    },
    {
      // MemberManager will be rendered inside ProjectHome's <router-view>
      // when /project/:eid/members is matched
      path: 'members',
      name: ROUTE_HOME_MEMBERS,
      component: MemberManager
    },
    {
      // StorageManager will be rendered inside ProjectHome's <router-view>
      // when /project/:eid/connections is matched
      path: 'connections',
      name: ROUTE_HOME_CONNECTIONS,
      component: StorageManager
    },
    {
      // TrashManager will be rendered inside ProjectHome's <router-view>
      // when /project/:eid/trash is matched
      path: 'trash',
      name: ROUTE_HOME_TRASH,
      component: TrashManager
    }
  ]
}
