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
      <div class="mr-10 mt-2" v-if="$route.name == 'procedures'">
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

        <v-list-group color="white">
          <v-icon slot="prependIcon" color="white">mdi-help-circle-outline</v-icon>
          <template v-slot:activator>
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
import NotificationBadge from '@/components/shared/NotificationBadge'

export default {
  name: 'Login',
  components: {
    NotificationBadge
  },
  data: function() {
    return {
      drawer: false,
    }
  },
  computed: {
    avatarSize() {
      switch (this.$vuetify.breakpoint.name) {
        case 'xs': return 40
        case 'sm': return 40
        default: return 45
      }
    },
    barSize() {
      switch (this.$vuetify.breakpoint.name) {
        case 'xs': return 240
        case 'sm': return 245
        case 'md': return 250
        case 'lg': return 245
        case 'xl': return 240
        default: return 250
      }
    }
  },
  methods: {
    async logout() {
      try {
        await this.$store.dispatch('logout')
        this.$router.push({
          name: 'welcome'
        })
      } catch(error) {
        console.log(error)
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
</style>