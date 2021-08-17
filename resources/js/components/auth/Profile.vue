<template>
  <v-main>
    <v-container grid-list-md>
      <v-layout wrap>
        <v-flex md6 xs12 class="px-4">
          <v-card :loading="loading">
            <template slot="progress">
              <v-progress-linear
                color="secondary"
                height="10"
                indeterminate
              ></v-progress-linear>
            </template>
            <v-toolbar
              color="secondary"
              dark
            >
              <v-toolbar-title>
                Datos del usuario
              </v-toolbar-title>
            </v-toolbar>
            <v-card-text>
              <v-simple-table>
                <template v-slot:default>
                  <tbody>
                    <tr>
                      <td class="text-right">Nombre: </td>
                      <td>{{ user.name }}</td>
                    </tr>
                    <tr>
                      <td class="text-right">Área: </td>
                      <td>{{ user.area }}</td>
                    </tr>
                    <tr>
                      <td class="text-right">Tipo de documento: </td>
                      <td>{{ user.document_type }}</td>
                    </tr>
                    <tr>
                      <td class="text-right">Documento de identidad: </td>
                      <td>{{ user.username }}</td>
                    </tr>
                    <tr>
                      <td class="text-right">Teléfono: </td>
                      <td>{{ user.phone }}</td>
                    </tr>
                    <tr>
                      <td class="text-right">Dirección: </td>
                      <td>{{ user.address }}</td>
                    </tr>
                    <tr>
                      <td class="text-right">Email: </td>
                      <td>{{ user.email }}</td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
            </v-card-text>
          </v-card>
        </v-flex>
        <v-flex md6 xs12 class="px-4">
          <v-card :loading="loading">
            <template slot="progress">
              <v-progress-linear
                color="secondary"
                height="10"
                indeterminate
              ></v-progress-linear>
            </template>
            <v-toolbar
              color="secondary"
              dark
            >
              <v-toolbar-title>
                Cambiar contraseña
              </v-toolbar-title>
            </v-toolbar>
            <div class="px-5 pb-5">
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
            </div>
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
      },
      user: {},
    }
  },
  mounted() {
    this.fetchUser()
  },
  methods: {
    async fetchUser() {
      try {
        this.loading = true
        const response = await axios.get(`user/${this.$store.getters.user.id}`)
        this.user = response.data.payload.user
      } catch(error) {
        console.log(error)
      } finally {
        this.loading = false
      }
    },
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

<style lang="scss" scoped>
  tbody {
    tr:hover {
      background-color: transparent !important;
    }
  }
</style>