<template>
  <div>
    <v-row>
      <v-col cols="auto">
        <v-card>
          <v-toolbar
            color="secondary"
            dark
          >
            <ToolBarTitle title="Libro de registro de correspondencia"/>
            <v-spacer></v-spacer>
            <v-divider class="mx-5" vertical v-if="$store.getters.user.permissions.includes('CREAR TRÁMITE')"></v-divider>
            <AddButton text="Agregar hoja de ruta" @click="$refs.dialogProcedureForm.showDialog()" v-if="$store.getters.user.permissions.includes('CREAR TRÁMITE')"/>
          </v-toolbar>
          <v-row
            class="mt-1 px-4"
          >
            <v-col xl="9" lg="9" md="9" sm="8" xs="12">
              <div class="text-xl-h5 text-lg-h5 text-md-h6 text-sm-subtitle-1 text-xs-body-1">{{ area($store.getters.user.area_id) }}</div>
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
              :mobile="false"
              id="datatable"
            >
              <template v-slot:[`item.to_area`]="{ item }">
                <v-row justify="center">
                  <div v-if="item.archived">
                    <v-col cols="auto">
                      <div class="text-xs-caption text-sm-caption text-sm-body-2 text-md-body-2 text-lg-body-2 text-xl-body-1">
                        ARCHIVADO
                      </div>
                    </v-col>
                  </div>
                  <div v-else>
                    <v-col cols="6" v-if="$store.getters.user.permissions.includes('DERIVAR TRÁMITE') && item.owner && procedureTypes.length > 0">
                      <div v-if="item.validated || $store.getters.user.role != 'VERIFICADOR'">
                        <v-btn
                          dark
                          rounded
                          color="green darken-1"
                          @click="$refs.dialogProcedureFlow.showDialog(item, procedureType(item.procedure_type_id))"
                          small
                        >
                          Derivar
                        </v-btn>
                      </div>
                      <div v-else>
                        <v-btn
                          dark
                          rounded
                          color="orange darken-1"
                          @click="$refs.dialogProcedureRequirements.showDialog(item)"
                          small
                        >
                          Requisitos
                        </v-btn>
                      </div>
                    </v-col>
                    <v-col cols="autp" v-else>
                      <div class="text-xs-caption text-sm-caption text-sm-body-2 text-md-body-2 text-lg-body-2 text-xl-body-1">
                        {{ area(item.to_area) }}
                      </div>
                    </v-col>
                  </div>
                </v-row>
              </template>
              <template v-slot:[`item.incoming_at`]="{ item }">
                <div class="text-xs-caption text-sm-caption text-sm-body-2 text-md-body-2 text-lg-body-2 text-xl-body-1">
                  {{ item.incoming_at ? item.incoming_at : item.created_at | moment('L LT') }}
                </div>
              </template>
              <template v-slot:[`item.outgoing_at`]="{ item }">
                <div v-if="item.archived" class="text-xs-caption text-sm-caption text-sm-body-2 text-md-body-2 text-lg-body-2 text-xl-body-1">
                  {{ item.updated_at | moment('L LT') }}
                </div>
                <div v-else-if="!item.outgoing_at" class="text-xs-caption text-sm-caption text-sm-body-2 text-md-body-2 text-lg-body-2 text-xl-body-1">
                  -
                </div>
                <div v-else class="text-xs-caption text-sm-caption text-sm-body-2 text-md-body-2 text-lg-body-2 text-xl-body-1">
                  {{ item.outgoing_at | moment('L LT') }}
                </div>
              </template>
              <template v-slot:[`item.from_area`]="{ item }" v-if="$store.getters.user.role != 'RECEPCIÓN'">
                <div class="text-xs-caption text-sm-caption text-sm-body-2 text-md-body-2 text-lg-body-2 text-xl-body-1">
                  {{ area(item.from_area ? item.from_area : 0) }}
                </div>
              </template>
              <template v-slot:[`item.code`]="{ item }">
                <div class="text-xs-caption text-sm-caption text-sm-body-2 text-md-body-2 text-lg-body-2 text-xl-body-1">
                  {{ item.code }}
                </div>
              </template>
              <template v-slot:[`item.origin`]="{ item }">
                <div class="text-xs-caption text-sm-caption text-sm-body-2 text-md-body-2 text-lg-body-2 text-xl-body-1">
                  {{ item.origin }}
                </div>
              </template>
              <template v-slot:[`item.detail`]="{ item }">
                <div class="text-xs-caption text-sm-caption text-sm-body-2 text-md-body-2 text-lg-body-2 text-xl-body-1">
                  {{ item.detail }}
                </div>
              </template>
              <template v-slot:[`item.procedure_type_id`]="{ item }">
                <div class="text-xs-caption text-sm-caption text-sm-body-2 text-md-body-2 text-lg-body-2 text-xl-body-1">
                  {{ procedureType(item.procedure_type_id) }}
                </div>
              </template>
              <template v-slot:[`item.actions`]="{ item }">
                <v-container style="width: 15em;">
                  <v-row dense no-gutters justify="space-around">
                    <v-col cols="auto" v-if="!item.has_flowed && item.owner && !item.archived && $store.getters.user.permissions.includes('EDITAR TRÁMITE')">
                      <v-btn
                        color="yellow"
                        class="py-6"
                        small
                        @click="$refs.dialogProcedureForm.showDialog(item)"
                      >
                        <v-icon>
                          mdi-pencil
                        </v-icon>
                      </v-btn>
                    </v-col>
                    <v-col cols="auto">
                      <v-btn
                        color="#00C853"
                        class="py-6"
                        small
                        dark
                        @click="$refs.dialogProcedureAttachments.showDialog(item, procedureType(item.procedure_type_id))"
                      >
                        <v-icon>
                          mdi-file-pdf
                        </v-icon>
                      </v-btn>
                    </v-col>
                    <v-col cols="auto" v-if="item.has_flowed">
                      <v-btn
                        color="info"
                        class="py-6"
                        small
                        @click="$refs.dialogProcedureTimeline.showDialog(item)"
                      >
                        <v-icon>
                          mdi-chart-timeline-variant
                        </v-icon>
                      </v-btn>
                    </v-col>
                    <v-col cols="auto" v-if="$store.getters.user.permissions.includes('ARCHIVAR TRÁMITE') && item.has_flowed && item.owner && !item.archived">
                      <v-btn
                        color="warning"
                        class="py-6"
                        small
                        dark
                        @click="$refs.dialogProcedureArchive.showDialog(item)"
                      >
                        <v-icon>
                          mdi-folder
                        </v-icon>
                      </v-btn>
                    </v-col>
                    <v-col cols="auto" v-if="!item.has_flowed && item.owner && !item.archived && $store.getters.user.permissions.includes('ELIMINAR TRÁMITE')">
                      <v-btn
                        color="red"
                        class="py-6"
                        small
                        dark
                        @click="$refs.dialogProcedureDelete.showDialog(item)"
                      >
                        <v-icon>
                          mdi-delete
                        </v-icon>
                      </v-btn>
                    </v-col>
                  </v-row>
                </v-container>
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <ProcedureForm ref="dialogProcedureForm" :procedure-types="procedureTypes" v-on:updateList="fetchProcedures"/>
    <ProcedureDelete ref="dialogProcedureDelete" v-on:updateList="fetchProcedures"/>
    <ProcedureFlow ref="dialogProcedureFlow" :areas="areas" v-on:updateList="fetchProcedures"/>
    <ProcedureRequirements ref="dialogProcedureRequirements" :requirements="requirements" v-on:updateList="fetchProcedures"/>
    <ProcedureArchive ref="dialogProcedureArchive" v-on:updateList="fetchProcedures"/>
    <ProcedureAttachments ref="dialogProcedureAttachments"/>
    <ProcedureTimeline ref="dialogProcedureTimeline"/>
  </div>
