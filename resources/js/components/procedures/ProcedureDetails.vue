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
                  <td class="font-weight-medium text-body-1 pa-0">
                    <ul class="my-2" style="list-style-type:none">
                      <li v-show="files.length == 0">
                        SIN ARCHIVOS ADJUNTOS
                      </li>
                      <li v-for="(file, index) in files" :key="index">
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
            small
          >
            mdi-close
          </v-icon>
          <div class="text-body-2">ELIMINAR</div>
        </v-list-item>
      </v-list>
    </v-menu>
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
        const response = await axios.get(`procedure/${procedureId}/attachment`)
        this.files = response.data.payload.files
      } catch (error) {
        this.error = true
        this.errorMessage = error.response.data.message
      } finally {
        this.loading = false
      }
    },
    async deleteAttachment() {
      try {
        this.loading = true
        const response = await axios.delete(`procedure/${this.procedure.id}/attachment/${this.fileSelected}`)
        this.$toast.info(response.data.message)
        this.fetchAttachments(this.procedure.id)
      } catch (error) {
        this.error = true
        this.errorMessage = error.response.data.message
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