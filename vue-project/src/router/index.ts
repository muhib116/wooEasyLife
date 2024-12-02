import { createRouter, createWebHashHistory, createWebHistory } from 'vue-router'
import HomeView from '@/pages/dashboard/Index.vue'
import OrdersView from '@/pages/orders/Index.vue'
import FraudCheckerView from '@/pages/fraudChecker/Index.vue'
import ConfigView from '@/pages/config/Index.vue'
import SMSConfigView from '@/pages/config/smsConfig/Index.vue'
import LicenseView from '@/pages/config/license/Index.vue'
import SendSmsView from '@/pages/config/sendSms/Index.vue'
import RechargeView from '@/pages/config/recherge/Index.vue'
import IntegrationView from '@/pages/config/integration/Index.vue'
import CourierView from '@/pages/config/courier/Index.vue'

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
    {
      path: '/fraud-check',
      name: 'fraudCheck',
      component: FraudCheckerView,
    },
    {
      path: '/config',
      name: 'config',
      component: ConfigView,
      children: [
        {
          path: '/license',
          name: 'license',
          component: LicenseView
        },
        {
          path: '/sms-config',
          name: 'smsConfig',
          component: SMSConfigView
        },
        {
          path: '/send-sms',
          name: 'sendSms',
          component: SendSmsView
        },
        {
          path: '/recharge',
          name: 'recharge',
          component: RechargeView
        },
        {
          path: '/integration',
          name: 'integration',
          component: IntegrationView
        },
        {
          path: '/courier',
          name: 'courier',
          component: CourierView
        },
      ]
    },
  ],
})

export default router
