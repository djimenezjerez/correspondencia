<template>
  <v-app>
    <v-main>
      <v-container fluid fill-height>
        <v-layout flex align-center justify-center>
          <v-flex  xs12 sm4 elevation-6>
            <v-toolbar class="pt-5 blue darken-4">
              <v-toolbar-items>
                <v-toolbar-title class="white--text"><h4>LOGIN</h4></v-toolbar-title>
              </v-toolbar-items>
            </v-toolbar>
            <v-card>
              <v-card-text class="pt-4">
                <v-form v-model="valid" ref="form">
                  <v-text-field
                    label="User"
                    v-model="username"
                    min="3"
                    :rules="usernameRules"
                    required
                  ></v-text-field>
                  <v-text-field
                    label="Password"
                    v-model="password"
                    min="4"
                    :append-icon="e1 ? 'mdi-eye' : 'mdi-eye-off'"
                    @click:append="() => (e1 = !e1)"
                    :type="e1 ? 'password' : 'text'"
                    :rules="passwordRules"
                    required
                  ></v-text-field>
                  <v-layout align-end justify-end>
                      <v-btn @click="submit" :class=" { 'blue darken-4 white--text' : valid, disabled: !valid }">Login</v-btn>
                  </v-layout>
                </v-form>
              </v-card-text>
              </v-card>
          </v-flex>
        </v-layout>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
export default {
  data: function () {
    return {
      valid: false,
      e1: true,
      password: '',
      passwordRules: [
        (v) => !!v || 'Password is required',
      ],
      username: '',
      usernameRules: [
        (v) => !!v || 'User is required',
      ],
    }
  },
  methods: {
    submit () {
      if (this.$refs.form.validate()) {
        this.$refs.form.$el.submit()
      }
    },
    clear () {
      this.$refs.form.reset()
    }
  },
}
</script>