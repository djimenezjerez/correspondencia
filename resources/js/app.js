require('@/bootstrap');
window.Vue = require('vue').default

import Axios from 'axios'
import vuetify from '@/vuetify'
import router from '@/router'
import store from '@/store'

Vue.prototype.$http = Axios;
const token = localStorage.getItem('token')
if (token) {
  Vue.prototype.$http.defaults.headers.common['Authorization'] = `Bearer ${token}`
}

const app = new Vue({
  store,
  router,
  vuetify,
  el: '#app',
})
