<template>
  <div>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-toolbar
            color="secondary"
            dark
          >
            <ToolBarTitle title="Regularización de Documentos"/>
            <v-spacer></v-spacer>
          </v-toolbar>
          <v-row
            class="mt-1 px-4 pb-0 mb-0"
            dense
          >
            <v-col xl="9" lg="9" md="9" sm="8" xs="12" class="pb-0 mb-0">
              <div class="text-xl-h5 text-lg-h5 text-md-h6 text-sm-subtitle-1 text-xs-body-1">{{ area($store.getters.user.area_id) }}</div>
            </v-col>
            <v-col xl="3" lg="3" md="3" sm="4" xs="12" class="pb-0 mb-0">
              <SearchInput v-model="search"/>
            </v-col>
          </v-row>
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
              mobile-breakpoint="0"
              dense
              id="datatable"
            >
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
                <v-btn
                  :dark="$store.getters.user.id == item.user_id"
                  rounded
                  color="orange darken-1"
                  @click="$refs.dialogProcedureRequirements.showDialog(item)"
                  class="mx-0"
                  :small="$vuetify.breakpoint.xl || $vuetify.breakpoint.lg || $vuetify.breakpoint.md"
                  :x-small="$vuetify.breakpoint.md || $vuetify.breakpoint.sm || $vuetify.breakpoint.xs"
                  :disabled="$store.getters.user.id != item.user_id"
                >
                  <div class="text-center text-responsive">
                    Requisitos
                  </div>
                </v-btn>
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <ProcedureRequirements ref="dialogProcedureRequirements" :procedure-types="procedureTypes" v-on:updateList="fetchProcedures"/>
  </div>
</template>

<script>
import ProcedureRequirements from '@/components/procedures/ProcedureRequirements'
import SearchInput from '@/components/shared/SearchInput'

export default {
  name: 'ProceduresList',
  components: {
    ProcedureRequirements,
    SearchInput,
  },
  data: function() {
    return {
      loading: false,
      search: null,
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
          width: '12%',
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
          width: '12%',
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
    this.fetchProcedures()
  },
  watch: {
    options: function(newVal, oldVal) {
      if (newVal.page != oldVal.page || newVal.itemsPerPage != oldVal.itemsPerPage || newVal.sortBy != oldVal.sortBy || newVal.sortDesc != oldVal.sortDesc) {
        this.fetchProcedures()
      }
    },
    search: function(value) {
      this.fetchProcedures()
    }
  },
  methods: {
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
        this.loading = true
        let response = await axios.get('procedure/requirement/list', {
          params: {
            page: this.options.page,
            per_page: this.options.itemsPerPage,
            search: this.search,
          },
        })
        this.procedures = response.data.payload.data
        this.totalProcedures = response.data.payload.total
        this.options.page = response.data.payload.current_page
        this.options.itemsPerPage = parseInt(response.data.payload.per_page)
      } catch(error) {
        console.error(error)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>