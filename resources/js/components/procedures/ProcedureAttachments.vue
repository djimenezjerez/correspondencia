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
        <ToolBarTitle title="Archivos adjuntos"/>
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
              <td class="text-right font-weight-light text-body-1">Hoja de ruta: </td>
              <td class="font-weight-bold text-body-1">{{ procedure.code }}</td>
            </tr>
            <tr>
              <td class="text-right font-weight-light text-body-1">Tipo de trámite: </td>
              <td class="font-weight-bold text-body-1">{{ procedureType }}</td>
            </tr>
            <tr>
              <td class="text-right font-weight-light text-body-1">Archivos adjuntos: </td>
              <td class="font-weight-medium text-body-1 pa-0">
                <ul class="my-2" style="list-style-type:none">
                  <li v-show="procedureFiles.length == 0">
                    SIN ARCHIVOS ADJUNTOS
                  </li>
                  <li v-for="(file, index) in procedureFiles" :key="index">
                    <v-container>
                      <v-row no-gutters>
                        <v-col cols="10" class="text-left">
                          <v-btn
                            text
                            :disabled="loading"
                            @click="downloadFile(file)"
                          >
                            <v-icon class="mr-1" :color="getExtension(file.filename).color">
                              {{ getExtension(file.filename).icon }}
                            </v-icon>
                            {{ file.filename }}
                          </v-btn>
                        </v-col>
                        <v-col cols="2" class="text-right">
                          <v-btn
                            icon
                            :disabled="loading"
                            v-if="$helpers.userOwnsProcedure(procedure.user_id) && $store.getters.user.permissions.includes('ADJUNTAR ARCHIVO')"
                            @click="deleteAttachment(file)"
                          >
                            <v-icon color="error">
                              mdi-delete
                            </v-icon>
                          </v-btn>
                        </v-col>
                      </v-row>
                    </v-container>
                  </li>
                </ul>
              </td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>
      <v-divider></v-divider>
      <div class="px-5 pb-5">
        <validation-observer ref="procedureFilesObserver" v-slot="{ invalid }">
          <form v-on:submit.prevent="submit" v-if="$helpers.userOwnsProcedure(procedure.user_id) && !procedure.archived && $store.getters.user.permissions.includes('ADJUNTAR ARCHIVO')">
            <v-card-text>
              <v-row align="center">
                <v-col cols="8">
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
                      label="Adjuntar archivos"
                      v-model="files"
                      data-vv-name="attachments"
                      :error-messages="errors"
                      color="green darken-1"
                    ></v-file-input>
                  </validation-provider>
                </v-col>
                <v-col cols="4" class="text-right">
                  <v-btn
                    type="submit"
                    color="warning"
                    :small="$vuetify.breakpoint.sm"
                    :x-small="$vuetify.breakpoint.xs"
                    :disabled="invalid || loading"
                  >
                    Subir
                    <v-icon right size="25">
                      mdi-file-document-multiple
                    </v-icon>
                  </v-btn>
                </v-col>
              </v-row>
              <div v-show="error" class="text-center red--text text-xl-h6 text-lg-subtitle-1 text-md-subtitle-1 text-sm-subtitle-2 text-xs-body-1 mt-2">{{ errorMessage }}</div>
            </v-card-text>
          </form>
        </validation-observer>
        <v-card-actions>
          <v-btn
            color="info"
            block
            :disabled="loading"
            @click="closeDialog"
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
  name: 'ProcedureAttachments',
  data: function() {
    return {
      dialog: false,
      loading: true,
      error: false,
      errorMessage: '',
      procedure: {
        id: null,
      },
      procedureType: null,
      procedureFiles: [],
      files: null,
      showMenu: false,
      x: 0,
      y: 0,
    }
  },
  methods: {
    async downloadFile(file) {
      try {
        const response = await axios.get(`procedure/${this.procedure.id}/attachment/${file.id}`, {
          responseType: 'blob',
          timeout: 30000,
        })
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', file.filename);
        document.body.appendChild(link);
        link.click();
      } catch (error) {
        console.error(error)
      }
    },
    getExtension(filename) {
      const extension = filename.split('.')[1].toUpperCase()
      switch(extension) {
        case 'DOC':
        case 'DOCX':
          return {
            icon: 'mdi-file-word-box',
            color: 'blue'
          }
        case 'JPG':
        case 'JPEG':
        case 'PNG':
        case 'BMP':
        case 'GIF':
          return {
            icon: 'mdi-image-area',
            color: 'green'
          }
        case 'PDF':
          return {
            icon: 'mdi-file-document-multiple',
            color: 'red'
          }
        default:
          return {
            icon: 'file-outline',
            color: 'black'
          }
      }
    },
    async deleteAttachment(file) {
      if (file.user_id != this.$store.getters.user.id) {
        this.error = true
        this.errorMessage = 'El archivo fue cargado por otro usuario, no se puede eliminar'
      } else {
        try {
          this.loading = true
          const response = await axios.delete(`procedure/${this.procedure.id}/attachment/${file.id}`)
          this.$toast.success(response.data.message)
          this.fetchAttachments(this.procedure.id)
        } catch (error) {
          this.$toast.error(error.response.data.message)
        } finally {
          this.loading = false
        }
      }
    },
    closeDialog() {
      if (!this.loading) {
        this.dialog = false
        this.loading = true
        this.procedure = {
          id: null,
        }
      }
    },
    showDialog(procedure, procedureType) {
      this.procedure = {
        ...procedure
      }
      this.error = false
      this.errorMessage = ''
      this.fetchAttachments(procedure.id)
      this.files = null
      this.procedureFiles = []
      this.procedureType = procedureType
      this.dialog = true
      this.$nextTick(() => {
        this.$refs.procedureFilesObserver.reset()
      })
    },
    async fetchAttachments(procedureId) {
      try {
        this.loading = true
        const response = await axios.get(`procedure/${procedureId}/attachment`)
        this.procedureFiles = response.data.payload.files
      } catch (error) {
        this.$toast.error(error.response.data.message)
      } finally {
        this.loading = false
      }
    },
    async submit() {
      try {
        this.error = false
        this.errorMessage = ''
        let valid = await this.$refs.procedureFilesObserver.validate()
        if (valid) {
          this.loading = true
          const settings = {
            headers: {
              'Content-Type': 'multipart/form-data'
            },
            timeout: 30000,
          }
          let formData = new FormData()
          this.files.forEach(file => {
            formData.append('attachments[]', file, file.name)
          })
          let response = await axios.post(`procedure/${this.procedure.id}/attachment`, formData, settings)
          this.files = []
          this.$nextTick(() => {
            this.$refs.procedureFilesObserver.reset()
          })
          this.$toast.success(response.data.message)
          this.fetchAttachments(this.procedure.id)
        }
      } catch(error) {
        this.error = true
        this.errorMessage = error.response.data.message
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

<style lang="css" scoped>
tr:hover {
  background-color: transparent !important;
}
</style>