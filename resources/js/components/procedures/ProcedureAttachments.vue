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
          Archivos adjuntos
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
                    <v-btn
                      text
                      :disabled="loading"
                      @click="downloadFile(file)"
                      @contextmenu="showOptions($event, file.id)"
                    >
                      <v-icon class="mr-1" :color="getExtension(file.filename).color">
                        {{ getExtension(file.filename).icon }}
                      </v-icon>
                      {{ file.filename }}
                    </v-btn>
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
          <form v-on:submit.prevent="submit" v-if="procedure.owner && !procedure.archived && $store.getters.user.permissions.includes('ADJUNTAR ARCHIVO')">
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
                    ></v-file-input>
                  </validation-provider>
                </v-col>
                <v-col cols="4" class="text-right">
                  <v-btn
                    type="submit"
                    color="warning"
                    :x-small="$vuetify.breakpoint.xs || $vuetify.breakpoint.sm"
                    :disabled="invalid || loading"
                  >
                    <v-icon left>
                      mdi-upload
                    </v-icon>
                    Enviar
                  </v-btn>
                </v-col>
              </v-row>
            </v-card-text>
          </form>
        </validation-observer>
        <v-card-actions>
          <v-btn
            color="primary"
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
    <v-menu
      v-model="showMenu"
      :position-x="x"
      :position-y="y"
      absolute
      offset-y
    >
      <v-list dense>
        <v-list-item @click="deleteAttachment" :disabled="loading">
          <v-icon
            color="error"
            class="mr-1"
            :x-small="$vuetify.breakpoint.xs || $vuetify.breakpoint.sm"
          >
            mdi-close
          </v-icon>
          <div class="text-md-body-2 text-sm-body-1 text-xs-caption">ELIMINAR</div>
        </v-list-item>
      </v-list>
    </v-menu>
  </v-dialog>
</template>

<script>
export default {
  name: 'ProcedureAttachments',
  data: function() {
    return {
      dialog: false,
      loading: true,
      procedure: {
        id: null,
      },
      procedureType: null,
      procedureFiles: [],
      files: null,
      showMenu: false,
      fileSelected: null,
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
        console.log(error)
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
            icon: 'mdi-file-pdf',
            color: 'red'
          }
        default:
          return {
            icon: 'file-outline',
            color: 'black'
          }
      }
    },
    async deleteAttachment() {
      try {
        this.loading = true
        const response = await axios.delete(`procedure/${this.procedure.id}/attachment/${this.fileSelected}`)
        this.$toast.info(response.data.message)
        this.fetchAttachments(this.procedure.id)
      } catch (error) {
        this.$toast.error(error.response.data.message)
      } finally {
        this.loading = false
      }
    },
    showOptions(e, fileSelected) {
      if (!this.procedure.archived && this.procedure.owner && this.$store.getters.user.permissions.includes('ADJUNTAR ARCHIVO')) {
        e.preventDefault()
        this.fileSelected = fileSelected
        this.showMenu = false
        this.x = e.clientX
        this.y = e.clientY
        this.$nextTick(() => {
          this.showMenu = true
        })
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
      this.fetchAttachments(procedure.id)
      this.fileSelected = null
      this.files = null
      this.procedureFiles = []
      this.procedureType = procedureType
      this.procedure = {
        ...procedure
      }
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
          this.files = null
          this.$nextTick(() => {
            this.$refs.procedureFilesObserver.reset()
          })
          this.$toast.info(response.data.message)
          if (response.data.payload.files.some(o => o.success)) {
            this.fetchAttachments(this.procedure.id)
          }
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