import Vue from 'vue'
import VueRouter from 'vue-router'
import Login from './components/Login'

Vue.use(VueRouter)

export default new VueRouter({
  mode: 'history',
  routes: [
    {
      path: '/',
      name: 'login',
      component: Login
    },
  ]
})