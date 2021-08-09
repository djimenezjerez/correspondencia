require('@/bootstrap')
require('@/validator')
window.Vue = require('vue').default

import Axios from 'axios'
import vuetify from '@/vuetify'
import router from '@/router'
import store from '@/store'

Vue.prototype.$http = Axios
Vue.prototype.$http.defaults.withCredentials = true
Vue.prototype.$http.defaults.baseURL = `${process.env.MIX_BASE_URL}/api/`
Vue.prototype.$http.defaults.headers.common['Authorization'] = `${store.getters.token.type} ${store.getters.token.value}`
Vue.prototype.$http.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
Vue.prototype.$http.defaults.headers.common['Access-Control-Allow-Origin'] = '*'
Vue.prototype.$http.defaults.headers.common['Accept'] = 'application/json'
Vue.prototype.$http.defaults.headers.common['Content-Type'] = 'application/json'

const app = new Vue({
  store,
  router,
  vuetify,
  el: '#app',
})
