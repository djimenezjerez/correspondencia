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
          <p class="text-center text-h5">
            ¿Seguro que desea eliminar el trámite
          </p>
          <p class="text-center text-h5">
            {{ procedure_type.name }}?
          </p>
          <p v-show="error" class="text-center red--text text-h6">{{ errorMessage }}</p>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            class="mr-2"
            color="success darken-1"
            large
            @click="deleteItem"
            :disabled="loading"
            v-show="!error"
          >
            <v-icon left>
              mdi-check
            </v-icon>
            Si
          </v-btn>
          <v-btn
            class="ml-2"
            color="error"
            large
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
  name: 'ProcedureTypeDelete',
  data: function() {
    return {
      dialog: false,
      loading: false,
      procedure_type: {},
      error: false,
      errorMessage: '',
    }
  },
  methods: {
    showDialog(procedure_type) {
      this.dialog = true
      this.error = false
      this.errorMessage = ''
      this.procedure_type = procedure_type
    },
    async deleteItem() {
      try {
        this.loading = true
        const response = await axios.delete(`procedure_type/${this.procedure_type.id}`)
        this.$toast.info(response.data.message)
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