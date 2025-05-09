import { createApp, ref} from 'vue'
import App from "./App.vue"
let app = createApp(App)

//Modules
import router from '@/config/routes/router'
app.use(router)

import store from '@/config/store'
app.use(store)

//Styling
import '@/config/styles/tailwind.css'
import '@/config/styles/app.scss'
import GetIcon from '@/components/Icon.vue'
import Loading from '@/components/Loading.vue'
import File from '@/components/File.vue'
app.component('icons', GetIcon)
app.component('loading', Loading)
app.component('file', File)

//Plugins
import elementPlugin from '@/config/plugins/element-ui-global'
import funcPlugin from '@/config/plugins/functions'
import numberFuncPlugin from '@/config/plugins/number-functions'
import dataFuncPlugin from '@/config/plugins/data-functions'
import dateFuncPlugin from '@/config/plugins/date-functions'
import uiFuncPlugin from '@/config/plugins/ui-functions'
import directives from '@/config/plugins/directives' // import your plugin

app.use(directives)
app.use(elementPlugin)
app.use(funcPlugin)
app.use(numberFuncPlugin)
app.use(dataFuncPlugin)
app.use(dateFuncPlugin)
app.use(uiFuncPlugin)

import jsonToFormData from 'json-form-data'
window.jsonToFormData = jsonToFormData

import JQuery from 'jquery'
window.jquery = JQuery
//Variables
import config from '@/config/url';
app.config.globalProperties.$baseUrl = config.baseUrl; 
app.config.globalProperties.$siteUrl = config.siteUrl; 
app.config.globalProperties.defaultRoute = config.defaultRoute; 

import API from '@/config/api'
app.config.globalProperties.$http = API

app.mount('#app')
