<template>
  <div>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-toolbar
            color="secondary"
            dark
          >
            <ToolBarTitle title="Seguimiento de correspondencia"/>
            <v-spacer></v-spacer>
          </v-toolbar>
          <validation-observer ref="searchObserver" v-slot="{ invalid }">
            <form v-on:submit.prevent="fetchProcedures">
              <v-row
                class="mt-2 pb-0 mb-0 px-xl-16 px-lg-16 mx-xl-16 mx-lg-16"
              >
                <v-col xl="5" lg="5" md="5" sm="5" xs="12" class="pb-0 mb-0 pr-0">
                  <validation-provider
                    v-slot="{ errors }"
                    name="search"
                    rules="required"
                  >
                    <v-text-field
                      label="Buscar"
                      v-model="search"
                      data-vv-name="search"
                      :error-messages="errors"
                      prepend-icon="mdi-magnify"
                      :persistent-hint="search_by == 'created_at'"
                      :hint="search_by == 'created_at' ? 'DD/MM/AAAA' : ''"
                    ></v-text-field>
                  </validation-provider>
                </v-col>
                <v-col xl="2" lg="2" md="2" sm="2" xs="12" class="pb-0 mb-0 pl-0">
                  <v-menu
                    v-if="search_by == 'created_at' && !$vuetify.breakpoint.xs"
                    v-model="menuDate"
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
                        @click.stop="menuDate = true"
                        :elevation="0"
                        :small="$vuetify.breakpoint.sm"
                      >
                        <v-icon style="font-size: 25px !important;">
                          mdi-calendar
                        </v-icon>
                      </v-btn>
                    </template>
                    <v-date-picker
                      first-day-of-week="1"
                      v-model="date"
                      no-title
                      @input="menuDate = false"
                    ></v-date-picker>
                  </v-menu>
                </v-col>
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
                      label="Por"
                      v-model="search_by"
                      data-vv-name="search_by"
                      :error-messages="errors"
                    ></v-select>
                  </validation-provider>
                </v-col>
              </v-row>
              <v-row
                class="my-0 px-4 pb-4"
                justify="space-around"
              >
                <v-col cols="12" class="text-center">
                  <v-btn
                    color="info"
                    type="submit"
                    :disabled="invalid || loading"
                  >Buscar</v-btn>
                </v-col>
              </v-row>
            </form>
          </validation-observer>
          <v-card-text class="pt-0 mt-0">
            <v-data-table
              :headers="headers"
              :items="procedures"
              :options.sync="options"
              :server-items-length="totalProcedures"
              :loading="loading"
              :footer-props="{
                itemsPerPageOptions: [8, 15, 30]
              }"
              :single-select="true"
              show-select
              mobile-breakpoint="0"
              dense
              id="datatable"
            >
              <template v-slot:[`item.data-table-select`]="{ item }">
                <v-row justify="center">
                  <v-col cols="auto">
                    <v-radio-group v-model="selectedProcedure">
                      <v-radio
                        :value="item.id"
                        color="blue lighten-1"
                      ></v-radio>
                    </v-radio-group>
                  </v-col>
                </v-row>
              </template>
              <template v-slot:[`item.incoming_at`]="{ item }">
                <div>
                  {{ item.incoming_at ? item.incoming_at : item.created_at | moment('L') }}
                </div>
                <div>
                  {{ item.incoming_at ? item.incoming_at : item.created_at | moment('LT') }}
                </div>
              </template>
              <template v-slot:[`item.from_area`]="{ item }">
                {{ area(item.from_area ? item.from_area : 0) }}
              </template>
              <template v-slot:[`item.procedure_type_id`]="{ item }">
                {{ procedureType(item.procedure_type_id) }}
              </template>
              <template v-slot:[`item.to_area`]="{ item }">
                <v-row justify="center">
                  <v-col cols="auto" v-if="item.archived && item.owner">
                    <div class="text-center">
                      Archivado
                    </div>
                  </v-col>
                  <v-col cols="auto" v-else>
                    <div class="text-center">
                      {{ area(item.to_area) }}
                    </div>
                  </v-col>
                </v-row>
              </template>
              <template v-slot:[`item.outgoing_at`]="{ item }">
                <div v-if="item.archived">
                  <div>
                    {{ item.updated_at | moment('L') }}
                  </div>
                  <div>
                    {{ item.updated_at | moment('LT') }}
                  </div>
                </div>
                <div v-else-if="!item.outgoing_at">
                  -
                </div>
                <div v-else>
                  <div>
                    {{ item.outgoing_at | moment('L') }}
                  </div>
                  <div>
                    {{ item.outgoing_at | moment('LT') }}
                  </div>
                </div>
              </template>
            </v-data-table>
            <v-row
              class="my-0 px-4 pt-2"
            >
              <v-col cols="6" class="text-end pr-6">
                <v-btn
                  color="info"
                  :disabled="selectedProcedure == null"
                  @click.stop="$refs.dialogProcedureTimeline.showDialog(procedures.find(o => o.id == selectedProcedure))"
                >Registro de recepciones</v-btn>
              </v-col>
              <v-col cols="6" class="text-start pl-6">
                <v-btn
                  color="success"
                  :disabled="search == null || search_by == null"
                  @click.stop="clearForm"
                >Limpiar</v-btn>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <ProcedureTimeline ref="dialogProcedureTimeline"/>
  </div>
