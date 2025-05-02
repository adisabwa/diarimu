import PublicLayout from '@/layouts/PublicLayout.vue'
import MainLayout from '@/layouts/MainLayout.vue'

import config from '@/config/url'
const baseUrl = config.baseUrl
const siteUrl = config.siteUrl

let routes = [
  {
    meta:{
      app: 'saving',
    },
    children: [
      {
        path: siteUrl + 'p/saving/',
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
                    name: 'saving-dashboard',
                    component: () => import('@/pages/saving/Dashboard.vue'),
                  },
                  {
                    name: 'saving-data',
                    path: 'datas/:type?',
                    props: true,
                    component: () => import('@/pages/data/DataList.vue'),
                  },   
                  {
                    path: '',
                    name: 'admin-saving', 
                    component: () => import('@/pages/saving/admin/Index.vue'),
                    meta: {
                        pageTitle: '<b>Daftar Pelanggaran Santri</b>',
                    }
                  },
                  {
                    path: 'rekapitulasi',
                    name: 'rekap-saving', 
                    component: () => import('@/pages/saving/admin/Rekapitulasi.vue'),
                    meta: {
                        pageTitle: '<b>Rekapitulasi Pelanggaran</b>',
                    }
                  },
                ]
            },
        ],
      },
    ],
  }
]

export default routes;