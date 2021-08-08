<template>
  <v-app>
    <v-app-bar
      app
      dark
      color="info"
      v-if="'name' in $store.getters.user"
    >
      <v-app-bar-nav-icon @click="drawer = true"></v-app-bar-nav-icon>
      <v-toolbar-title>Title</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-menu
        bottom
        offset-y
      >
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            icon
            v-bind="attrs"
            v-on="on"
            class="mr-1"
          >
            <v-avatar color="primary">
              <span class="white--text text-h5">{{ $store.getters.user.name.split(' ').slice(0,2).map(i => i[0]).join('') }}</span>
            </v-avatar>
          </v-btn>
        </template>
        <v-list>
          <v-list-item
            @click="() => {}"
          >
            <v-list-item-avatar class="mr-0">
              <v-icon color="info">
                mdi-account
              </v-icon>
            </v-list-item-avatar>
            <v-list-item-title class="mr-3">Profile</v-list-item-title>
          </v-list-item>
          <v-list-item
            @click="logout"
          >
            <v-list-item-avatar class="mr-0">
              <v-icon color="error">
                mdi-power
              </v-icon>
            </v-list-item-avatar>
            <v-list-item-title class="mr-3">Logout</v-list-item-title>
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
          <v-list-item link :to="{ name: 'dashboard' }">
            <v-list-item-icon>
              <v-icon>mdi-home</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Home</v-list-item-title>
          </v-list-item>

          <v-list-item link :to="{ name: 'login' }">
            <v-list-item-icon>
              <v-icon>mdi-account</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Account</v-list-item-title>
          </v-list-item>
        </v-list-item-group>
      </v-list>
    </v-navigation-drawer>

    <v-main>
      <v-container>
        <router-view></router-view>
      </v-container>
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
          name: 'login'
        })
      } catch(error) {
        console.log(error)
      }
    }
  }
}
</script>