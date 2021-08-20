<template>
  <v-dialog
    v-model="dialog"
    persistent
    max-width="600"
    @keydown.esc="closeDialog"
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
          Adjuntar archivos
        </v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn
          icon
          @click.stop="closeDialog"
        >
          <v-icon>
            mdi-close
          </v-icon>
        </v-btn>
      </v-toolbar>
      <v-simple-table class="mt-3">
        <template v-slot:default>
          <tbody>
            <tr>
              <td class="text-right text-body-1">Hoja de ruta: </td>
              <td class="font-weight-bold text-body-1">{{ procedure.code }}</td>
            </tr>
            <tr>
              <td class="text-right text-body-1">Tipo de trámite: </td>
              <td class="font-weight-bold text-body-1">{{ procedureType }}</td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>
      <v-divider></v-divider>
      <div class="px-5 pb-5">
        <div v-if="success">
          <v-card-text>
            <div class="font-weight-bold text-body-1 text-center">
              Resumen del proceso
            </div>
            <v-simple-table>
              <template v-slot:default>
                <tbody>
                  <tr v-for="(file, index) in files" :key="file.id">
                    <td>{{ index + 1 }}</td>
                    <td class="text-center">{{ file.filename }}</td>
                    <td class="text-center">{{ file.success ? 'CARGADO' : 'ARCHIVO EXISTENTE' }}</td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
          </v-card-text>
          <v-card-actions>
            <v-btn
              block
              type="submit"
              color="info lighten-1"
              @click="closeDialog"
            >
              <v-icon left>
                mdi-send
              </v-icon>
              Cerrar
            </v-btn>
          </v-card-actions>
        </div>
        <div v-else>
          <validation-observer ref="procedureFilesObserver" v-slot="{ invalid }">
            <form v-on:submit.prevent="submit">
              <v-card-text>
                <validation-provider
                  v-slot="{ errors }"
                  name="attachments"
                  rules="required|size:4000"
                >
                  <v-file-input
                    chips
                    counter
                    show-size
                    multiple
                    truncate-length="15"
                    accept=".doc,.docx,application/msword,application/pdf,image/gif,image/bmp,image/png,image/jpeg"
                    label="Seleccione los archivos"
                    v-model="files"
                    data-vv-name="attachments"
                    :error-messages="errors"
                  ></v-file-input>
                </validation-provider>
              </v-card-text>
              <v-card-actions>
                <v-btn
                  block
                  type="submit"
                  color="primary"
                  :disabled="invalid || loading"
                >
                  <v-icon left>
                    mdi-send
                  </v-icon>
                  Enviar
                </v-btn>
              </v-card-actions>
            </form>
          </validation-observer>
        </div>
      </div>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  name: 'ProcedureAttachments',
  data: function() {
    return {
      dialog: false,
      loading: false,
      procedure: {},
      procedureType: null,
      files: null,
      error: [],
      success: false,
    }
  },
  methods: {
    closeDialog() {
      if (!this.loading) {
        this.dialog = false
      }
    },
    showDialog(procedure, procedureType) {
      this.files = null
      this.success = false
      this.error = []
      this.procedureType = procedureType
      this.procedure = {
        ...procedure
      }
      this.dialog = true
      this.$nextTick(() => {
        this.$refs.procedureFilesObserver.reset()
      })
    },
    async submit() {
      try {
        let valid = await this.$refs.procedureFilesObserver.validate()
        if (valid) {
          this.loading = true
          const settings = {
            headers: {
              'Content-Type': 'multipart/form-data'
            },
          }
          let formData = new FormData()
          this.files.forEach(file => {
            formData.append('attachments[]', file, file.name)
          })
          const response = await axios.post(`procedure/${this.procedure.id}/file_upload`, formData, settings)
          this.files = response.data.payload.files
          this.success = true
        }
      } catch(error) {
        this.$refs.procedureFilesObserver.reset()
        if ('errors' in error.response.data) {
          if (!('attachments' in error.response.data.errors) || error.response.data.errors.length > 1) {
            this.$refs.procedureFilesObserver.setErrors({
              'attachments': error.response.data.errors[Object.keys(error.response.data.errors)[0]][0]
            })
          } else {
            this.$refs.procedureFilesObserver.setErrors(error.response.data.errors)
          }
        }
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