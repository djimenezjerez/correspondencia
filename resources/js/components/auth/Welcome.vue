<template>
  <v-app id="welcome">
    <v-container fill-height fluid class="pa-0">
      <v-row
        align="center"
        justify="space-between"
        no-gutters
      >
        <v-col
          xl="7"
          lg="7"
          md="7"
          sm="7"
          xs="12"
          class="text-center white--text"
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
                :width="imageWidth"
                src="/img/logo_low.png"
              ></v-img>
            </v-col>
          </v-row>
          <div class="text-md-caption text-lg-body-1 text-xl-h5 font-weight-normal py-4" :class="logoTextFont" :style="($vuetify.breakpoint.sm || $vuetify.breakpoint.xs) && 'font-size: 10px'">
            <div>
              Asociacion Nacional de Suboficiales y Sargentos de las Fuerzas Armadas del Estado
            </div>
            <div>
              &quot;ASCINALSS&quot;
            </div>
          </div>
          <div class="font-weight-normal text-xs-body-2 text-sm-subtitle-1 text-md-h6 text-lg-h5 text-xl-h4 py-8">
            GESTIÓN DOCUMENTAL Y CONTROL DE CORRESPONDENCIA
          </div>
          <div class="py-5">
            <v-btn
              @click.stop="$refs.dialogLogin.showDialog()"
              dark
              :x-large="$vuetify.breakpoint.lg || $vuetify.breakpoint.xl"
              :large="$vuetify.breakpoint.md"
              :small="$vuetify.breakpoint.sm"
              :x-small="$vuetify.breakpoint.xs"
              color="info"
            >
              Iniciar sesión
            </v-btn>
          </div>
          <div class="py-5">
            <v-btn
              dark
              :x-large="$vuetify.breakpoint.lg || $vuetify.breakpoint.xl"
              :large="$vuetify.breakpoint.md"
              :small="$vuetify.breakpoint.sm"
              :x-small="$vuetify.breakpoint.xs"
              @click.stop="$refs.dialogSearchCode.showDialog()"
              color="info"
            >
              Consultar trámite
            </v-btn>
          </div>
        </v-col>
        <v-col
          cols="5"
          v-if="!$vuetify.breakpoint.xs"
        >
          <v-img
            height="100vh"
            max-width="1600"
            src="/img/welcome_background.jpg"
          ></v-img>
        </v-col>
      </v-row>
    </v-container>
    <Login ref="dialogLogin"/>
    <SearchCode ref="dialogSearchCode"/>
  </v-app>
</template>

<script>
import Login from '@/components/auth/Login'
import SearchCode from '@/components/auth/SearchCode'

export default {
  name: 'Welcome',
  components: {
    Login,
    SearchCode,
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
    marginTopColumn1() {
      switch (this.$vuetify.breakpoint.name) {
        case 'xs': return '0px'
        case 'sm': return '60px'
        case 'md': return '125px'
        case 'lg': return '125px'
        case 'xl': return '125px'
        default: return '0px'
      }
    },
    marginTopColumn2() {
      switch (this.$vuetify.breakpoint.name) {
        case 'xs': return '0px'
        case 'sm': return '-35px'
        case 'md': return '-25px'
        case 'lg': return '-100px'
        case 'xl': return '-100px'
        default: return '0px'
      }
    },
    marginTopColumn3() {
      switch (this.$vuetify.breakpoint.name) {
        case 'xs': return '0px'
        case 'sm': return '184px'
        case 'md': return '233px'
        case 'lg': return '273px'
        case 'xl': return '300px'
        default: return '0px'
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