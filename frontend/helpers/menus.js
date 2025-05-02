let iqab = [
  {
    index: 'dashboard',
    route: { name:'iqab-dashboard' },
    icon:'mdi:view-dashboard',
    color:'bg-[theme(colors.emerald.700)]',
    title:'Dashboard',
    type:'menu',
  },
  {
    index:'data',
    icon:'fluent:clipboard-data-bar-24-filled',
    color:'bg-[theme(colors.emerald.700)]',
    title:'Pengolahan Data',
    type:'submenu',
    children: [
      {
        index:'santri-list',
        route: { name:'iqab-data', params: { type: 'santri' } },
        title:'Data Santri',
      },
      {
        index:'pelanggaran-list',
        route: { name:'iqab-data', params: { type: 'pelanggaran' } },
        title:'Jenis Pelanggaran',
      },
      {
        index:'iqab-list',
        route: { name:'iqab-data', params: { type: 'iqab' } },
        title:'Daftar Iqab',
      },
      // {
      //   index:'pelanggaran-iqab-list',
      //   route: { name:'iqab-data', params: { type: 'pelanggaran_iqab' } },
      //   title:'Pelanggaran & Iqab',
      // },
    ]
  },
  {
    index: 'admin-iqab',
    route: { name:'admin-iqab' },
    icon:'ph:notebook-fill',
    color:'bg-[theme(colors.emerald.700)]',
    title:'Pelanggaran Santri',
    type:'menu',
  },
  {
    index: 'rekap-iqab',
    route: { name:'rekap-iqab' },
    icon:'ic:baseline-data-thresholding',
    color:'bg-[theme(colors.emerald.700)]',
    title:'Rekapitulasi Pelanggaran',
    type:'menu',
  },
]

let library = [
  {
    index: 'dashboard',
    route: { name:'library-dashboard' },
    icon:'mdi:view-dashboard',
    color:'bg-[theme(colors.emerald.700)]',
    title:'Dashboard',
    type:'menu',
  },
  {
    index:'book',
    icon:'mdi:book',
    color:'bg-[theme(colors.emerald.700)]',
    title:'Kelola Data',
    type:'menu',
    route: { name:'book-list'},
  },
  {
    index:'data',
    icon:'fluent:clipboard-data-bar-24-filled',
    color:'bg-[theme(colors.emerald.700)]',
    title:'Pengolahan Data',
    type:'submenu',
    children: [
      {
        index:'jenis-buku-list',
        route: { name:'library-data', params: { type: 'jenis' } },
        title:'Data Jenis Buku',
      },
    ]
  }
]


let base = [
  {
    index: 'default',
    route: { name:'default' },
    icon:'mdi:view-dashboard',
    color:'bg-[theme(colors.emerald.700)]',
    title:'Dashboard',
    type:'menu',
  },
]

let saving = [
  {
    index:'data',
    icon:'fluent:clipboard-data-bar-24-filled',
    color:'bg-[theme(colors.emerald.700)]',
    title:'Pengolahan Data',
    type:'submenu',
    children: [
      {
        index:'santri-list',
        route: { name:'saving-data', params: { type: 'santri' } },
        title:'Data Santri',
      },
    ]
  },
  {
    index: 'admin-saving',
    route: { name:'admin-saving' },
    icon:'ph:notebook-fill',
    color:'bg-[theme(colors.emerald.700)]',
    title:'Tabungan Santri',
    type:'menu',
  },
  {
    index: 'rekap-saving',
    route: { name:'rekap-saving' },
    icon:'ic:baseline-data-thresholding',
    color:'bg-[theme(colors.emerald.700)]',
    title:'Rekapitulasi Keuangan',
    type:'menu',
  },
]

let topMenu = [
  {
    label:'Data Kepengurusan',
    route:'management-dashboard',
    icon:'simple-line-icons:organization',
    color:'bg-[theme(colors.emerald.700)]',
    image:'https://img.freepik.com/free-photo/hand-with-marker-drawing-social-network-map_1232-205.jpg?t=st=1732515956~exp=1732519556~hmac=6345df7f352240fb0aafccf4f5995b04070ac74f3c5ca66adfb8d8fd71a3832b&w=996',
  },
  {
    label:'Data PSB',
    route:'admin-management',
    icon:'material-symbols:school',
    color:'bg-[theme(colors.emerald.700)]',
    image:'https://i.ytimg.com/vi/mf2xJ22ePok/hq720.jpg?sqp=-oaymwEhCK4FEIIDSFryq4qpAxMIARUAAAAAGAElAADIQj0AgKJD&rs=AOn4CLDTApGdiQbR2RlDjcwKWOInJmkVsA',
  },
  {
    label:'Iqab Santri',
    route:'admin-iqab',
    icon:'healthicons:justice-24px',
    color:'bg-[theme(colors.emerald.700)]',
    image:'https://img.freepik.com/premium-photo/judge-gavel-scales-justice-court-hall-law-concept-judiciary-jurisprudence-justice-copy-space-based-generative-ai_438099-11686.jpg',
  },
  {
    label:'Perpustakaan Digital',
    route:'book-list',
    icon:'ion:library',
    color:'bg-[theme(colors.emerald.700)]',
    image:'https://media.npr.org/assets/img/2023/12/29/gettyimages-925364372-edit_custom-15f489a3ffaa6163f026535ac4705763d4ccb977.jpg?s=1100&c=85&f=webp',
  },
  {
    label:'User Management',
    route:'users',
    icon:'mdi:users-group',
    color:'bg-[theme(colors.emerald.700)]',
    image:'https://zahiraccounting.com/en-my/wp-content/uploads/2015/10/zahir-accounting-software-have-more-than-60.000-users.png',
  },
]

export { iqab }
export { library }
export { saving }
export { base }
export { topMenu }
export default {
  iqab,
  library,
  saving,
  base
}