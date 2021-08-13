require('@/bootstrap')
require('@/validator')
require('@/axios')
window.Vue = require('vue').default

import vuetify from '@/vuetify'
import router from '@/router'
import store from '@/store'

const app = new Vue({
  store,
  router,
  vuetify,
  el: '#app',
})
