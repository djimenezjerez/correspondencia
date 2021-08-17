<template>
  <v-app id="welcome">
    <v-container fill-height>
      <v-row
        justify="space-around"
        align="end"
      >
        <v-col
          cols="auto"
          md="4"
          sm="12"
          class="text-center pb-sm-0 pb-md-10"
        >
          <v-container>
            <v-row
              align="start"
              class="mb-sm-0 mb-md-4 mb-lg-12"
            >
              <v-col cols="12">
                <div class="white--text">
                  <div class="font-weight-light mb-sm-0 mb-md-1 mb-lg-4 text-sm-subtitle-1 text-md-h6 text-lg-h5">
                    Bienvenido al
                  </div>
                  <div class="font-weight-normal text-sm-h6 text-md-h5 text-lg-h4">
                    Sistema de Correspondencia
                  </div>
                </div>
              </v-col>
            </v-row>
            <v-row
              align="end"
            >
              <v-col cols="12">
                <div>
                  <v-btn
                    outlined
                    large
                    @click.stop="$refs.dialogLogin.showDialog()"
                    dark
                  >
                    Ingresar
                  </v-btn>
                </div>
              </v-col>
            </v-row>
          </v-container>
        </v-col>
        <v-col
          cols="auto"
          md="4"
          sm="12"
        >
          <v-row
            justify="center"
            no-gutters
          >
            <v-col
              cols="auto"
            >
              <v-img
                contain
                :max-width="imageWidth"
                src="/img/logo.png"
              ></v-img>
            </v-col>
            <v-col cols="12">
              <div class="text-center text-sm-caption text-md-caption text-lg-h6 font-weight-normal white--text" :class="logoTextFont">
                <div>
                  Asociacion Nacional de Suboficiales y Sargentos de las Fuerzas Armadas del Estado
                </div>
                <div>
                  &quot;ASCINALSS&quot;
                </div>
              </div>
            </v-col>
          </v-row>
        </v-col>
        <v-col
          cols="auto"
          md="4"
          sm="12"
          class="text-center pb-sm-0 pb-md-10"
        >
          <v-container>
            <v-row>
              <v-col cols="12">
                <div class="text-center">
                  <v-btn
                    outlined
                    large
                    dark
                  >
                    Consultar hoja de ruta
                  </v-btn>
                </div>
              </v-col>
            </v-row>
          </v-container>
        </v-col>
      </v-row>
    </v-container>
    <Login ref="dialogLogin"/>
  </v-app>
</template>

<script>
import Login from '@/components/auth/Login'

export default {
  name: 'Welcome',
  components: {
    Login,
  },
  data: function() {
    return {
      dialogLogin: false,
    }
  },
  computed: {
    imageWidth() {
      switch (this.$vuetify.breakpoint.name) {
        case 'xs': return '180px'
        case 'sm': return '200px'
        case 'md': return '250px'
        default: return '350'
      }
    },
    logoTextFont() {
      switch (this.$vuetify.breakpoint.name) {
        case 'xs': return 'font-weight-light'
        case 'sm': return 'font-weight-light'
        default: return 'font-weight-normal'
      }
    },
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
            name: 'welcome'
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
  background-color: var(--v-background-base);
}
</style>