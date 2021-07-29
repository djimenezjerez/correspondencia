require('./bootstrap');
window.Vue = require('vue').default

import vuetify from './vuetify'
import router from './router'
import Login from './components/Login.vue'

const app = new Vue({
  router,
  vuetify,
  el: '#app',
  render: h => h(Login),
})
