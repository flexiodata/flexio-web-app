import { ROUTE_ONBOARD_PAGE } from '../constants/route'
import OnboardingPage from '../components/OnboardingPage.vue'

export default {
  path: '/onboard',
  name: ROUTE_ONBOARD_PAGE,
  component: OnboardingPage,
  meta: { requiresAuth: true }
}
