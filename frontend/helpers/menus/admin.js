let admin = [
  {
    index:'dashboard',
    route:'dashboard',
    function:'',
    icon:'mdi:home',
    label:'Beranda',
  },
  {
    index:'data-list',
    route:'data-list',
    function:'',
    icon:'garden:user-list-fill-16',
    label:'Data Pengguna',
    params:{
      type:'pengguna',
    }
  },
  {
    index:'group-admin',
    route:'group-admin',
    function:'',
    icon:'mingcute:group-3-fill',
    label:'Data Kelompok',
  },
  {
    index:'account',
    route:'account',
    function:'',
    icon:'mdi:account',
    label:'Data Profil',
  },
  {
    route:'',
    function:'doLogout',
    icon:'mdi:logout',
    label:'Keluar',
  },
]

export default admin