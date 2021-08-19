<template>
  <v-app id="welcome">
    <v-container :fill-height="!$vuetify.breakpoint.xs" fluid>
      <v-row
        justify="space-around"
        align="end"
      >
        <v-col
          cols="auto"
          sm="4"
          xs="12"
          class="text-center pb-xs-0 pb-sm-10"
        >
          <v-container>
            <v-row
              align="start"
              class="mb-xs-0 mb-sm-6 mb-md-4 mb-lg-14 mb-xl-16"
            >
              <v-col cols="12">
                <div class="white--text">
                  <div class="font-weight-light mb-xs-0 mb-sm-0 mb-md-1 text-xs-caption text-sm-subtitle-2 text-xl-h4 text-md-h6">
                    Bienvenido al
                  </div>
                  <div class="font-weight-normal text-xs-body-2 text-sm-subtitle-1 text-xl-h3 text-md-h5">
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
                    @click.stop="$refs.dialogLogin.showDialog()"
                    dark
                    :x-large="$vuetify.breakpoint.lg || $vuetify.breakpoint.xl"
                    :large="$vuetify.breakpoint.md"
                    :small="$vuetify.breakpoint.sm"
                    :x-small="$vuetify.breakpoint.xs"
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
          sm="4"
          xs="12"
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
              <div class="text-center text-md-caption text-lg-body-1 text-xl-h4 font-weight-normal white--text" :class="logoTextFont" :style="($vuetify.breakpoint.sm || $vuetify.breakpoint.xs) && 'font-size: 10px'">
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
          sm="4"
          xs="12"
          class="text-center pb-xs-0 pb-sm-10"
        >
          <v-container>
            <v-row
              align="start"
            >
              <v-col cols="12">
                <v-btn
                  outlined
                  dark
                  :x-large="$vuetify.breakpoint.lg || $vuetify.breakpoint.xl"
                  :large="$vuetify.breakpoint.md"
                  :small="$vuetify.breakpoint.sm"
                  :x-small="$vuetify.breakpoint.xs"
                >
                  Consultar hoja de ruta
                </v-btn>
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
        case 'xs': return '120px'
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

<style scoped>
#welcome {
  background-color: var(--v-background-base);
}
</style>