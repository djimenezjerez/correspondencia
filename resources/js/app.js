require('@/bootstrap')
window.Vue = require('vue').default

import vuetify from '@/vuetify'
import router from '@/router'
import store from '@/store'
import '@/helpers'
import '@/mqtt'

import ToolBarTitle from '@/components/shared/ToolBarTitle'

Vue.component('ToolBarTitle', ToolBarTitle)

export const bus = new Vue();
new Vue({
  store,
  router,
  vuetify,
  el: '#app',
})
