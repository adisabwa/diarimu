let management = [
    {
      index: 'dashboard',
      route: { name:'management-dashboard' },
      icon:'mdi:view-dashboard',
      title:'Dashboard',
      type:'menu',
    },
    {
      index:'data',
      icon:'fluent:clipboard-data-bar-24-filled',
      title:'Data',
      type:'submenu',
      children: [
        {
          index:'pcm-list',
          route: { name:'admin-management-data', params: { type: 'pcm'} },
          title:'Pimpinan Cabang',
        },
        {
          index:'prm-list',
          route: { name:'admin-management-data', params: { type: 'prm'} },
          title:'Pimpinan Ranting',
        },
        {
          index:'unit-list',
          route: { name:'admin-management-data', params: { type: 'unit'} },
          title:'Unit Pembantu Pimpinan',
        },
      ]
    },
    {
      index: 'admin-management-member',
      route: { name:'admin-management-data', params: { type: 'anggota'} },
      icon:'ph:notebook-fill',
      title:'Data Anggota',
      type:'menu',
    },
    {
      index: 'admin-management-aum',
      route: { name:'admin-management-aum' },
      icon:'fa6-solid:school',
      title:'Amal Usaha',
      type:'menu',
    },
  ]

  export default management