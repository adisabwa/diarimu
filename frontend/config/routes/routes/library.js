import MainLayout from '@/layouts/MainLayout.vue'

import config from '@/config/url'
const baseUrl = config.baseUrl
const siteUrl = config.siteUrl

let routes = [
  {
    meta:{
      app: 'library'
    },
    children: [
      {
        path: siteUrl + 'p/library/',
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
                    name: 'library-dashboard',
                    component: () => import('@/pages/library/Dashboard.vue'),
                  },
                  {
                    name: 'book-list',
                    path: 'book',
                    props: true,
                    component: () => import('@/pages/library/admin/BookList.vue'),
                  },
                  {
                    name: 'library-data',
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