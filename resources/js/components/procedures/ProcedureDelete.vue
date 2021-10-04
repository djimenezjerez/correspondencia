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
              ¿Seguro que desea eliminar la hoja de ruta:
            </div>
            <div>
              {{ procedure.code }}?
            </div>
            <div v-show="error" class="text-center red--text text-xl-h6 text-lg-subtitle-1 text-md-subtitle-1 text-sm-subtitle-2 text-xs-body-1">{{ errorMessage }}</div>
          </div>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            class="mr-2"
            color="success darken-1"
            :large="this.$vuetify.breakpoint.lg || this.$vuetify.breakpoint.xl"
            :small="this.$vuetify.breakpoint.sm || this.$vuetify.breakpoint.xs"
            @click="submit"
            :disabled="loading"
            v-show="!error"
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
            {{ error ? 'Cerrar': 'No' }}
          </v-btn>
        </v-card-actions>
      </div>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  name: 'ProcedureDelete',
  data: function() {
    return {
      dialog: false,
      loading: false,
      procedure: {},
      error: false,
      errorMessage: '',
    }
  },
  methods: {
    showDialog(procedure) {
      this.dialog = true
      this.error = false
      this.errorMessage = ''
      this.procedure = procedure
    },
    async submit() {
      try {
        this.loading = true
        const response = await axios.delete(`procedure/${this.procedure.id}`)
        this.$toast.success(response.data.message)
        this.$emit('updateList')
        this.dialog = false
      } catch(error) {
        this.error = true
        this.errorMessage = error.response.data.message
        this.$emit('updateList')
      } finally {
        this.loading = false
      }
    }
  },
}
</script>