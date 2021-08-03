require('./bootstrap');
window.Vue = require('vue').default

import vuetify from './vuetify'
import router from './router'
import store from './store'
import Login from './components/Login.vue'
import MainLayout from './components/MainLayout.vue'

const app = new Vue({
  store,
  router,
  vuetify,
  el: '#app',
})
