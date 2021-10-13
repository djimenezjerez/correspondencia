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
      <div class="px-5 pb-5">
        <v-card-text>
          <div class="text-center text-xl-h6 text-lg-h6 text-subtitle-1 text-sm-subtitle-2 text-xs-body-1">
            <div>
              ¿Seguro que desea {{ user.is_active ? 'desactivar' : 'reactivar' }} al usuario:
            </div>
            <div>
              {{ [user.name, user.last_name].join(' ') }}?
            </div>
          </div>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            class="mr-2"
            color="success darken-1"
            :large="this.$vuetify.breakpoint.lg || this.$vuetify.breakpoint.xl"
            :small="this.$vuetify.breakpoint.sm || this.$vuetify.breakpoint.xs"
            @click="switchStatus"
            :disabled="loading"
          >
            <v-icon left>
              mdi-check
            </v-icon>
            SI
          </v-btn>
          <v-btn
            class="ml-2"
            color="error"
            :large="this.$vuetify.breakpoint.lg || this.$vuetify.breakpoint.xl"
            :small="this.$vuetify.breakpoint.sm || this.$vuetify.breakpoint.xs"
            @click="dialog = false"
            :disabled="loading"
          >
            <v-icon left>
              mdi-close
            </v-icon>
            NO
          </v-btn>
        </v-card-actions>
      </div>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  name: 'UserSwitch',
  data: function() {
    return {
      dialog: false,
      loading: false,
      user: {},
    }
  },
  methods: {
    showDialog(user) {
      this.dialog = true
      this.user = user
    },
    async switchStatus() {
      try {
        this.loading = true
        if (this.user.is_active) {
          const response = await axios.delete(`user/${this.user.id}`)
          this.$toast.success(response.data.message)
        } else {
          const response = await axios.patch(`deleted/user/${this.user.id}`)
          this.$toast.success(response.data.message)
        }
        this.$emit('updateList')
        this.dialog = false
      } catch(error) {
        console.error(error)
      } finally {
        this.loading = false
      }
    }
  },
}
</script>