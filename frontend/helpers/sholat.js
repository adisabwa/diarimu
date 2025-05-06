
export function setStatusColor(number){
    if (number === '' || number === null || number === undefined)
      return '[&_*]:bg-slate-50 [&_*]:text-slate-400 bg-slate-50 text-slate-400'
    else if (number == 100)
      return '[&_*]:bg-green-100 [&_*]:text-green-900 bg-green-100 text-green-900'
    else if (number >= 75)
      return '[&_*]:bg-sky-100 [&_*]:text-sky-900 bg-sky-100 text-sky-900'
    else if (number >= 50)
      return '[&_*]:bg-purple-100 [&_*]:text-purple-900 bg-purple-100  text-purple-900'
    else if (number >= 25)
      return '[&_*]:bg-orange-100 [&_*]:text-orange-900 bg-orange-100 text-orange-900'
    else if (number >= 0)
      return '[&_*]:bg-red-500 [&_*]:text-white bg-red-500 text-white'
    else
      return '[&_*]:bg-slate-200 bg-slate-200'
  }
  
let options  = [
  {
    value:100,
    label:`Jama'ah`,
  },
  {
    value:75,
    label:`Masbuq`,
  },
  {
    value:50,
    label:`Sendiri`,
  },
  {
    value:25,
    label:`Terlambat`,
  },
  {
    value:0,
    label:`Tidak Sholat`,
  },
]

export { options };

export function getLabel(value){
  let op = options.find( o => o.value == value )
  if (op)
    return op.label
  else
    return '-';
}
export function setStatusText(status){
    if (status == '0') return 'Terdaftar'
    else if (status == '1') return 'Sudah Dibayar'
    else if (status == '2') return 'Terverifikasi'
    else if (status == '-1') return 'Koreksi Data'
    else return ''
  }