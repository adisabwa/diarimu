
import { baseUrl } from "@/config/url";

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

let topMenu = {
  quranBaca: {
    url:'quran/baca',
    label:"Baca Qur'an",
    route:'quran-baca',
    color:'bg-lime-200',
    shadowColor:'shadow-lime-600',
    textColor:'text-orange-500',
    image:baseUrl + 'assets/images/icons/quran.png',
  },
  quranHafal: {
    url:'quran/hafal',
    label:"Hafal Qur'an",
    route:'quran-hafal',
    color:'bg-yellow-200',
    shadowColor:'shadow-yellow-600',
    textColor:'text-orange-500',
    image:baseUrl + 'assets/images/icons/memorization.png',
  },
  quranTarjamah: {
    url:'quran/tarjamah',
    label:"Tarjamahan Qur'an",
    route:'quran-tarjamah',
    color:'bg-sky-200',
    shadowColor:'shadow-sky-600',
    textColor:'text-orange-500',
    image:baseUrl + 'assets/images/icons/kajian.png',
  },
  sholatWajib: {
    url:'sholat/wajib',
    label:"Sholat Wajib",
    route:'sholat-wajib',
    color:'bg-purple-200',
    shadowColor:'shadow-purple-600',
    textColor:'text-orange-500',
    image:baseUrl + 'assets/images/icons/mosque.png',
  },
  sholatSunnah: {
    url:'sholat/sunnah',
    label:"Sholat Sunnah",
    route:'sholat-sunnah',
    color:'bg-rose-200',
    shadowColor:'shadow-rose-600',
    textColor:'text-orange-500',
    image:baseUrl + 'assets/images/icons/prayer-rug.png',
  },
  infaqShadaqah: {
    url:'infaq/shadaqah',
    label:"Infaq / Shadaqah",
    route:'shadaqah',
    color:'bg-teal-200',
    shadowColor:'shadow-teal-600',
    textColor:'text-orange-500',
    image:baseUrl + 'assets/images/icons/infaq.png',
  },
}

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