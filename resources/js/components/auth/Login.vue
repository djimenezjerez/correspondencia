<template>
  <v-card>
    <v-card-title>
      <v-spacer></v-spacer>
      <v-btn
        icon
        @click.stop="$emit('closeDialog')"
      >
        <v-icon>
          mdi-close
        </v-icon>
      </v-btn>
    </v-card-title>
    <validation-observer ref="loginObserver" v-slot="{ invalid }">
      <form v-on:submit.prevent="login">
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
            color="primary"
            :disabled="invalid || loading"
          >
            Ingresar
          </v-btn>
        </v-card-actions>
      </form>
    </validation-observer>
  </v-card>
</template>

<script>
export default {
  name: 'Login',
  data: function() {
    return {
      dialogLogin: false,
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
            name: this.$store.getters.user.roles.includes('ADMINISTRADOR') ? 'users' : 'dashboard'
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