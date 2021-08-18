<template>
  <div>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-toolbar
            color="secondary"
            dark
          >
            <v-toolbar-title>
              Libro de registro de correspondencia
            </v-toolbar-title>
            <v-spacer></v-spacer>
            <v-divider class="mx-5" vertical></v-divider>
            <AddButton text="Agregar hoja de ruta" @click="$refs.dialogProcedureForm.showDialog()"/>
          </v-toolbar>
          <v-row
            class="mt-1 px-4"
          >
            <v-col xl="9" lg="9" md="9" sm="8" xs="12">
              <div class="text-xl-h5 text-lg-h5 text-md-h6 text-sm-subtitle-1 text-xs-body-1">{{ currentArea }}</div>
            </v-col>
            <v-col xl="3" lg="3" md="3" sm="4" xs="12">
              <SearchInput v-model="search"/>
            </v-col>
          </v-row>
          <v-card-text>
            <v-data-table
              :headers="headers"
              :items="procedures"
              :options.sync="options"
              :server-items-length="totalProcedures"
              :loading="loading"
              :footer-props="{
                itemsPerPageOptions: [8, 15, 30]
              }"
              class="elevation-1"
            >
              <template v-slot:[`item.area_id`]="{ item }">
                {{ area(item.area_id) }}
              </template>
              <template v-slot:[`item.procedure_type_id`]="{ item }">
                {{ procedureType(item.procedure_type_id) }}
              </template>
              <template v-slot:[`item.flow`]="{ item }">
                <v-btn
                  v-if="item.owner"
                  :medium="$vuetify.breakpoint.xl || $vuetify.breakpoint.lg"
                  :x-small="$vuetify.breakpoint.md || $vuetify.breakpoint.sm || $vuetify.breakpoint.xs"
                  rounded
                  color="warning"
                  @click="$refs.dialogProcedureFlow.showDialog(item, procedureType(item.procedure_type_id))"
                >
                  <v-icon
                    class="mr-3"
                  >
                    mdi-send
                  </v-icon>
                  Derivar
                </v-btn>
              </template>
              <template v-slot:[`item.actions`]="{ item }">
                <v-row justify="center">
                  <v-col cols="auto" v-if="!item.has_flowed && item.owner">
                    <v-tooltip bottom>
                      <template #activator="{ on }">
                        <v-icon
                          color="info"
                          v-on="on"
                          @click="$refs.dialogProcedureForm.showDialog(item)"
                        >
                          mdi-pencil
                        </v-icon>
                      </template>
                      <span>Editar</span>
                    </v-tooltip>
                  </v-col>
                  <v-col cols="auto" v-if="!item.has_flowed && item.owner">
                    <v-tooltip bottom>
                      <template #activator="{ on }">
                        <v-icon
                          color="error"
                          v-on="on"
                          @click="$refs.dialogProcedureDelete.showDialog(item)"
                        >
                          mdi-close
                        </v-icon>
                      </template>
                      <span>Eliminar</span>
                    </v-tooltip>
                  </v-col>
                </v-row>
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <ProcedureForm ref="dialogProcedureForm" :procedure-types="procedureTypes" v-on:updateList="fetchProcedures"/>
    <ProcedureDelete ref="dialogProcedureDelete" v-on:updateList="fetchProcedures"/>
    <ProcedureFlow ref="dialogProcedureFlow" :areas="areas" v-on:updateList="fetchProcedures"/>
  </div>
</template>

<script>
import ProcedureForm from '@/components/procedures/ProcedureForm'
import ProcedureDelete from '@/components/procedures/ProcedureDelete'
import ProcedureFlow from '@/components/procedures/ProcedureFlow'
import SearchInput from '@/components/shared/SearchInput'
import AddButton from '@/components/shared/AddButton'

export default {
  name: 'ProceduresList',
  components: {
    ProcedureForm,
    ProcedureDelete,
    ProcedureFlow,
    SearchInput,
    AddButton,
  },
  data: function() {
    return {
      loading: false,
      search: null,
      options: {
        page: 1,
        itemsPerPage: 8,
        sortBy: ['updated_at'],
        sortDesc: [false]
      },
      totalProcedures: 0,
      procedureTypes: [],
      areas: [],
      procedures: [],
      headers: [
        {
          text: 'Fecha de inicio',
          align: 'center',
          sortable: true,
          value: 'created_at',
        }, {
          text: 'Hoja de ruta',
          align: 'center',
          sortable: true,
          value: 'code',
        }, {
          text: 'Tipo de trámite',
          align: 'start',
          sortable: true,
          value: 'procedure_type_id',
        }, {
          text: 'Procedencia',
          align: 'start',
          sortable: false,
          value: 'origin',
        }, {
          text: 'Detalle/Asunto',
          align: 'start',
          sortable: false,
          value: 'detail',
        }, {
          text: 'Sección actual',
          align: 'start',
          sortable: false,
          value: 'area_id',
        }, {
          text: 'Sección destino',
          align: 'center',
          sortable: false,
          value: 'flow',
        }, {
          text: 'Acciones',
          align: 'center',
          sortable: false,
          value: 'actions',
        },
      ],
    }
  },
  computed: {
    currentArea() {
      const area = this.areas.find(o => o.id == this.$store.getters.user.area_id)
      if (area) {
        return area.name
      } else {
        return ''
      }
    }
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
        console.log(error)
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
        console.log(error)
      } finally {
        this.loading = false
      }
    },
    async fetchProcedures() {
      try {
        this.loading = true
        let response = await axios.get('procedure', {
          params: {
            page: this.options.page,
            per_page: this.options.itemsPerPage,
            sort_by: this.options.sortBy,
            sort_desc: this.options.sortDesc,
            search: this.search,
          },
        })
        this.procedures = response.data.payload.data
        this.totalProcedures = response.data.payload.total
        this.options.page = response.data.payload.current_page
        this.options.itemsPerPage = parseInt(response.data.payload.per_page)
      } catch(error) {
        console.log(error)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>