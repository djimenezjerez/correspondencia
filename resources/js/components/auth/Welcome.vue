<template>
  <v-app>
    <v-container fill-height fluid id="welcome">
      <v-row justify="space-around" align="center">
        <v-col sm="12" md="3" offset-md="1">
          <p class="text-center text-sm-h4 text-md-h3 font-weight-light white--text">
            Bienvenido al:
          </p>
          <p class="text-center text-sm-h3 text-md-h2 font-weight-medium white--text">
            Sistema de Correspondencia
          </p>
          <v-row justify="center" align="center">
            <v-btn
              outlined
              x-large
              class="mt-md-16"
              @click.stop="dialogLogin = true"
              dark
            >
              Ingresar
            </v-btn>
          </v-row>
        </v-col>
        <v-col sm="12" md="3" offset-md="1">
          <v-row justify="center" align="center">
            <v-col cols="12">
              <v-img src="/img/logo.png"></v-img>
            </v-col>
          </v-row>
          <v-row justify="center" align="center">
            <p class="text-center text-sm-h4 text-md-h4 font-weight-light mt-5 white--text">
              Asociacion Nacional de Sufoficiales y Sargentos de las Fuerzas Armadas del Estado
            </p>
          </v-row>
        </v-col>
        <v-col sm="12" md="4">
          <v-row justify="center" align="center">
            <v-btn
              outlined
              x-large
              class="mt-16"
              dark
            >
              Consultar hoja de ruta
            </v-btn>
          </v-row>
        </v-col>
      </v-row>
    </v-container>
    <v-dialog
      v-model="dialogLogin"
      persistent
      max-width="600"
    >
      <Login v-on:closeDialog="dialogLogin = false"/>
    </v-dialog>
    <vtoast ref="vtoast"/>
  </v-app>
</template>

<script>
import Login from '@/components/auth/Login'
import vtoast from '@/layouts/Snackbar'

export default {
  name: 'Welcome',
  components: {
    Login,
    vtoast
  },
  data: function() {
    return {
      dialogLogin: false,
    }
  },
  mounted() {
    if ('toast' in this.$route.params) {
      this.$refs.vtoast.show(this.$route.params.toast.toString())
    }
  },
  methods: {
    async login() {
      try {
        let valid = await this.$refs.loginObserver.validate()
        if (valid) {
          this.loading = true
          await axios.get('sanctum/csrf-cookie')
          await this.$store.dispatch('login', this.loginForm)
          this.loading = false
          this.$router.push({
            name: 'dashboard'
          })
        }
      } catch(error) {
        this.loginForm.password = ''
        this.$refs.loginObserver.reset()
        if ('errors' in error.response.data) {
          this.$refs.loginObserver.setErrors(error.response.data.errors)
        }
        this.loading = false
      }
    },
  },
}
</script>

<style>
#welcome {
  background-color: var(--v-primary-base);
}
</style>