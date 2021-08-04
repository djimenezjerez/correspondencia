<template>
  <v-app>
    <v-main>
      <v-container fluid fill-height>
        <v-layout flex align-center justify-center>
          <v-flex xs12 sm3>
            <v-card-actions class="justify-center display-1 mb-3">
              <v-icon large class="mr-3">mdi-briefcase</v-icon>
              LOGIN
            </v-card-actions>
            <v-form v-model="valid" ref="form" @submit="submit" v-on:submit.prevent>
              <v-card color="blue-grey lighten-5" elevation="6" class="pl-4 pr-4 pb-4">
                <v-card-text>
                  <v-text-field
                    label="User"
                    v-model="loginForm.username.value"
                    :error-messages="loginForm.username.error"
                    prepend-icon="mdi-account"
                  ></v-text-field>
                  <v-text-field
                    label="Password"
                    v-model="loginForm.password.value"
                    :error-messages="loginForm.password.error"
                    prepend-icon="mdi-lock"
                    :append-icon="shadowPassword ? 'mdi-eye' : 'mdi-eye-off'"
                    @click:append="() => (shadowPassword = !shadowPassword)"
                    :type="shadowPassword ? 'password' : 'text'"
                  ></v-text-field>
                </v-card-text>
                <v-card-actions>
                  <v-btn
                    block
                    type="submit"
                    :class="{
                      'blue darken-4 white--text': valid,
                      disabled: !valid
                    }"
                  >Login</v-btn>
                </v-card-actions>
              </v-card>
            </v-form>
          </v-flex>
        </v-layout>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
export default {
  name: 'Login',
  data: function () {
    return {
      valid: false,
      shadowPassword: true,
      loginForm: {
        username: {
          value: '',
          error: null,
        },
        password: {
          value: '',
          error: null,
        }
      }
    }
  },
  methods: {
    async submit () {
      try {
        await axios.get('/sanctum/csrf-cookie', {
          baseURL: process.env.BASE_URL,
        })
        window.axios.defaults.baseURL = '/api/'
        let data = {}
        for (const [key, entry] of Object.entries(this.loginForm)) {
          data[key] = entry.value
        }
        let response = await axios.post('login', data)
        // Dispatch vuex login action
      } catch(error) {
        if ('errors' in error.response.data) {
          Object.keys(error.response.data.errors).forEach((key) => {
            if (key in this.loginForm) {
              this.loginForm[key].error = error.response.data.errors[key][0]
            }
          })
        }
        this.loginForm.password.value = ''
      }
    },
  },
}
</script>