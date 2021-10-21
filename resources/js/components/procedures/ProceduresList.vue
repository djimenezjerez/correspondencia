<template>
  <div>
    <v-row>
      <v-col cols="12">
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
            class="mt-1 px-4 pb-0 mb-0"
            dense
          >
            <v-col cols="12" class="pb-0 mb-0">
              <div class="text-xl-h5 text-lg-h5 text-md-h6 text-sm-subtitle-1 text-xs-body-1">{{ area($store.getters.user.area_id) }}</div>
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
                <v-row justify="center">
                  <div v-if="item.archived && item.owner">
                    <v-col cols="auto">
                      <div class="text-center">
                        Archivado
                      </div>
                    </v-col>
                  </div>
                  <div v-else>
                    <v-col cols="6" v-if="$store.getters.user.permissions.includes('DERIVAR TRÁMITE') && item.owner && procedureTypes.length > 0">
                      <div v-if="item.verified || $store.getters.user.role != 'VERIFICADOR'">
                        <v-btn
                          rounded
                          color="green darken-1"
                          @click="$refs.dialogProcedureFlow.showDialog(item, procedureType(item.procedure_type_id))"
                          class="mx-0"
                          :small="$vuetify.breakpoint.xl || $vuetify.breakpoint.lg || $vuetify.breakpoint.md"
                          :x-small="$vuetify.breakpoint.md || $vuetify.breakpoint.sm || $vuetify.breakpoint.xs"
                          :disabled="!$helpers.userOwnsProcedure(item.user_id)"
                        >
                          <div class="text-center white--text text-responsive">
                            Derivar
                          </div>
                        </v-btn>
                      </div>
                      <div v-else>
                        <v-btn
                          rounded
                          color="orange darken-1"
                          @click="$refs.dialogProcedureRequirements.showDialog(item)"
                          class="mx-0"
                          :small="$vuetify.breakpoint.xl || $vuetify.breakpoint.lg || $vuetify.breakpoint.md"
                          :x-small="$vuetify.breakpoint.md || $vuetify.breakpoint.sm || $vuetify.breakpoint.xs"
                          :disabled="!$helpers.userOwnsProcedure(item.user_id)"
                        >
                        <div class="text-center white--text text-responsive">
                          Requisitos
                        </div>
                        </v-btn>
                      </div>
                    </v-col>
                    <v-col cols="auto" v-else>
                      <div class="text-center">
                        {{ area(item.to_area) }}
                      </div>
                    </v-col>
                  </div>
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
              <template v-slot:[`item.actions`]="{ item }">
                <v-row dense no-gutters justify="space-around" align="center">
                  <v-col cols="auto" v-if="!item.has_flowed && item.owner && !item.archived && $store.getters.user.permissions.includes('EDITAR TRÁMITE')">
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          v-bind="attrs"
                          v-on="on"
                          color="info"
                          @click="$refs.dialogProcedureForm.showDialog(item)"
                          class="px-0 py-4 mx-0 my-1"
                          :small="$vuetify.breakpoint.xl || $vuetify.breakpoint.lg"
                          :x-small="$vuetify.breakpoint.md || $vuetify.breakpoint.sm || $vuetify.breakpoint.xs"
                          :disabled="!$helpers.userOwnsProcedure(item.user_id)"
                        >
                          <v-icon
                            dense
                            :small="$vuetify.breakpoint.md"
                            :x-small="$vuetify.breakpoint.md || $vuetify.breakpoint.sm || $vuetify.breakpoint.xs"
                            class="pa-0 ma-0"
                          >
                            mdi-pencil
                          </v-icon>
                        </v-btn>
                      </template>
                      <span>Editar Hoja de Ruta</span>
                    </v-tooltip>
                  </v-col>
                  <v-col cols="auto">
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          v-bind="attrs"
                          v-on="on"
                          color="error"
                          @click="$refs.dialogProcedureAttachments.showDialog(item, procedureType(item.procedure_type_id))"
                          dark
                          class="px-0 py-4 mx-0 my-1"
                          :small="$vuetify.breakpoint.xl || $vuetify.breakpoint.lg"
                          :x-small="$vuetify.breakpoint.md || $vuetify.breakpoint.sm || $vuetify.breakpoint.xs"
                        >
                          <v-icon
                            dense
                            :small="$vuetify.breakpoint.md"
                            :x-small="$vuetify.breakpoint.md || $vuetify.breakpoint.sm || $vuetify.breakpoint.xs"
                            class="pa-0 ma-0"
                          >
                            mdi-file-document-multiple
                          </v-icon>
                        </v-btn>
                      </template>
                      <span>Archivos adjuntos</span>
                    </v-tooltip>
                  </v-col>
                  <v-col cols="auto" v-if="$store.getters.user.permissions.includes('ARCHIVAR TRÁMITE') && item.has_flowed && item.owner && !item.archived">
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          v-bind="attrs"
                          v-on="on"
                          color="warning lighten-1"
                          @click="$refs.dialogProcedureArchive.showDialog(item)"
                          :dark="$store.getters.user.id == item.user_id"
                          class="px-0 py-4 mx-0 my-1"
                          :small="$vuetify.breakpoint.xl || $vuetify.breakpoint.lg"
                          :x-small="$vuetify.breakpoint.md || $vuetify.breakpoint.sm || $vuetify.breakpoint.xs"
                          :disabled="!$helpers.userOwnsProcedure(item.user_id)"
                        >
                          <v-icon
                            dense
                            :small="$vuetify.breakpoint.md"
                            :x-small="$vuetify.breakpoint.md || $vuetify.breakpoint.sm || $vuetify.breakpoint.xs"
                            class="pa-0 ma-0"
                          >
                            mdi-folder
                          </v-icon>
                        </v-btn>
                      </template>
                      <span>Archivar</span>
                    </v-tooltip>
                  </v-col>
                  <v-col cols="auto" v-if="!item.has_flowed && item.owner && !item.archived && $store.getters.user.permissions.includes('ELIMINAR TRÁMITE')">
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          v-bind="attrs"
                          v-on="on"
                          color="warning darken-1"
                          @click="$refs.dialogProcedureDelete.showDialog(item)"
                          class="px-0 py-4 mx-0 my-1"
                          :small="$vuetify.breakpoint.xl || $vuetify.breakpoint.lg"
                          :x-small="$vuetify.breakpoint.md || $vuetify.breakpoint.sm || $vuetify.breakpoint.xs"
                          :disabled="!$helpers.userOwnsProcedure(item.user_id)"
                        >
                          <v-icon
                            dense
                            :small="$vuetify.breakpoint.md"
                            :x-small="$vuetify.breakpoint.md || $vuetify.breakpoint.sm || $vuetify.breakpoint.xs"
                            class="pa-0 ma-0"
                          >
                            mdi-delete
                          </v-icon>
                        </v-btn>
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
    <ProcedureRequirements ref="dialogProcedureRequirements" :procedure-types="procedureTypes" v-on:updateList="fetchProcedures"/>
    <ProcedureArchive ref="dialogProcedureArchive" v-on:updateList="fetchProcedures"/>
    <ProcedureAttachments ref="dialogProcedureAttachments"/>
  </div>
</template>

<script>
import ProcedureForm from '@/components/procedures/ProcedureForm'
import ProcedureDelete from '@/components/procedures/ProcedureDelete'
import ProcedureFlow from '@/components/procedures/ProcedureFlow'
import ProcedureArchive from '@/components/procedures/ProcedureArchive'
import ProcedureRequirements from '@/components/procedures/ProcedureRequirements'
import ProcedureAttachments from '@/components/procedures/ProcedureAttachments'
import SearchInput from '@/components/shared/SearchInput'
import AddButton from '@/components/shared/AddButton'
import { bus } from '@/app'

export default {
  name: 'ProceduresList',
  components: {
    ProcedureForm,
    ProcedureDelete,
    ProcedureFlow,
    ProcedureArchive,
    ProcedureRequirements,
    ProcedureAttachments,
    SearchInput,
    AddButton,
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
          text: 'ACCIONES',
          align: 'center',
          sortable: false,
          value: 'actions',
          width: '17%',
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
    bus.$on('updateProcedureList', () => {
      this.search = null
      this.options = {
        page: 1,
        itemsPerPage: 8,
      },
      this.fetchProcedures()
    })
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
        let response = await axios.get('procedure', {
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