import { ROUTE_ONBOARD } from '../constants/route'
import OnboardingPage from '../components/OnboardingPage.vue'

export default {
  path: '/onboard',
  name: ROUTE_ONBOARD,
  component: OnboardingPage,
  meta: { requiresAuth: true }
}
