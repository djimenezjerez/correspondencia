require('@/bootstrap')
window.Vue = require('vue').default

import vuetify from '@/vuetify'
import router from '@/router'
import store from '@/store'

import ToolBarTitle from '@/components/shared/ToolBarTitle'

Vue.component('ToolBarTitle', ToolBarTitle)

const app = new Vue({
  store,
  router,
  vuetify,
  el: '#app',
})
