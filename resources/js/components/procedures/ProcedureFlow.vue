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
        <ToolBarTitle title="Derivar hoja de ruta"/>
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
        <validation-observer ref="procedureFlowObserver" v-slot="{ invalid }">
          <form v-on:submit.prevent="submit">
            <v-card-text>
              <validation-provider
                v-slot="{ errors }"
                name="area_id"
                rules="required"
              >
                <v-select
                  :items="filteredAreas"
                  item-text="name"
                  item-value="id"
                  label="Sección destino"
                  v-model="selectedArea"
                  data-vv-name="area_id"
                  :error-messages="errors"
                  prepend-icon="mdi-briefcase"
                ></v-select>
              </validation-provider>
            </v-card-text>
            <v-card-actions>
              <v-btn
                block
                type="submit"
                color="info"
                :disabled="invalid || loading"
              >
                <v-icon left>
                  mdi-send
                </v-icon>
                Derivar
              </v-btn>
            </v-card-actions>
          </form>
        </validation-observer>
      </div>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  name: 'ProcedureFlow',
  props: {
    areas: {
      type: Array,
      required: true,
    },
  },
  data: function() {
    return {
      dialog: false,
      loading: false,
      procedure: {},
      selectedArea: null,
      procedureType: null,
    }
  },
  computed: {
    filteredAreas() {
      const area = this.areas.find(o => o.id == this.procedure.area_id)
      if (area.group > 1) {
        return this.areas.filter(o => o.group == area.group && o.id != this.procedure.area_id)
      } else {
        return this.areas.filter(o => o.group != 0 && o.id != this.procedure.area_id)
      }
    },
  },
  methods: {
    showDialog(procedure, procedureType) {
      this.procedureType = procedureType
      this.procedure = {
        ...procedure
      }
      this.selectedArea = null,
      this.dialog = true
      this.$nextTick(() => {
        this.$refs.procedureFlowObserver.reset()
      })
    },
    async printRoadmap() {
      try {
        let valid = await this.$refs.procedureFlowObserver.validate()
        if (valid) {
          this.loading = true
          const response = await axios.post(`procedure/${this.procedure.id}/print`, {
            area_id: this.selectedArea
          })
          // Abrir PDF en nueva ventana
          let pdfWindow = window.open('')
          pdfWindow.document.write('<html><head><title>' + response.data.payload.file.name + '</title><style>body{margin: 0px;}iframe{border-width: 0px;}</style></head>')
          pdfWindow.document.write('<body><iframe width="100%" height="100%" src="data:application/pdf;base64,' + encodeURI(response.data.payload.file.content) + '"></iframe></body></html>')
        }
      } catch(error) {
        this.$refs.procedureFlowObserver.reset()
        if ('errors' in error.response.data) {
          this.$refs.procedureFlowObserver.setErrors(error.response.data.errors)
        }
        this.$toast.error(error.response.data.message)
      } finally {
        this.loading = false
      }
    },
    async submit() {
      try {
        let valid = await this.$refs.procedureFlowObserver.validate()
        if (valid) {
          if (!this.procedure.has_flowed) {
            await this.printRoadmap()
          }
          this.loading = true
          const response = await axios.post(`procedure/${this.procedure.id}/flow`, {
            area_id: this.selectedArea
          })
          this.$toast.success(`${response.data.message}: ${response.data.payload.procedure.code}`)
          this.$emit('updateList')
          this.dialog = false
        }
      } catch(error) {
        this.$refs.procedureFlowObserver.reset()
        if ('errors' in error.response.data) {
          this.$refs.procedureFlowObserver.setErrors(error.response.data.errors)
        }
        this.$toast.error(error.response.data.message)
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