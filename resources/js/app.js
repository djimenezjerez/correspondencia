window.Vue = require('vue').default

import router from './router'
import Login from './components/Login.vue'

const app = new Vue({
  router,
  el: '#app',
  render: h => h(Login),
})
