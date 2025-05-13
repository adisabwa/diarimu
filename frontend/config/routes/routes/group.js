import PublicLayout from '@/layouts/PublicLayout.vue'
import MainLayout from '@/layouts/MainLayout.vue'

import config from '@/config/url'
const baseUrl = config.baseUrl
const siteUrl = config.siteUrl

let routes = [
  {
    meta:{
      app: 'group'
    },
    children: [
      {
        path: siteUrl + 'p/group',
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
                path: '',
                name: 'group-admin', 
                component: () => import('@/pages/group/admin/Index.vue'),
                meta: {
                    pageTitle: "<b>Daftar Group</b>",
                    allowedRoles:['admin'],
                    // redirect:'dashboard',
                }
              },
            ]
          },
          // {
          //   path: 'wajib',
          //   component: MainLayout,
          //   meta: {
          //       requiresAuth: true,
          //       enterFromClass : "scale-0 opacity-50",
          //       enterToClass : "opacity-100",
          //       leaveFromClass : "opacity-100",
          //       leaveToClass : "scale-0 opacity-50",
          //   },
          //   children: [ 
          //     {
          //       path: '',
          //       name: 'group-wajib', 
          //       component: () => import('@/pages/group/wajib/Index.vue'),
          //       meta: {
          //           pageTitle: "<b>Daftar Sholat Wajib</b>",
          //       }
          //     },
          //   ]
          // },
        ],
      },
    ],
  }
]

export default routes;