import {
  ROUTE_BYUSER_PAGE,
  ROUTE_PIPE_LIST_PAGE,
  ROUTE_CONNECTION_LIST_PAGE,
  ROUTE_STORAGE_PAGE,
  ROUTE_ACTIVITY_LIST_PAGE,
  ROUTE_PIPE_PAGE
} from '../constants/route'
import AppByUser from '../components/AppByUser.vue'
import AppPipes from '../components/AppPipes.vue'
import AppConnections from '../components/AppConnections.vue'
import AppStorage from '../components/AppStorage.vue'
import AppActivity from '../components/AppActivity.vue'
import PipeDocument from '../components/PipeDocument.vue'

export default {
  path: '/:user_identifier',
  name: ROUTE_BYUSER_PAGE,
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
      name: ROUTE_PIPE_LIST_PAGE,
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
      name: ROUTE_CONNECTION_LIST_PAGE,
      component: AppConnections
    },
    {
      // storage
      path: 'storage',
      name: ROUTE_STORAGE_PAGE,
      component: AppStorage
    },
    {
      // activity
      path: 'activity',
      name: ROUTE_ACTIVITY_LIST_PAGE,
      component: AppActivity
    }
  ]
}
