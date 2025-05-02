import PublicLayout from '@/layouts/PublicLayout.vue'
import MainLayout from '@/layouts/MainLayout.vue'

import config from '@/config/url'
const baseUrl = config.baseUrl
const siteUrl = config.siteUrl

let routes = [
  {
    meta:{
      app: 'management'
    },
    children: [
      {
        path: siteUrl + 'p/management/',
        children: [
          {
              path: 'admin',
              component: MainLayout,
              meta: {
                requiresAuth: true,
                enterFromClass : "scale-0 opacity-50",
                enterToClass : "opacity-100",
                leaveFromClass : "opacity-100",
                leaveToClass : "scale-0 opacity-50",
              },
              children: [
                {
                  path: 'dashboard',
                  name: 'management-dashboard',
                  component: () => import('@/pages/management/Dashboard.vue'),
                },
                {
                  path: 'aum',
                  name: 'admin-management-aum',
                  component: () => import('@/pages/management/admin/Aum.vue'),
                },
                {
                  name: 'admin-management-data',
                  path: 'datas/:type?',
                  props: true,
                  component: () => import('@/pages/data/DataList.vue'),
                },   
              ]
          },
        ],
      },
    ],
  }
]

export default routes;