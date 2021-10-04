import Vue from 'vue'
import Vuetify from 'vuetify'
import VuetifyToast from 'vuetify-toast-snackbar'
import es from 'vuetify/src/locale/es.ts'

Vue.use(Vuetify)

const vuetify = new Vuetify({
  lang: {
    locales: { es },
    current: 'es',
  },
  icons: {
    iconfont: 'mdiSvg',
  },
  theme: {
    dark: false,
    options: {
      customProperties: true,
    },
    themes: {
      light: {
        primary: '#2f3639',
        secondary: '#4c585d',
        tertiary: '#eae4e1',
        accent: '#8c9eff',
        error: '#b71c1c',
        background: '#151b1e',
      },
    },
  },
})

Vue.use(VuetifyToast, {
  $vuetify: vuetify.framework,
  timeout: 5000,
  x: 'center',
  y: 'top',
})

export default vuetify