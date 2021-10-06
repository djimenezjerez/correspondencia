<template>
  <v-dialog
    v-model="dialog"
    persistent
    max-width="600"
    @keydown.esc="dialog = false"
  >
    <v-card
      :loading="loading"
    >
      <template slot="progress">
        <v-progress-linear
          color="secondary"
          height="10"
          indeterminate
        ></v-progress-linear>
      </template>
      <v-toolbar dense dark color="secondary">
        <ToolBarTitle title="Bienvenido"/>
        <v-spacer></v-spacer>
        <v-btn
          icon
          @click.stop="dialog = false"
        >
          <v-icon>
            mdi-close
          </v-icon>
        </v-btn>
      </v-toolbar>
      <div class="px-5 pb-5">
        <v-row
          justify="center"
          no-gutters
        >
          <v-col
            cols="12"
          >
            <v-img
              class="mt-4"
              contain
              :max-height="imageHeight"
              :height="imageHeight"
              src="/img/logo_low.png"
            ></v-img>
          </v-col>
          <v-col cols="12" class="pt-0">
            <validation-observer ref="loginObserver" v-slot="{ invalid }">
              <form v-on:submit.prevent="submit">
                <v-card-text>
                  <validation-provider
                    v-slot="{ errors }"
                    name="username"
                    rules="required|min:3"
                  >
                    <v-text-field
                      label="Usuario"
                      v-model="loginForm.username"
                      data-vv-name="username"
                      :error-messages="errors"
                      prepend-icon="mdi-account"
                      autofocus
                    ></v-text-field>
                  </validation-provider>
                  <validation-provider
                    v-slot="{ errors }"
                    name="password"
                    rules="required|min:4"
                  >
                    <v-text-field
                      label="Contraseña"
                      v-model="loginForm.password"
                      data-vv-name="password"
                      :error-messages="errors"
                      prepend-icon="mdi-lock"
                      :append-icon="shadowPassword ? 'mdi-eye' : 'mdi-eye-off'"
                      @click:append="() => (shadowPassword = !shadowPassword)"
                      :type="shadowPassword ? 'password' : 'text'"
                    ></v-text-field>
                  </validation-provider>
                </v-card-text>
                <v-card-actions>
                  <v-btn
                    block
                    type="submit"
                    color="info"
                    :disabled="invalid || loading"
                  >
                    Ingresar
                  </v-btn>
                </v-card-actions>
              </form>
            </validation-observer>
          </v-col>
        </v-row>
      </div>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  name: 'Login',
  data: function() {
    return {
      dialog: false,
      shadowPassword: true,
      loginForm: {
        username: '',
        password: '',
      },
      loading: false,
    }
  },
  computed: {
    imageHeight() {
      switch (this.$vuetify.breakpoint.name) {
        case 'xs': return '70px'
        case 'sm': return '110px'
        case 'md': return '130px'
        default: return '140'
      }
    },
  },
  methods: {
    showDialog() {
      this.loginForm = {
        username: '',
        password: '',
      }
      this.dialog = true
      this.$nextTick(() => {
        this.$refs.loginObserver.reset()
      })
    },
    async submit() {
      try {
        let valid = await this.$refs.loginObserver.validate()
        if (valid) {
          this.loading = true
          await axios.get('sanctum/csrf-cookie')
          await this.$store.dispatch('login', this.loginForm)
          this.$router.push({
            name: this.$store.getters.user.isAdmin ? 'users' : 'procedures'
          })
        }
      } catch(error) {
        this.loginForm.password = ''
        this.$refs.loginObserver.reset()
        if ('errors' in error.response.data) {
          this.$refs.loginObserver.setErrors(error.response.data.errors)
        }
      } finally {
        this.loading = false
      }
    },
  },
}
</script>