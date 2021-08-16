<template>
  <v-main>
    <v-container grid-list-md>
      <v-layout wrap>
        <v-flex md6 xs12>
          <v-card>
            <v-card-title class="text-h5">Perfil</v-card-title>
            <v-card-text>
              <p class="text-h4">{{ $store.getters.user.name }}</p>
              <p class="text-h5">{{ $store.getters.user.username }}</p>
            </v-card-text>
          </v-card>
        </v-flex>
        <v-flex md6 xs12>
          <v-card :loading="loading">
            <template slot="progress">
              <v-progress-linear
                color="info"
                indeterminate
              ></v-progress-linear>
            </template>
            <v-card-title class="text-h5">Cambiar contraseña</v-card-title>
            <validation-observer ref="passwordObserver" v-slot="{ invalid }">
              <v-form @submit="changePassword" v-on:submit.prevent>
                <v-card-text>
                  <validation-provider
                    v-slot="{ errors }"
                    name="old_password"
                    rules="required|min:4"
                  >
                    <v-text-field
                      label="Contraseña actual"
                      v-model="passwordForm.old_password"
                      data-vv-name="old_password"
                      :error-messages="errors"
                      prepend-icon="mdi-lock"
                      type="password"
                    ></v-text-field>
                  </validation-provider>
                  <validation-provider
                    v-slot="{ errors }"
                    name="password"
                    rules="required|min:4"
                  >
                    <v-text-field
                      label="Contraseña nueva"
                      v-model="passwordForm.password"
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
                    color="primary"
                    :disabled="invalid || loading"
                  >Enviar</v-btn>
                </v-card-actions>
              </v-form>
            </validation-observer>
          </v-card>
        </v-flex>
        <v-flex xs12>
          <v-card>
            <v-card-title class="text-h5">Roles y permissions</v-card-title>
            <v-card-text>
              <v-simple-table>
                <template v-slot:default>
                  <thead>
                    <tr>
                      <th class="text-left">
                        Roles
                      </th>
                      <th class="text-left">
                        Permisos
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>
                        <p>&#8226; {{ $store.getters.user.role }}</p>
                      </td>
                      <td>
                        <p v-for="permission in $store.getters.user.permissions" :key="permission">&#8226; {{ permission }}</p>
                      </td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
            </v-card-text>
          </v-card>
        </v-flex>
      </v-layout>
    </v-container>
  </v-main>
</template>

<script>
export default {
  name: 'Profile',
  data: function() {
    return {
      shadowPassword: true,
      loading: false,
      passwordForm: {
        old_password: '',
        password: '',
      }
    }
  },
  methods: {
    async changePassword() {
      try {
        let valid = await this.$refs.passwordObserver.validate()
        if (valid) {
          if (this.passwordForm.old_password == this.passwordForm.password) {
            this.$refs.passwordObserver.setErrors({
              password: ['La contraseña nueva debe ser diferente a la actual'],
            })
          } else {
            this.loading = true
            const response = await axios.patch(`user/${this.$store.getters.user.id}`, this.passwordForm)
            await this.$store.dispatch('logout')
            this.loading = false
            this.$toast.info(response.data.message)
            this.$router.push({
              name: 'welcome',
            })
          }
        }
      } catch(error) {
        this.passwordForm.password = ''
        this.$refs.passwordObserver.reset()
        if ('errors' in error.response.data) {
          this.$refs.passwordObserver.setErrors(error.response.data.errors)
        }
        this.loading = false
      }
    },
  }
}
</script>