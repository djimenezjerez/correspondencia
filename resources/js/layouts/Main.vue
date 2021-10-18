<template>
  <v-app>
    <v-app-bar
      app
      dark
      :dense="$vuetify.breakpoint.xs"
      color="background"
      v-if="$store.getters.isLoggedIn"
    >
      <v-app-bar-nav-icon
        @click="drawer = !drawer"
      ></v-app-bar-nav-icon>
      <v-divider class="ml-1 mr-5" vertical></v-divider>
      <v-btn
        outlined
        :large="$vuetify.breakpoint.xl || $vuetify.breakpoint.lg"
        :small="$vuetify.breakpoint.sm || $vuetify.breakpoint.xs"
        dark
        :to="{ name: $store.getters.user.isAdmin ? 'users' : 'procedures' }"
      >
        <v-icon
          class="mr-3"
        >
          mdi-home
        </v-icon>
        Inicio
      </v-btn>
      <v-spacer></v-spacer>
      <div class="mr-10 mt-2">
        <NotificationBadge/>
      </div>
      <v-menu
        bottom
        offset-y
        light
      >
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            :large="$vuetify.breakpoint.xl || $vuetify.breakpoint.lg"
            :small="$vuetify.breakpoint.sm || $vuetify.breakpoint.xs"
            icon
            v-bind="attrs"
            v-on="on"
            class="mr-1"
          >
            <v-avatar color="secondary" :size="avatarSize">
              <span class="text-xl-h5 text-lg-h6 text-md-subtitle-1 text-sm-subtitle-2 text-xs-body-1">{{ $store.getters.user.name[0] + $store.getters.user.last_name[0] }}</span>
            </v-avatar>
          </v-btn>
        </template>
        <v-list>
          <v-list-item @click="() => $router.push({ name: 'profile' })">
            <v-list-item-avatar class="mr-0">
              <v-icon color="info">
                mdi-account
              </v-icon>
            </v-list-item-avatar>
            <v-list-item-title class="mr-3">PERFIL</v-list-item-title>
          </v-list-item>
          <v-list-item
            @click="logout"
          >
            <v-list-item-avatar class="mr-0">
              <v-icon color="error">
                mdi-power
              </v-icon>
            </v-list-item-avatar>
            <v-list-item-title class="mr-3">CERRAR SESIÓN</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
    </v-app-bar>
    <!-- Barra lateral de Menu Aplicación -->
    <v-navigation-drawer
      app
      dark
      permanent
      :mini-variant.sync="drawer"
      color="secondary"
      :width="barSize"
    >
      <v-row align="center" justify="center" class="my-3" dense>
        <v-col cols="auto" class="text-center">
          <v-img
            src="/img/logo_low.png"
            contain
            width="120"
          ></v-img>
          <div class="white--text font-weight-bold" style="font-size: 16px;" v-if="!drawer">
            ASCINALSS
          </div>
        </v-col>
      </v-row>
      <v-divider></v-divider>
      <v-list
        nav
        dense
      >
        <v-list-item link :to="{ name: 'users' }" v-if="$store.getters.user.isAdmin">
          <v-list-item-icon>
            <v-icon>mdi-account</v-icon>
          </v-list-item-icon>
          <v-list-item-title>Usuarios</v-list-item-title>
        </v-list-item>
        <v-list-item link :to="{ name: 'procedure_types' }" v-if="$store.getters.user.isAdmin">
          <v-list-item-icon>
            <v-icon>mdi-paperclip</v-icon>
          </v-list-item-icon>
          <v-list-item-title>Trámites</v-list-item-title>
        </v-list-item>
        <v-list-item link :to="{ name: 'requirements' }" v-if="$store.getters.user.isAdmin">
          <v-list-item-icon>
            <v-icon>mdi-card-account-details</v-icon>
          </v-list-item-icon>
          <v-list-item-title>Requisitos</v-list-item-title>
        </v-list-item>
        <v-list-item link :to="{ name: 'procedures' }" v-if="!$store.getters.user.isAdmin">
          <v-list-item-icon>
            <v-icon>mdi-paperclip</v-icon>
          </v-list-item-icon>
          <v-list-item-title>Hojas de ruta</v-list-item-title>
        </v-list-item>
        <v-list-item link :to="{ name: 'procedure_requirements' }" v-if="$store.getters.user.permissions.includes('LEER REQUISITOS DE TRÁMITE')">
          <v-list-item-icon>
            <v-icon>mdi-file-document-multiple-outline</v-icon>
          </v-list-item-icon>
          <v-list-item-title>Regularización de Documentos</v-list-item-title>
        </v-list-item>
        <v-list-item link :to="{ name: 'tracking' }" v-if="!$store.getters.user.isAdmin">
          <v-list-item-icon>
            <v-icon>mdi-chart-timeline-variant</v-icon>
          </v-list-item-icon>
          <v-list-item-title>Seguimiento</v-list-item-title>
        </v-list-item>
        <v-list-item link :to="{ name: 'reports' }">
          <v-list-item-icon>
            <v-icon>mdi-chart-bar</v-icon>
          </v-list-item-icon>
          <v-list-item-title>Reportes</v-list-item-title>
        </v-list-item>

        <v-list-group color="white">
          <template v-slot:activator>
            <v-list-item-icon>
              <v-icon>mdi-help-circle-outline</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Ayuda</v-list-item-title>
          </template>
          <v-list-item :to="{ name: 'help_manual' }" :class="{ 'ml-6': !drawer }">
            <v-list-item-icon>
              <v-icon>mdi-information</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Manual de usuario</v-list-item-title>
          </v-list-item>
        </v-list-group>
      </v-list>
    </v-navigation-drawer>
    <v-main>
      <v-container fluid>
        <router-view></router-view>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
