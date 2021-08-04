window._ = require('lodash')

window.axios = require('axios')
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
window.axios.defaults.withCredentials = true
// window.axios.defaults.baseURL = `${process.env.BASE_URL}/api/`
window.axios.defaults.headers.common['Access-Control-Allow-Origin'] = '*'
window.axios.defaults.headers.common['Accept'] = 'application/json'
window.axios.defaults.headers.common['Content-Type'] = 'application/json'

// import Echo from 'laravel-echo';
// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//   broadcaster: 'pusher',
//   key: process.env.MIX_PUSHER_APP_KEY,
//   cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//   forceTLS: false
// });