import Vue from 'vue'
import VueRouter from 'vue-router'
import Welcome from '@/components/auth/Welcome'
import MainLayout from '@/layouts/Main'
import RequirementsList from '@/components/requirements/RequirementsList'
import ProcedureTypesList from '@/components/procedure_types/ProcedureTypesList'
import ProceduresList from '@/components/procedures/ProceduresList'
import ProcedureRequirementList from '@/components/procedures/ProcedureRequirementList'
import ProcedureTracking from '@/components/procedures/ProcedureTracking'
import Report from '@/components/reports/Report'
import Profile from '@/components/auth/Profile'
import UsersList from '@/components/users/UsersList'
import HelpManual from '@/components/help/Manual'
import store from '@/store.js'

Vue.use(VueRouter)

const router = new VueRouter({
  mode: 'history',
  routes: [
    {
      path: '*',
      redirect: {
        name: 'root',
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
          path: '/requirements',
          name: 'requirements',
          component: RequirementsList,
        }, {
          path: '/procedure_types',
          name: 'procedure_types',
          component: ProcedureTypesList,
        }, {
          path: '/procedures',
          name: 'procedures',
          component: ProceduresList,
        }, {
          path: '/profile',
          name: 'profile',
          component: Profile,
        }, {
          path: '/user',
          name: 'users',
          component: UsersList,
        }, {
          path: '/help_manual',
          name: 'help_manual',
          component: HelpManual,
        }, {
          path: '/procedure_requirements',
          name: 'procedure_requirements',
          component: ProcedureRequirementList,
        }, {
          path: '/tracking',
          name: 'tracking',
          component: ProcedureTracking,
        }, {
          path: '/reports',
          name: 'reports',
          component: Report,
        }
      ]
    },
  ]
})

router.beforeEach((to, from, next) => {
  if (to.name != 'welcome') {
    if (store.getters.isLoggedIn) {
      if (to.name == 'root') {
        if(store.getters.user.isAdmin) {
          next({
            name: 'users',
          })
        } else {
          next({
            name: 'procedures',
          })
        }
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
        name: 'root',
      })
    } else {
      next()
    }
  }
})

export default router