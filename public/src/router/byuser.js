import {
  ROUTE_APP_PIPES,
  ROUTE_APP_CONNECTIONS,
  ROUTE_APP_STORAGE,
  ROUTE_APP_ACTIVITY,
  ROUTE_PIPES
} from '../constants/route'
import AppByUser from '../components/AppByUser.vue'
import AppPipes from '../components/AppPipes.vue'
import AppConnections from '../components/AppConnections.vue'
import AppStorage from '../components/AppStorage.vue'
import AppActivity from '../components/AppActivity.vue'
import PipeDocument from '../components/PipeDocument.vue'

export default {
  path: '/:user_identifier',
  component: AppByUser,
  meta: { requiresAuth: true },
  children: [
    {
      // redirect to pipe list
      path: '',
      redirect: 'pipes'
    },
    {
      // pipe list
      path: 'pipes',
      name: ROUTE_APP_PIPES,
      component: AppPipes
    },
    {
      // pipe document
      path: 'pipes/:identifier/:view?/:state?',
      component: PipeDocument
    },
    {
      // connection list
      path: 'connections',
      name: ROUTE_APP_CONNECTIONS,
      component: AppConnections
    },
    {
      // storage
      path: 'storage',
      name: ROUTE_APP_STORAGE,
      component: AppStorage
    },
    {
      // activity
      path: 'activity',
      name: ROUTE_APP_ACTIVITY,
      component: AppActivity
    }
  ]
}
