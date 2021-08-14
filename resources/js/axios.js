import Axios from 'axios'
import Vue from 'vue'
import router from '@/router'
import store from '@/store'

Vue.prototype.$http = Axios
Vue.prototype.$http.defaults.withCredentials = true
Vue.prototype.$http.defaults.baseURL = `${process.env.MIX_BASE_URL}/api/`
Vue.prototype.$http.defaults.headers.common['Authorization'] = `${store.getters.token.type} ${store.getters.token.value}`
Vue.prototype.$http.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
Vue.prototype.$http.defaults.headers.common['Access-Control-Allow-Origin'] = '*'
Vue.prototype.$http.defaults.headers.common['Accept'] = 'application/json'
Vue.prototype.$http.defaults.headers.common['Content-Type'] = 'application/json'

const responseSuccessHandler = response => {
  return response
}

const responseErrorHandler = async error => {
  try {
    if (error.response.status === 403) {
      if (router.currentRoute.name != 'welcome') {
        await store.dispatch('logout')
        router.push({
          name: 'welcome'
        })
      }
    }
  } catch(e) {
    console.log(e)
  } finally {
    return Promise.reject(error)
  }
}

Vue.prototype.$http.interceptors.response.use(
  response => responseSuccessHandler(response),
  error => responseErrorHandler(error),
)