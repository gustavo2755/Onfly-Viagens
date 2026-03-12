import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/authStore'
import LoginPage from '../pages/LoginPage.vue'
import DashboardPage from '../pages/DashboardPage.vue'
import TravelOrdersPage from '../pages/TravelOrdersPage.vue'
import CreateTravelOrderPage from '../pages/CreateTravelOrderPage.vue'
import TravelOrderDetailPage from '../pages/TravelOrderDetailPage.vue'

const routes = [
  { path: '/login', name: 'login', component: LoginPage, meta: { guestOnly: true } },
  { path: '/', redirect: '/travel-orders' },
  { path: '/travel-orders', name: 'travel-orders', component: TravelOrdersPage, meta: { requiresAuth: true } },
  { path: '/travel-orders/create', name: 'travel-orders-create', component: CreateTravelOrderPage, meta: { requiresAuth: true } },
  { path: '/travel-orders/:id', name: 'travel-orders-detail', component: TravelOrderDetailPage, meta: { requiresAuth: true } },
  { path: '/dashboard', name: 'dashboard', component: DashboardPage, meta: { requiresAuth: true, adminOnly: true } },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach(async (to) => {
  const authStore = useAuthStore()

  if (authStore.token && !authStore.user) {
    try {
      await authStore.fetchMe()
    } catch {
      authStore.signOut()
    }
  }

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    return { name: 'login' }
  }

  if (to.meta.guestOnly && authStore.isAuthenticated) {
    return { name: 'travel-orders' }
  }

  if (to.meta.adminOnly && !authStore.isAdmin) {
    return { name: 'travel-orders' }
  }

  return true
})

export default router
