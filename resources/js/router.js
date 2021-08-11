import Vue from 'vue'
import VueRouter from 'vue-router'
import Login from '@/components/Login'
import MainLayout from '@/components/MainLayout'
import Dashboard from '@/components/Dashboard'
import Profile from '@/components/Profile'
import Users from '@/components/Users'
import store from '@/store.js'

Vue.use(VueRouter)

const router = new VueRouter({
  mode: 'history',
  routes: [
    {
      path: '*',
      redirect: {
        name: 'dashboard',
      }
    }, {
      path: '/login',
      name: 'login',
      component: Login,
    }, {
      path: '/',
      name: 'root',
      component: MainLayout,
      children: [
        {
          path: '/dashboard',
          name: 'dashboard',
          component: Dashboard,
        }, {
          path: '/profile',
          name: 'profile',
          component: Profile,
        }, {
          path: '/user',
          name: 'users',
          component: Users,
        }
      ]
    },
  ]
})

router.beforeEach((to, from, next) => {
  if (to.name != 'login') {
    if (store.getters.isLoggedIn) {
      if (to.name == 'root') {
        next({
          name: 'dashboard',
        })
      } else {
        next()
      }
    } else {
      next({
        name: 'login',
      })
    }
  } else {
    if (store.getters.isLoggedIn) {
      next({
        name: 'dashboard',
      })
    } else {
      next()
    }
  }
})

export default router