</template>

<script>
import ProcedureTimeline from '@/components/procedures/ProcedureTimeline'

export default {
  name: 'ProceduresList',
  components: {
    ProcedureTimeline
  },
  data: function() {
    return {
      menuDate: false,
      loading: false,
      date: null,
      search: null,
      search_by: null,
      selectedProcedure: null,
      searchParameters: [
        {
          text: 'Hoja de Ruta',
          value: 'code',
        }, {
          text: 'Procedencia',
          value: 'origin',
        }, {
          text: 'Asunto/Detalle',
          value: 'detail',
        }, {
          text: 'Fecha de Recepción',
          value: 'created_at',
        },
      ],
      headers: [
        {
          text: 'Nº',
          align: 'center',
          sortable: false,
          value: 'counter',
          width: '2%',
        }, {
          text: 'FECHA DE INGRESO',
          align: 'center',
          sortable: false,
          value: 'incoming_at',
          width: '7%',
        }, {
          text: 'SECCIÓN/ORIGEN',
          align: 'center',
          sortable: false,
          value: 'from_area',
          width: '11%',
        }, {
          text: 'CÓDIGO DE HOJA DE RUTA',
          align: 'center',
          sortable: false,
          value: 'code',
          width: '10%',
        }, {
          text: 'TIPO DE TRÁMITE',
          align: 'center',
          sortable: false,
          value: 'procedure_type_id',
          width: '10%',
        }, {
          text: 'PROCEDENCIA',
          align: 'center',
          sortable: false,
          value: 'origin',
          width: '10%',
        }, {
          text: 'DETALLE/ASUNTO',
          align: 'center',
          sortable: false,
          value: 'detail',
          width: '15%',
        }, {
          text: 'SECCIÓN/DESTINO',
          align: 'center',
          sortable: false,
          value: 'to_area',
          width: '11%',
        }, {
          text: 'FECHA DE DERIVACIÓN',
          align: 'center',
          sortable: false,
          value: 'outgoing_at',
          width: '7%',
        }, {
          text: 'SELECCIONAR',
          align: 'center',
          sortable: false,
          value: 'data-table-select',
          width: '4%',
        },
      ],
      options: {
        page: 1,
        itemsPerPage: 8,
        sortBy: ['updated_at'],
        sortDesc: [true]
      },
      totalProcedures: 0,
      procedureTypes: [],
      areas: [],
      procedures: [],
    }
  },
  mounted() {
    const table = document.getElementById('datatable').getElementsByTagName('table')[0]
    table.setAttribute('class', 'datatables')
    table.setAttribute('width', '100%')
  },
  created() {
    this.fetchAreas()
    this.fetchProcedureTypes()
  },
  watch: {
    date: function(value) {
      if (value != null && value != '' && this.search_by == 'created_at') {
        this.search = this.$moment(value).format('L')
      }
    },
    options: function(newVal, oldVal) {
      if (newVal.page != oldVal.page || newVal.itemsPerPage != oldVal.itemsPerPage || newVal.sortBy != oldVal.sortBy || newVal.sortDesc != oldVal.sortDesc) {
        this.fetchProcedures()
      }
    },
  },
  methods: {
    clearForm() {
      this.date = null
      this.search = null
      this.search_by = null
      this.selectedProcedure = null
      this.procedures = []
      this.$nextTick(() => {
        this.$refs.searchObserver.reset()
      })
    },
    procedureType(value) {
      const procedureType = this.procedureTypes.find(o => o.id === value)
      if (procedureType) {
        return procedureType.name
      } else {
        return '-'
      }
    },
    area(value) {
      if (value == 0) return 'TRÁMITE INICIADO EN LA SECCIÓN'
      const area = this.areas.find(o => o.id === value)
      if (area) {
        return area.name
      } else {
        return '-'
      }
    },
    async fetchProcedureTypes() {
      try {
        this.loading = true
        let response = await axios.get('procedure_type', {
          params: {
            all: true,
          },
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
        this.areas = response.data.payload.areas
      } catch(error) {
        console.error(error)
      } finally {
        this.loading = false
      }
    },
    async fetchProcedures() {
      try {
        this.selectedProcedure = null
        this.procedures = []
        let date
        let valid = await this.$refs.searchObserver.validate()
        if (this.search_by == 'created_at' && this.search != null && this.search != '') {
          date = this.$moment(this.search, 'DD/MM/YYYY', true)
          valid = date.isValid()
          if (!valid) {
            this.$refs.searchObserver.setErrors({
              search: 'La fecha debe estar en formato DD/MM/YYYY'
            })
          }
        }
        if (valid) {
          this.selectedProcedure = null
          this.loading = true
          let response = await axios.get('tracking', {
            params: {
              page: this.options.page,
              per_page: this.options.itemsPerPage,
              search: (this.search_by == 'created_at') ? date.format('YYYY-MM-DD') : this.search,
              search_by: this.search_by,
            },
          })
          this.procedures = response.data.payload.data
          this.totalProcedures = response.data.payload.total
          this.options.page = response.data.payload.current_page
          this.options.itemsPerPage = parseInt(response.data.payload.per_page)
        }
      } catch(error) {
        this.$refs.searchObserver.reset()
        if ('errors' in error.response.data) {
          this.$refs.searchObserver.setErrors(error.response.data.errors)
        }
      } finally {
        this.loading = false
      }
    }
  }
}
</script>