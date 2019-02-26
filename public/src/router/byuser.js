import {
  ROUTE_BYUSER_PAGE,
  ROUTE_BYUSER_PIPE_PAGE,
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
      component: AppPipes
    },
    {
      // pipe document
      path: 'pipes/:identifier/:view?/:state?',
      name: ROUTE_BYUSER_PIPE_PAGE,
      component: PipeDocument
    },
    {
      // connection list
      path: 'connections',
      component: AppConnections
    },
    {
      // storage
      path: 'storage',
      component: AppStorage
    },
    {
      // activity
      path: 'activity',
      component: AppActivity
    }
  ]
}
