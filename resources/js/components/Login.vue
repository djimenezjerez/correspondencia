<template>
  <v-app>
    <v-main>
      <v-container fill-height>
        <v-layout align-center justify-center>
          <v-flex xs12 sm3>
            <v-card-actions class="justify-center display-1 mb-3">
              <v-icon large class="mr-3">mdi-briefcase</v-icon>
              LOGIN
            </v-card-actions>
            <validation-observer ref="loginObserver" v-slot="{ invalid }">
              <v-form @submit="login" v-on:submit.prevent>
                <v-card color="secondary lighten-5" elevation="6" class="pb-4" :loading="loading">
                  <template slot="progress">
                    <v-progress-linear
                      color="info"
                      indeterminate
                    ></v-progress-linear>
                  </template>
                  <v-card-text>
                    <validation-provider
                      v-slot="{ errors }"
                      name="username"
                      rules="required|min:3|alpha_num"
                    >
                      <v-text-field
                        label="User"
                        v-model="loginForm.username"
                        data-vv-name="username"
                        :error-messages="errors"
                        prepend-icon="mdi-account"
                        ref="usernameField"
                      ></v-text-field>
                    </validation-provider>
                    <validation-provider
                      v-slot="{ errors }"
                      name="password"
                      rules="required|min:4"
                    >
                      <v-text-field
                        label="Password"
                        v-model="loginForm.password"
                        data-vv-name="password"
                        :error-messages="errors"
                        prepend-icon="mdi-lock"
                        :append-icon="shadowPassword ? 'mdi-eye-off' : 'mdi-eye'"
                        @click:append="() => (shadowPassword = !shadowPassword)"
                        :type="shadowPassword ? 'text' : 'password'"
                      ></v-text-field>
                    </validation-provider>
                  </v-card-text>
                  <v-card-actions>
                    <v-btn
                      block
                      type="submit"
                      :disabled="invalid || loading"
                    >SUBMIT</v-btn>
                  </v-card-actions>
                </v-card>
              </v-form>
            </validation-observer>
          </v-flex>
        </v-layout>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
export default {
  name: 'Login',
  mounted() {
    this.$refs.usernameField.$refs.input.focus()
  },
  data: function() {
    return {
      shadowPassword: false,
      loginForm: {
        username: '',
        password: '',
      },
      loading: false,
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