</template>

<script>
import ProcedureForm from '@/components/procedures/ProcedureForm'
import ProcedureDelete from '@/components/procedures/ProcedureDelete'
import ProcedureFlow from '@/components/procedures/ProcedureFlow'
import ProcedureArchive from '@/components/procedures/ProcedureArchive'
import ProcedureRequirements from '@/components/procedures/ProcedureRequirements'
import ProcedureAttachments from '@/components/procedures/ProcedureAttachments'
import ProcedureTimeline from '@/components/procedures/ProcedureTimeline'
import SearchInput from '@/components/shared/SearchInput'
import AddButton from '@/components/shared/AddButton'

export default {
  name: 'ProceduresList',
  components: {
    ProcedureForm,
    ProcedureDelete,
    ProcedureFlow,
    ProcedureArchive,
    ProcedureRequirements,
    ProcedureAttachments,
    ProcedureTimeline,
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
        sortDesc: [true]
      },
      totalProcedures: 0,
      procedureTypes: [],
      areas: [],
      procedures: [],
      requirements: [],
    }
  },
  computed: {
    headers() {
      let headers = [
        {
          text: 'Fecha de ingreso',
          align: 'center',
          sortable: false,
          value: 'incoming_at',
          width: '8%',
        }, {
          text: 'Sección/Origen',
          align: 'center',
          sortable: false,
          value: 'from_area',
          width: '10%',
        }, {
          text: 'Código de hoja de ruta',
          align: 'center',
          sortable: false,
          value: 'code',
          width: '10%',
        }, {
          text: 'Tipo de trámite',
          align: 'center',
          sortable: false,
          value: 'procedure_type_id',
          width: '10%',
        }, {
          text: 'Procedencia',
          align: 'center',
          sortable: false,
          value: 'origin',
          width: '10%',
        }, {
          text: 'Detalle/Asunto',
          align: 'center',
          sortable: false,
          value: 'detail',
          width: '12%',
        }, {
          text: 'Sección/Destino',
          align: 'center',
          sortable: false,
          value: 'to_area',
          width: '10%',
        }, {
          text: 'Fecha de derivación',
          align: 'center',
          sortable: false,
          value: 'outgoing_at',
          width: '8%',
        }, {
          text: 'Acciones',
          align: 'center',
          sortable: false,
          value: 'actions',
          width: '8%',
        },
      ]
      if (this.$store.getters.user.role == 'RECEPCIÓN') {
        return headers.filter(o => o.value != 'from_area')
      } else {
        return headers
      }
    }
  },
  mounted() {
    const table = document.getElementById('datatable').getElementsByTagName('table')[0]
    table.setAttribute('class', 'datatables')
  },
  created() {
    this.fetchAreas()
    this.fetchRequirements()
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
    async fetchRequirements() {
      try {
        this.loading = true
        let response = await axios.get('requirement', {
          params: {
            all: true,
          },
        })
        this.requirements = response.data.payload.requirements
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