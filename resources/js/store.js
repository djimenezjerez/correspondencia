import Vue from 'vue'
import Vuex from 'vuex'
import VuexPersistence from 'vuex-persist'

Vue.use(Vuex)

const vuexLocal = new VuexPersistence({
  key: 'boilerplate',
  storage: window.localStorage,
})

export default new Vuex.Store({
  state: {
    loggedIn: false,
    acccessToken: '',
    tokenType: '',
    user: {},
    role: {},
  },
  getters: {
    isLoggedIn(state) {
      return state.loggedIn
    },
    token(state) {
      return {
        value: state.acccessToken,
        type: state.tokenType
      }
    },
    user(state) {
      return state.user
    },
    role(state) {
      return state.user
    },
  },
  mutations: {
    login(state, data) {
      Vue.prototype.$http.defaults.headers.common['Authorization'] = `${data.token_type} ${data.access_token}`
      state.acccessToken = data.access_token
      state.tokenType = data.token_type
      state.user = data.user
      state.role = data.role
      state.loggedIn = true
    },
    logout(state) {
      state.acccessToken = ''
      state.tokenType = ''
      state.user = {}
      state.role = {}
      state.loggedIn = false
    },
  },
  actions: {
    login({commit}, data) {
      return new Promise(async (resolve, reject) => {
        try {
          let response = await axios.post('login', data)
          commit('login', response.data.payload)
          resolve(response)
        } catch(error) {
          commit('logout')
          reject(error)
        }
      })
    },
    logout({commit}) {
      return new Promise(async (resolve, reject) => {
        try {
          let response = await axios.post('logout')
          commit('logout')
          resolve(response)
        } catch(error) {
          commit('logout')
          reject(error)
        }
      })
    },
  },
  plugins: [vuexLocal.plugin],
})