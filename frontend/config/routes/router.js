import { createRouter, createWebHistory , createWebHashHistory} from 'vue-router'

import store from '@/config/store'
import config from '@/config/url'

import managementRoute from './routes/management'
import iqabRoute from './routes/iqab'
import authRoute from './routes/auth'
import libraryRoute from './routes/library'
import defaultRoute from './routes/default'
import savingRoute from './routes/saving'
// Vue router
const routes = new createRouter({
  history: createWebHistory(),
  routes: [
    ...managementRoute,
    ...defaultRoute,
    ...libraryRoute,
    ...authRoute,
    ...iqabRoute,
    ...savingRoute,
	],
  scrollBehavior: function(to, from, savedPosition) {
    // console.log(savedPosition)
    if (savedPosition) {
      return savedPosition
    } else {
      if (to.name == 'main')
        return
      return { top: 0 }
    }
  },
})

routes.beforeEach(async (to, from, next) => {
  await store.dispatch('checkUser')
  const loggedUser = store.getters.loggedUser
  var title = to.meta.pageTitle
  if (title) {
    var pageTitle = title
  } else {
    var pageTitle = '<b>Layanan Penjadwalan</b>'
  }
  var pageSubTitle = to.meta.pageSubTitle

  store.dispatch('changePageTitle', pageTitle)
  store.dispatch('changePageSubTitle', pageSubTitle)

  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (loggedUser.role == '') {
      // window.alert('Silahkan login terlebih dahulu')
      next({
        name: 'login',
        query: { nextUrl: to.fullPath }
      })
    } else {
      if (to.meta.app) {
        // console.log(to.meta.app, loggedUser.app);
        if (!loggedUser.app.includes(to.meta.app)) {
          next({
            name: 'unauthorized',
          })
        } else {
          next()
        }
      } else {
        next()
      }
    }
  } else if (to.matched.some(record => record.meta.guest)) {
    next()
  } else {
    next()
  }
})

export default routes