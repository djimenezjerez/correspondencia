import Vue from 'vue'
import Vuetify from 'vuetify'
import es from 'vuetify/src/locale/es.ts'

Vue.use(Vuetify)

export default new Vuetify({
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
        primary: '#005349',
        secondary: '#696969',
        tertiary: '#37474F',
        accent: '#8c9eff',
        error: '#b71c1c',
      },
    },
  },
})