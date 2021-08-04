const mix = require('laravel-mix')

mix
.webpackConfig({
  resolve: {
    alias: {
      "@": __dirname + '/resources/js'
    },
  },
})
.js('resources/js/app.js', 'public/js')
.vue()
.sass('resources/sass/app.scss', 'public/css')
.copy('resources/fonts', 'public/fonts')
