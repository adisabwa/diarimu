import PublicLayout from '@/layouts/PublicLayout.vue'
import MainLayout from '@/layouts/MainLayout.vue'

import config from '@/config/url'
const baseUrl = config.baseUrl
const siteUrl = config.siteUrl

let routes = [
  {
    meta:{
      app: 'iqab'
    },
    children: [
      {
        path: siteUrl + 'p/iqab/',
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
                    name: 'iqab-dashboard',
                    component: () => import('@/pages/iqab/Dashboard.vue'),
                  },
                  {
                    name: 'iqab-data',
                    path: 'datas/:type?',
                    props: true,
                    component: () => import('@/pages/data/DataList.vue'),
                  },   
                  {
                    path: '',
                    name: 'admin-iqab', 
                    component: () => import('@/pages/iqab/admin/Index.vue'),
                    meta: {
                        pageTitle: '<b>Daftar Pelanggaran Santri</b>',
                    }
                  },
                  {
                    path: 'rekapitulasi',
                    name: 'rekap-iqab', 
                    component: () => import('@/pages/iqab/admin/Rekapitulasi.vue'),
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