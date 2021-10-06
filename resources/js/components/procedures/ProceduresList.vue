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
              hide-default-header
              mobile-breakpoint="0"
              dense
              id="datatable"
            >
              <template v-slot:header>
                <thead>
                  <tr>
                    <th v-for="(header, index) in tableHeaders" :key="index" class="text-center" :style="{ 'font-size': `${fontSize} !important` }">
                      {{ header.text }}
                    </th>
                    <th width="15%" class="px-0 mx-0 text-center" :style="{ 'font-size': `${fontSize} !important` }">
                      {{ actionHeader.text }}
                    </th>
                  </tr>
                </thead>
              </template>
              <template v-slot:body="{ items }">
                <tbody>
                  <tr v-for="item in items" :key="item.id" :style="{ 'height': rowHeight }">
                    <td>
                      <div class="text-center" :style="{ 'font-size': `${fontSize} !important` }">
                        {{ item.incoming_at ? item.incoming_at : item.created_at | moment('L LT') }}
                      </div>
                    </td>
                    <td v-if="$store.getters.user.role != 'RECEPCIÓN'">
                      <div class="text-center" :style="{ 'font-size': `${fontSize} !important` }">
                        {{ area(item.from_area ? item.from_area : 0) }}
                      </div>
                    </td>
                    <td>
                      <div class="text-center" :style="{ 'font-size': `${fontSize} !important` }">
                        {{ item.code }}
                      </div>
                    </td>
                    <td>
                      <div class="text-center" :style="{ 'font-size': `${fontSize} !important` }">
                        {{ procedureType(item.procedure_type_id) }}
                      </div>
                    </td>
                    <td>
                      <div class="text-center" :style="{ 'font-size': `${fontSize} !important` }">
                        {{ item.origin }}
                      </div>
                    </td>
                    <td>
                      <div class="text-center" :style="{ 'font-size': `${fontSize} !important` }">
                        {{ item.detail }}
                      </div>
                    </td>
                    <td>
                      <v-row justify="center">
                        <div v-if="item.archived && item.owner">
                          <v-col cols="auto">
                            <div class="text-center" :style="{ 'font-size': `${fontSize} !important` }">
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
                                class="py-xl-6 py-lg-6 py-md-0 py-sm-0 py-xs-0 px-xl-4 px-lg-4 px-md-2 px-sm-2 px-xs-0 mx-0"
                                :small="$vuetify.breakpoint.xl || $vuetify.breakpoint.lg || $vuetify.breakpoint.md"
                                :x-small="$vuetify.breakpoint.md || $vuetify.breakpoint.sm || $vuetify.breakpoint.xs"
                              >
                                <div class="text-center" :style="{ 'font-size': `${fontSize} !important` }">
                                  Derivar
                                </div>
                              </v-btn>
                            </div>
                            <div v-else>
                              <v-btn
                                dark
                                rounded
                                color="orange darken-1"
                                @click="$refs.dialogProcedureRequirements.showDialog(item)"
                                class="py-xl-6 py-lg-6 py-md-0 py-sm-0 py-xs-0 px-xl-4 px-lg-4 px-md-2 px-sm-2 px-xs-0 mx-0"
                                :small="$vuetify.breakpoint.xl || $vuetify.breakpoint.lg || $vuetify.breakpoint.md"
                                :x-small="$vuetify.breakpoint.md || $vuetify.breakpoint.sm || $vuetify.breakpoint.xs"
                              >
                              <div class="text-center" :style="{ 'font-size': `${fontSize} !important` }">
                                Requisitos
                              </div>
                              </v-btn>
                            </div>
                          </v-col>
                          <v-col cols="autp" v-else>
                            <div class="text-center" :style="{ 'font-size': `${fontSize} !important` }">
                              {{ area(item.to_area) }}
                            </div>
                          </v-col>
                        </div>
                      </v-row>
                    </td>
                    <td>
                      <div class="text-center" :style="{ 'font-size': `${fontSize} !important` }">
                        <div v-if="item.archived">
                          {{ item.updated_at | moment('L LT') }}
                        </div>
                        <div v-else-if="!item.outgoing_at">
                          -
                        </div>
                        <div v-else>
                          {{ item.outgoing_at | moment('L LT') }}
                        </div>
                      </div>
                    </td>
                    <td>
                      <v-row dense no-gutters justify="space-around">
                        <v-col cols="auto" v-if="!item.has_flowed && item.owner && !item.archived && $store.getters.user.permissions.includes('EDITAR TRÁMITE')">
                          <v-btn
                            color="info"
                            @click="$refs.dialogProcedureForm.showDialog(item)"
                            class="py-xl-6 py-lg-6 py-md-0 py-sm-0 py-xs-0 px-0 mx-0 my-1"
                            :small="$vuetify.breakpoint.xl || $vuetify.breakpoint.lg"
                            :x-small="$vuetify.breakpoint.md || $vuetify.breakpoint.sm || $vuetify.breakpoint.xs"
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
                        </v-col>
                        <v-col cols="auto">
                          <v-btn
                            color="error"
                            @click="$refs.dialogProcedureAttachments.showDialog(item, procedureType(item.procedure_type_id))"
                            dark
                            class="py-xl-6 py-lg-6 py-md-0 py-sm-0 py-xs-0 px-0 mx-0 my-1"
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
                        </v-col>
                        <v-col cols="auto" v-if="item.has_flowed">
                          <v-btn
                            color="info"
                            @click="$refs.dialogProcedureTimeline.showDialog(item)"
                            class="py-xl-6 py-lg-6 py-md-0 py-sm-0 py-xs-0 px-0 mx-0 my-1"
                            :small="$vuetify.breakpoint.xl || $vuetify.breakpoint.lg"
                            :x-small="$vuetify.breakpoint.md || $vuetify.breakpoint.sm || $vuetify.breakpoint.xs"
                          >
                            <v-icon
                              dense
                              :small="$vuetify.breakpoint.md"
                              :x-small="$vuetify.breakpoint.md || $vuetify.breakpoint.sm || $vuetify.breakpoint.xs"
                              class="pa-0 ma-0"
                            >
                              mdi-chart-timeline-variant
                            </v-icon>
                          </v-btn>
                        </v-col>
                        <v-col cols="auto" v-if="$store.getters.user.permissions.includes('ARCHIVAR TRÁMITE') && item.has_flowed && item.owner && !item.archived">
                          <v-btn
                            color="warning"
                            @click="$refs.dialogProcedureArchive.showDialog(item)"
                            dark
                            class="py-xl-6 py-lg-6 py-md-0 py-sm-0 py-xs-0 px-0 mx-0 my-1"
                            :small="$vuetify.breakpoint.xl || $vuetify.breakpoint.lg"
                            :x-small="$vuetify.breakpoint.md || $vuetify.breakpoint.sm || $vuetify.breakpoint.xs"
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
                        </v-col>
                        <v-col cols="auto" v-if="!item.has_flowed && item.owner && !item.archived && $store.getters.user.permissions.includes('ELIMINAR TRÁMITE')">
                          <v-btn
                            color="warning"
                            @click="$refs.dialogProcedureDelete.showDialog(item)"
                            dark
                            class="py-xl-6 py-lg-6 py-md-0 py-sm-0 py-xs-0 px-0 mx-0 my-1"
                            :small="$vuetify.breakpoint.xl || $vuetify.breakpoint.lg"
                            :x-small="$vuetify.breakpoint.md || $vuetify.breakpoint.sm || $vuetify.breakpoint.xs"
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
                        </v-col>
                      </v-row>
                    </td>
                  </tr>
                </tbody>
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
  beforeDestroy() {
    this.$mqtt.unsubscribe(this.topic)
  },
  mqtt: {
    'procedures/received/area/+' (data, topic) {
      if (topic == this.topic) {
        bus.$emit('updateProcedureNotification', Number(String.fromCharCode.apply(null, data)))
      }
    }
  },
  computed: {
    topic() {
      return `procedures/received/area/${this.$store.getters.user.area_id}`
    },
    tableHeaders() {
      const headers = this.headers.slice(0, -1)
      return headers
    },
    actionHeader() {
      const header = this.headers.splice(-1)
      return header[0]
    },
    rowHeight() {
      switch (this.$vuetify.breakpoint.name) {
        case 'xs': return '65px'
        case 'sm': return '55px'
        case 'md': return '45px'
        case 'lg': return '80px'
        case 'xl': return '90px'
        default: return '45px'
      }
    },
    fontSize() {
      switch (this.$vuetify.breakpoint.name) {
        case 'xs': return '8px'
        case 'sm': return '8px'
        case 'md': return '8px'
        case 'lg': return '14px'
        case 'xl': return '16px'
        default: return '12px'
      }
    },
    headers() {
      let headers = [
        {
          text: 'Fecha de ingreso',
          align: 'center',
          sortable: false,
          value: 'incoming_at',
          // width: '8%',
        }, {
          text: 'Sección/Origen',
          align: 'center',
          sortable: false,
          value: 'from_area',
          // width: '10%',
        }, {
          text: 'Código de hoja de ruta',
          align: 'center',
          sortable: false,
          value: 'code',
          // width: '10%',
        }, {
          text: 'Tipo de trámite',
          align: 'center',
          sortable: false,
          value: 'procedure_type_id',
          // width: '10%',
        }, {
          text: 'Procedencia',
          align: 'center',
          sortable: false,
          value: 'origin',
          // width: '10%',
        }, {
          text: 'Detalle/Asunto',
          align: 'center',
          sortable: false,
          value: 'detail',
          // width: '12%',
        }, {
          text: 'Sección/Destino',
          align: 'center',
          sortable: false,
          value: 'to_area',
          // width: '10%',
        }, {
          text: 'Fecha de derivación',
          align: 'center',
          sortable: false,
          value: 'outgoing_at',
          // width: '8%',
        }, {
          text: 'Acciones',
          align: 'center',
          sortable: false,
          value: 'actions',
          // width: '8%',
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
    table.setAttribute('width', '100%')
    this.$mqtt.subscribe(this.topic)
    console.log(`Suscrito a socket: ${this.topic}`)
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