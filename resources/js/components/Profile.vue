<template>
  <v-main>
    <v-container fill-height>
      <v-layout align-start justify-center>
        <v-flex xs12 sm6>
          <v-card>
            <v-card-title class="text-h3">{{ $store.getters.user.name }}</v-card-title>
            <v-card-subtitle class="text-h5">{{ $store.getters.user.username }}</v-card-subtitle>
            <v-card-text>
              <v-card>
                <v-card-title class="text-h5">Role and permissions</v-card-title>
                <v-card-text>
                  <v-simple-table>
                    <template v-slot:default>
                      <thead>
                        <tr>
                          <th class="text-left">
                            Role
                          </th>
                          <th class="text-left">
                            Permissions
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>{{ $store.getters.role.name }}</td>
                          <td>
                            <p v-for="permission in rolePermissions" :key="permission">&#8226; {{ permission }}</p>
                          </td>
                        </tr>
                      </tbody>
                    </template>
                  </v-simple-table>
                </v-card-text>
              </v-card>
              <v-card class="mt-5" :loading="loading">
                <template slot="progress">
                  <v-progress-linear
                    color="info"
                    indeterminate
                  ></v-progress-linear>
                </template>
                <v-card-title class="text-h5">Change password</v-card-title>
                <validation-observer ref="passwordObserver" v-slot="{ invalid }">
                  <v-form @submit="changePassword" v-on:submit.prevent>
                    <v-card-text>
                      <validation-provider
                        v-slot="{ errors }"
                        name="old_password"
                        rules="required|min:4"
                      >
                        <v-text-field
                          label="Old Password"
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
                          label="New Password"
                          v-model="passwordForm.password"
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
                  </v-form>
                </validation-observer>
              </v-card>
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
      rolePermissions: [],
      shadowPassword: false,
      loading: false,
      passwordForm: {
        old_password: '',
        password: '',
      }
    }
  },
  created() {
    this.getPermissions(this.$store.getters.role.id)
  },
  methods: {
    async getPermissions(roleId) {
      try {
        this.loading = true
        let response = await axios.get(`role/${roleId}/permission`)
        this.rolePermissions = response.data.payload.permissions
        this.loading = false
      } catch(error) {
        console.log(error)
        this.loading = false
      }
    },
    async changePassword() {
      try {
        let valid = await this.$refs.passwordObserver.validate()
        if (valid) {
          if (this.passwordForm.old_password == this.passwordForm.password) {
            this.$refs.passwordObserver.setErrors({
              password: ['The new password must be different from the old'],
            })
          } else {
            this.loading = true
            await axios.patch(`user/${this.$store.getters.user.id}`, this.passwordForm)
            await this.$store.dispatch('logout')
            this.loading = false
            this.$router.push({
              name: 'login'
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