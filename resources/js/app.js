require('@/bootstrap');
window.Vue = require('vue').default

import Axios from 'axios'
import vuetify from '@/vuetify'
import router from '@/router'
import store from '@/store'

Vue.prototype.$http = Axios;
Vue.prototype.$http.defaults.headers.common['Authorization'] = `${store.getters.tokenType} ${store.getters.accessToken}`

const app = new Vue({
  store,
  router,
  vuetify,
  el: '#app',
})
