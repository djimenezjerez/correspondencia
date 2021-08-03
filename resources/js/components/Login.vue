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
            <v-card color="blue-grey lighten-5" elevation="6" class="pl-4 pr-4 pb-4">
              <v-card-text>
                <v-form v-model="valid" ref="form">
                  <v-text-field
                    label="User"
                    v-model="username"
                    prepend-icon="mdi-account"
                  ></v-text-field>
                  <v-text-field
                    label="Password"
                    v-model="password"
                    :append-icon="e1 ? 'mdi-eye' : 'mdi-eye-off'"
                    @click:append="() => (e1 = !e1)"
                    :type="e1 ? 'password' : 'text'"
                  ></v-text-field>
                </v-form>
              </v-card-text>
              <v-card-actions>
                <v-btn
                  block
                  @click="submit"
                  :class="{
                    'blue darken-4 white--text': valid,
                    disabled: !valid
                  }"
                >Login</v-btn>
              </v-card-actions>
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
      username: '',
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