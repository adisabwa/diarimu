import PublicLayout from '@/layouts/PublicLayout.vue'
import MainLayout from '@/layouts/MainLayout.vue'

import config from '@/config/url'
const baseUrl = config.baseUrl
const siteUrl = config.siteUrl

let routes = [
  {
    children: [
      {
        path: baseUrl,
        component: PublicLayout,
        children: [
          {
            path: '',
            alias: ['index.php','index.php/p'],
            name: 'default', 
            component: () => import('@/pages/Default.vue'),
            meta: {
              enterFromClass : "-translate-x-full opacity-0",
              enterToClass : "opacity-50",
              leaveFromClass : "opacity-50",
              leaveToClass : "translate-x-full opacity-0",
              pageTitle: '<b>Darul Arqom Patean Boarding School</b>',
            }
          }
        ]
      },
      {
        path: siteUrl + 'p/',
        component: MainLayout,
        children: [
          {
            path: 'dashboard',
            name: 'dashboard', 
            component: () => import('@/pages/Dashboard.vue'),
            meta: {
              enterFromClass : "-translate-x-full opacity-0",
              enterToClass : "opacity-50",
              leaveFromClass : "opacity-50",
              leaveToClass : "translate-x-full opacity-0",
              pageTitle: '<b>Darul Arqom Patean Boarding School</b>',
            }
          }
        ]
      },
    ],
  }
]

export default routes;