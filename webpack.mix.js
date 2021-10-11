const mix = require('laravel-mix')
require('vuetifyjs-mix-extension')

mix
.webpackConfig({
  stats: {
    children: true,
  },
  resolve: {
    alias: {
      "@": __dirname + '/resources/js'
    },
  },
})
.js('resources/js/app.js', 'public/js')
.sass('resources/sass/app.scss', 'public/css')
.copy('resources/fonts', 'public/fonts')
.copy('resources/img', 'public/img')
.copy('resources/audio', 'public/audio')
.copy('resources/assets', 'public/assets')
.vuetify(
  'vuetify-loader',
  'resources/sass/variables.scss',
  {
    progressiveImages: true
  }
)
.vue()
.sourceMaps()