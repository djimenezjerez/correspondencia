import Vue from 'vue'
import VueRouter from 'vue-router'
import Welcome from '@/components/auth/Welcome'
import MainLayout from '@/layouts/Main'
import Dashboard from '@/components/Dashboard'
import Profile from '@/components/auth/Profile'
import UsersList from '@/components/users/UsersList'
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
      path: '/welcome',
      name: 'welcome',
      component: Welcome,
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
          component: UsersList,
        }
      ]
    },
  ]
})

router.beforeEach((to, from, next) => {
  if (to.name != 'welcome') {
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
        name: 'welcome',
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