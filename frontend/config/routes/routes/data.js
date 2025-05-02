import PublicLayout from '@/layouts/PublicLayout.vue'

import config from '@/config/url'
const baseUrl = config.baseUrl
const siteUrl = config.siteUrl

let routes = [
 {
    path: siteUrl + 'p/psb/',
    component: PublicLayout,
    children: [
      {
        path: 'create',
        name: 'psb-create', 
        component: () => import('@/pages/psb/Create.vue'),
        meta: {
          enterFromClass : "translate-x-full opacity-0",
          enterToClass : "opacity-50",
          leaveFromClass : "opacity-50",
          pageTitle: '<b>Pendaftaran Santri Baru</b>',
          leaveToClass : "-translate-x-full opacity-0",
        }
      },
    ]
 },
]

export default routes;