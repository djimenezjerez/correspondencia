import Vue from 'vue'
import store from '@/store.js'

Vue.prototype.$helpers = {
  userOwnsProcedure(procedureId) {
    return store.getters.user.id == procedureId
  },
  fontSize(breakpoint) {
    switch (breakpoint) {
      case 'xs': return '8px'
      case 'sm': return '8px'
      case 'md': return '11px'
      case 'lg': return '14px'
      case 'xl': return '16px'
      default: return '12px'
    }
  },
  rowHeight(breakpoint) {
    switch (breakpoint) {
      case 'xs': return '65px'
      case 'sm': return '55px'
      case 'md': return '45px'
      case 'lg': return '80px'
      case 'xl': return '90px'
      default: return '45px'
    }
  },
}
