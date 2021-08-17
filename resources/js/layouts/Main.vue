<template>
  <v-app>
    <v-app-bar
      app
      dark
      color="background"
      v-if="$store.getters.isLoggedIn"
    >
      <v-app-bar-nav-icon
        @click="drawer = true"
        v-if="$store.getters.user.isAdmin"
      ></v-app-bar-nav-icon>
      <v-toolbar-title>
        <v-img
          src="/img/logo.png"
          contain
          width="80"
        ></v-img>
      </v-toolbar-title>
      <v-divider class="mx-10" vertical></v-divider>
      <v-btn
        outlined
        x-large
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
      <v-menu
        bottom
        offset-y
        light
      >
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            icon
            v-bind="attrs"
            v-on="on"
            class="mr-1"
          >
            <v-avatar color="secondary">
              <span class="text-h5">{{ $store.getters.user.name.split(' ').slice(0,2).map(i => i[0]).join('') }}</span>
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
      v-model="drawer"
      absolute
      temporary
    >
      <v-list
        nav
        dense
      >
        <v-list-item-group v-model="group">
          <v-list-item link :to="{ name: 'users' }">
            <v-list-item-icon>
              <v-icon>mdi-account</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Usuarios</v-list-item-title>
          </v-list-item>
          <v-list-item link :to="{ name: 'requirements' }">
            <v-list-item-icon>
              <v-icon>mdi-card-account-details</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Requisitos</v-list-item-title>
          </v-list-item>
          <v-list-item link :to="{ name: 'procedure_types' }">
            <v-list-item-icon>
              <v-icon>mdi-paperclip</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Trámites</v-list-item-title>
          </v-list-item>
        </v-list-item-group>
      </v-list>
    </v-navigation-drawer>
    <v-main>
      <router-view></router-view>
    </v-main>
  </v-app>
</template>

<script>
export default {
  name: 'Login',
  data: function() {
    return {
      drawer: false,
      group: null,
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