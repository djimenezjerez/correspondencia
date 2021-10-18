<template>
  <div>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-toolbar
            color="secondary"
            dark
          >
            <ToolBarTitle title="Reporte dinámico de correspondencia"/>
            <v-spacer></v-spacer>
          </v-toolbar>
          <validation-observer ref="searchObserver" v-slot="{ invalid }">
            <form v-on:submit.prevent="getReport">
              <v-row
                class="mt-2 pb-0 mb-0 px-xl-16 px-lg-16 mx-xl-16 mx-lg-16"
              >
                <v-col xl="4" lg="4" md="4" sm="4" xs="12" class="pb-0 mb-0 pr-0">
                  <validation-provider
                    v-slot="{ errors }"
                    name="start_date"
                    rules="required"
                  >
                    <v-text-field
                      label="Fecha inicial"
                      v-model="reportForm.start_date"
                      data-vv-name="start_date"
                      :error-messages="errors"
                      prepend-icon="mdi-calendar"
                      persistent-hint
                      hint="DD/MM/AAAA"
                    ></v-text-field>
                  </validation-provider>
                </v-col>
                <v-col xl="2" lg="2" md="2" sm="2" xs="12" class="pb-0 mb-0 pl-0">
                  <v-menu
                    v-if="!$vuetify.breakpoint.xs"
                    v-model="menuDate.start"
                    :close-on-content-click="true"
                    transition="scale-transition"
                    offset-y
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        class="mt-sm-5 mt-md-3"
                        color="success"
                        v-bind="attrs"
                        v-on="on"
                        @click.stop="menuDate.start = true"
                        :elevation="0"
                        :small="$vuetify.breakpoint.sm"
                        :disabled="loading"
                      >
                        <v-icon style="font-size: 25px !important;">
                          mdi-calendar
                        </v-icon>
                      </v-btn>
                    </template>
                    <v-date-picker
                      first-day-of-week="1"
                      v-model="startDate"
                      no-title
                      @input="menuDate.start = false"
                    ></v-date-picker>
                  </v-menu>
                </v-col>
                <v-col xl="4" lg="4" md="4" sm="4" xs="12" class="pb-0 mb-0 pr-0">
                  <validation-provider
                    v-slot="{ errors }"
                    name="end_date"
                    rules="required"
                  >
                    <v-text-field
                      label="Fecha final"
                      v-model="reportForm.end_date"
                      data-vv-name="end_date"
                      :error-messages="errors"
                      prepend-icon="mdi-calendar"
                      persistent-hint
                      hint="DD/MM/AAAA"
                    ></v-text-field>
                  </validation-provider>
                </v-col>
                <v-col xl="2" lg="2" md="2" sm="2" xs="12" class="pb-0 mb-0 pl-0">
                  <v-menu
                    v-if="!$vuetify.breakpoint.xs"
                    v-model="menuDate.end"
                    :close-on-content-click="true"
                    transition="scale-transition"
                    offset-y
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        class="mt-sm-5 mt-md-3"
                        color="success"
                        v-bind="attrs"
                        v-on="on"
                        @click.stop="menuDate.end = true"
                        :elevation="0"
                        :small="$vuetify.breakpoint.sm"
                        :disabled="loading"
                      >
                        <v-icon style="font-size: 25px !important;">
                          mdi-calendar
                        </v-icon>
                      </v-btn>
                    </template>
                    <v-date-picker
                      first-day-of-week="1"
                      v-model="endDate"
                      no-title
                      @input="menuDate.end = false"
                    ></v-date-picker>
                  </v-menu>
                </v-col>
              </v-row>
              <v-row
                class="mt-2 pb-0 mb-0 px-xl-16 px-lg-16 mx-xl-16 mx-lg-16"
              >
                <v-col xl="5" lg="5" md="5" sm="5" xs="12" class="pb-0 mb-0">
                  <validation-provider
                    v-slot="{ errors }"
                    name="area_id"
                    rules=""
                  >
                    <v-select
                      :items="areas"
                      item-text="name"
                      item-value="id"
                      label="Área"
                      v-model="reportForm.area_id"
                      data-vv-name="area_id"
                      :error-messages="errors"
                      prepend-icon="mdi-briefcase"
                      :disabled="!$store.getters.user.isAdmin"
                    ></v-select>
                  </validation-provider>
                </v-col>
              </v-row>
              <v-row
                class="mt-2 pb-0 mb-0 px-xl-16 px-lg-16 mx-xl-16 mx-lg-16"
              >
                <v-col xl="5" lg="5" md="5" sm="5" xs="12" class="pb-0 mb-0">
                  <validation-provider
                    v-slot="{ errors }"
                    name="procedure_type_id"
                    rules=""
                  >
                    <v-select
                      :items="procedureTypes"
                      item-text="name"
                      item-value="id"
                      label="Tipo de trámite"
                      v-model="reportForm.procedure_type_id"
                      data-vv-name="procedure_type_id"
                      :error-messages="errors"
                      prepend-icon="mdi-paperclip"
                    ></v-select>
                  </validation-provider>
                </v-col>
              </v-row>
              <v-row
                class="mt-2 pb-0 mb-0 px-xl-16 px-lg-16 mx-xl-16 mx-lg-16"
              >
                <v-col xl="5" lg="5" md="5" sm="5" xs="12" class="pb-0 mb-0">
                  <validation-provider
                    v-slot="{ errors }"
                    name="search_by"
                    rules="required"
                  >
                    <v-select
                      :items="searchParameters"
                      item-text="text"
                      item-value="value"
                      label="Criterios de Selección"
                      v-model="reportForm.search_by"
                      data-vv-name="search_by"
                      :error-messages="errors"
                      prepend-icon="mdi-tray"
                    ></v-select>
                  </validation-provider>
                </v-col>
              </v-row>
              <v-row
                class="my-0 px-4 pt-2"
              >
                <v-col cols="6" class="text-end pr-6">
                  <v-btn
                    color="error"
                    type="submit"
                    :disabled="invalid || loading"
                  >Generar en PDF</v-btn>
                </v-col>
                <v-col cols="6" class="text-start pl-6">
                  <v-btn
                    color="yellow"
                    :disabled="Object.values(reportForm).every(i => i === null) || loading"
                    @click.stop="clearForm"
                  >Limpiar</v-btn>
                </v-col>
              </v-row>
              <v-row
                class="my-0 px-4 pt-2"
              >
                <v-col cols="12" class="text-center red--text">
                  {{ errorMessage }}
                </v-col>
              </v-row>
            </form>
          </validation-observer>
          <v-card-footer>
            <v-progress-linear
              v-if="loading"
              color="primary"
              height="4"
              indeterminate
            ></v-progress-linear>
          </v-card-footer>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script>
