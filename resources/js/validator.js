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
      name: 'nombre',
      last_name: 'apellido',
      identity_card: 'documento de identidad',
      username: 'usuario',
      password: 'contraseña',
      old_password: 'contraseña actual',
      phone: 'teléfono',
      address: 'dirección',
      area_id: 'sección',
      document_type_id: 'tipo de documento',
      code: 'código',
      origin: 'procedencia',
      detail: 'detalle',
    }
  },
})

localize('es');

Object.keys(rules).forEach(rule => {
  extend(rule, rules[rule])
})