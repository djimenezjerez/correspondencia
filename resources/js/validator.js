import Vue from 'vue'
import { localize, extend, ValidationObserver, ValidationProvider } from 'vee-validate'
import * as rules from 'vee-validate/dist/rules';
import en from 'vee-validate/dist/locale/en.json';
import es from 'vee-validate/dist/locale/es.json';

Vue.component('validation-observer', ValidationObserver)
Vue.component('validation-provider', ValidationProvider)

localize({
  en,
  es,
})

localize({
  es: {
    names: {
      username: 'usuario',
      password: 'contraseña',
    }
  },
})

localize('en');

Object.keys(rules).forEach(rule => {
  extend(rule, rules[rule])
})