import Vue from 'vue'
import moment from 'moment'
import VueMqtt from 'vue-mqtt'

Vue.use(VueMqtt, `${JSON.parse(process.env.MIX_MQTT_SSL) ? 'wss' : 'ws'}://${process.env.MIX_MQTT_HOST}:${process.env.MIX_MQTT_PORT}`, {
  clientId: `web.${moment().unix()}`
})