import mqtt from 'mqtt'
import moment from 'moment'
import { bus } from '@/app'
import NotificationBadge from '@/components/shared/NotificationBadge'

export default {
  name: 'Login',
  components: {
    NotificationBadge
  },
  data: function() {
    return {
      drawer: false,
      mqtt: {
        connected: false,
        unsubscribe: () => {},
        end: () => {},
      },
    }
  },
  computed: {
    topic() {
      return `procedures/tray/area/${this.$store.getters.user.area_id}`
    },
    avatarSize() {
      switch (this.$vuetify.breakpoint.name) {
        case 'xs': return 40
        case 'sm': return 40
        default: return 45
      }
    },
    barSize() {
      switch (this.$vuetify.breakpoint.name) {
        case 'xs': return 250
        case 'sm': return 255
        case 'md': return 260
        case 'lg': return 255
        case 'xl': return 250
        default: return 260
      }
    },
  },
  mounted() {
    this.mqttConnect()
  },
  beforeDestroy() {
    this.mqtt.unsubscribe(this.topic)
    this.mqtt.end()
  },
  methods: {
    mqttConnect() {
      if (!this.mqtt.connected) {
        this.mqtt = mqtt.connect(`${JSON.parse(process.env.MIX_MQTT_SSL) ? 'wss' : 'ws'}://${process.env.MIX_MQTT_HOST}:${process.env.MIX_MQTT_PORT}`, {
          clientId: `web.${moment().unix()}`
        })
        this.mqtt.subscribe(this.topic, { qos: 1 }, (error) => {
          if (error) {
            console.log('Error en la subscripción de WebSocket', error)
            return
          }
          console.log('Suscrito a WebSocket:', this.topic)
        })
      }
      this.mqtt.on('connect' , () => {
        console.log('Conectado a servidor de WebSockets')
      })
      this.mqtt.on('reconnect' , () => {
        console.log('Reconectando con servidor de WebSockets...')
      })
      this.mqtt.on('disconnect' , () => {
        console.log('Desconectando de servidor de WebSockets...')
      })
      this.mqtt.on('end' , () => {
        console.log('Desconexión con servidor de WebSockets completa')
      })
      this.mqtt.on('error' , (error) => {
        console.error(error)
      })
      this.mqtt.on('message', function (topic, payload) {
        bus.$emit('updateProcedureNotification', Number(String.fromCharCode.apply(null, payload)))
      })
    },
    async logout() {
      try {
        await this.$store.dispatch('logout')
        this.$router.push({
          name: 'welcome'
        })
      } catch(error) {
        console.error(error)
      }
    }
  }
}
</script>

<style type="css" scoped>
.v-tooltip__content {
  font-size: 15px !important;
  /* background-color: blue; */
  /* opacity: 0.9 !important; */
}
.v-icon {
    font-size: calc(1em + 0.1vh + 0.2vw) !important;
    padding-left: 4px;
    padding-right: 0;
    margin: 0;
}
.v-list-item__icon {
    padding: 0px !important;
    margin-right: 5px !important;
    margin-left: 0px !important;
}
</style>