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
      <v-toolbar dense dark color="secondary">
        <v-toolbar-title>
          Detalles de la hoja de ruta
        </v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn
          icon
          @click.stop="dialog = false"
        >
          <v-icon>
            mdi-close
          </v-icon>
        </v-btn>
      </v-toolbar>
      <div class="px-5 pb-5">
        <v-card-text>
          <v-simple-table>
            <template v-slot:default>
              <tbody>
                <tr>
                  <td class="text-right font-weight-light text-body-1">Hoja de ruta: </td>
                  <td class="font-weight-medium text-body-1">{{ procedure.code }}</td>
                </tr>
                <tr>
                  <td class="text-right font-weight-light text-body-1">Tipo de trámite: </td>
                  <td class="font-weight-medium text-body-1">{{ procedureType }}</td>
                </tr>
                <tr>
                  <td class="text-right font-weight-light text-body-1">Archivos adjuntos: </td>
                  <td class="font-weight-medium text-body-1">
                    <v-simple-table class="mt-3">
                      <template v-slot:default>
                        <tbody>
                          <tr v-for="(file, index) in files" :key="index">
                            <td>{{ file.filename }}</td>
                            <td v-if="$store.getters.user.permissions.includes('ADJUNTAR ARCHIVO')">
                              <v-tooltip bottom>
                                <template v-slot:activator="{ on, attrs }">
                                  <v-btn
                                    v-on="on"
                                    v-bind="attrs"
                                    text
                                    icon
                                    @click="deleteAttachment(file.id)"
                                  >
                                    <v-icon
                                      color="error"
                                    >
                                      mdi-close
                                    </v-icon>
                                  </v-btn>
                                </template>
                                <span>Eliminar</span>
                              </v-tooltip>
                            </td>
                          </tr>
                        </tbody>
                      </template>
                    </v-simple-table>
                  </td>
                </tr>
              </tbody>
            </template>
          </v-simple-table>
          <div v-show="error" class="text-center red--text text-xl-h6 text-lg-subtitle-1 text-md-subtitle-1 text-sm-subtitle-2 text-xs-body-1">{{ errorMessage }}</div>
          <v-divider></v-divider>
        </v-card-text>
        <v-card-actions>
          <v-btn
            block
            type="submit"
            color="primary"
            :disabled="loading"
            @click="dialog = false"
          >
            <v-icon left>
              mdi-close
            </v-icon>
            Cerrar
          </v-btn>
        </v-card-actions>
      </div>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  name: 'ProcedureDetails',
  data: function() {
    return {
      dialog: false,
      loading: true,
      files: [],
      procedure: {},
      procedureType: null,
      error: false,
      errorMessage: '',
    }
  },
  methods: {
    showDialog(procedure, procedureType) {
      this.files = []
      this.fetchAttachments(procedure.id)
      this.error = false
      this.errorMessage = ''
      this.procedureType = procedureType
      this.procedure = {
        ...procedure
      }
      this.dialog = true
    },
    async fetchAttachments(procedureId) {
      try {
        this.loading = true
        const response = await axios.get(`procedure/${procedureId}/file_upload`)
        this.files = response.data.payload.files
      } catch (error) {
        this.error = true
        this.errorMessage = error.response.data.message
      } finally {
        this.loading = false
      }
    },
    async deleteAttachment(attachmentId) {
      try {
        this.loading = true
        const response = await axios.delete(`procedure/${this.procedure.id}/file_upload/${attachmentId}`)
        this.files = this.files.filter(o => o.id != attachmentId)
        this.$toast.info(response.data.message)
      } catch (error) {
        this.error = true
        this.errorMessage = error.response.data.message
      } finally {
        this.loading = false
      }
    }
  },
}
</script>

<style lang="scss" scoped>
  tbody {
    tr:hover {
      background-color: transparent !important;
    }
  };
  // table {
  //   table-layout: fixed;
  //   width: 100%;
  // };
  // td {
  //   width: 50%;
  // };
</style>