export default {
  name: 'Report',
  data: function() {
    return {
      procedureTypes: [],
      areas: [],
      menuDate: {
        start: false,
        end: false,
      },
      loading: false,
      startDate: null,
      endDate: null,
      reportForm: {
        start_date: null,
        end_date: null,
        area_id: null,
        procedure_type_id: null,
        search_by: null,
      },
      errorMessage: '',
      searchParameters: [
        {
          text: 'Pendientes',
          value: 'pending',
        }, {
          text: 'Recibidos',
          value: 'received',
        }, {
          text: 'Archivados',
          value: 'archived',
        },
      ],
    }
  },
  created() {
    this.fetchAreas()
    this.fetchProcedureTypes()
  },
  watch: {
    startDate: function(value) {
      if (value != null && value != '') {
        this.reportForm.start_date = this.$moment(value).format('L')
        this.$nextTick(() => {
          this.$refs.searchObserver.reset()
        })
      }
    },
    endDate: function(value) {
      if (value != null && value != '') {
        this.reportForm.end_date = this.$moment(value).format('L')
        this.$nextTick(() => {
          this.$refs.searchObserver.reset()
        })
      }
    },
  },
  methods: {
    clearForm() {
      this.menuDate = {
        start: false,
        end: false,
      }
      this.startDate = null
      this.endDate = null
      this.reportForm = {
        start_date: null,
        end_date: null,
        area_id: this.$store.getters.user.isAdmin ? null : this.reportForm.area_id,
        procedure_type_id: null,
        search_by: null,
      }
      this.errorMessage = ''
      this.$nextTick(() => {
        this.$refs.searchObserver.reset()
      })
    },
    async fetchProcedureTypes() {
      try {
        this.loading = true
        let response = await axios.get('procedure_type', {
          params: {
            all: true,
          },
        })
        response.data.payload.procedure_types.unshift({
          id: null,
          name: 'Todos'
        })
        this.procedureTypes = response.data.payload.procedure_types
      } catch(error) {
        console.error(error)
      } finally {
        this.loading = false
      }
    },
    async fetchAreas() {
      try {
        this.loading = true
        let response = await axios.get('area')
        if (this.$store.getters.user.isAdmin) {
          response.data.payload.areas.unshift({
            id: null,
            name: 'Todas'
          })
          this.areas = response.data.payload.areas
        } else {
          this.areas = response.data.payload.areas.filter(o => o.id == this.$store.getters.user.area_id)
          this.reportForm.area_id = this.areas[0].id
        }
      } catch(error) {
        console.error(error)
      } finally {
        this.loading = false
      }
    },
    async getReport() {
      try {
        this.errorMessage = ''
        let startDate, endDate
        let valid = await this.$refs.searchObserver.validate()
        startDate = this.$moment(this.reportForm.start_date, 'DD/MM/YYYY', true)
        if (!startDate.isValid()) {
          this.$refs.searchObserver.setErrors({
            start_date: 'La fecha inicial debe estar en formato DD/MM/YYYY'
          })
        }
        endDate = this.$moment(this.reportForm.end_date, 'DD/MM/YYYY', true)
        if (!endDate.isValid()) {
          this.$refs.searchObserver.setErrors({
            end_date: 'La fecha final debe estar en formato DD/MM/YYYY'
          })
        }
        if (endDate.isBefore(startDate)) {
          this.$refs.searchObserver.setErrors({
            end_date: 'La fecha final debe ser posterior o igual a la fecha inicial'
          })
        }
        valid &= startDate.isValid() && endDate.isValid() && !startDate.isAfter(endDate) && !endDate.isBefore(startDate)
        if (valid) {
          this.loading = true
          let response = await axios.get('report', {
            params: {
              start_date: startDate.format('YYYY-MM-DD'),
              end_date: endDate.format('YYYY-MM-DD'),
              area_id: this.reportForm.area_id,
              procedure_type_id: this.reportForm.procedure_type_id,
              search_by: this.reportForm.search_by,
            },
          })
          let pdfWindow = window.open('')
          pdfWindow.document.write('<html><head><title>' + response.data.payload.file.name + '</title><style>body{margin: 0px;}iframe{border-width: 0px;}</style></head>')
          pdfWindow.document.write('<body><iframe width="100%" height="100%" src="data:application/pdf;base64,' + encodeURI(response.data.payload.file.content) + '"></iframe></body></html>')
        }
      } catch(error) {
        this.$refs.searchObserver.reset()
        if ('errors' in error.response.data) {
          this.$refs.searchObserver.setErrors(error.response.data.errors)
        }
        this.errorMessage = error.response.data.message
      } finally {
        this.loading = false
      }
    },
  },
}
</script>