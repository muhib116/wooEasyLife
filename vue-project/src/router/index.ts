import { createRouter, createWebHashHistory, createWebHistory } from 'vue-router'
import HomeView from '@/pages/dashboard/Index.vue'
import OrdersView from '@/pages/orders/Index.vue'

const router = createRouter({
  history: createWebHashHistory(),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/orders',
      name: 'orders',
      component: OrdersView,
    },
  ],
})

export default router
