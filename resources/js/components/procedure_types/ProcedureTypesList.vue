<template>
  <div>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-toolbar
            color="secondary"
            dark
          >
            <ToolBarTitle title="Trámites"/>
            <v-spacer></v-spacer>
            <v-divider class="mx-5" vertical></v-divider>
            <AddButton text="Agregar trámite" @click="$refs.dialogProcedureTypeForm.showDialog()"/>
          </v-toolbar>
          <v-row
            align="end"
            justify="end"
            class="mt-1"
          >
            <v-col cols="auto" md="3" sm="4" xs="12">
              <SearchInput v-model="search"/>
            </v-col>
          </v-row>
          <v-card-text class="pt-0 mt-0">
            <v-data-table
              :headers="headers"
              :items="procedureTypes"
              :options.sync="options"
              :server-items-length="totalProcedureTypes"
              :loading="loading"
              :footer-props="{
                itemsPerPageOptions: [8, 15, 30]
              }"
              mobile-breakpoint="0"
              dense
              id="datatable"
            >
              <template v-slot:[`item.requirements`]="{ item }" v-if="requirements.length">
                <ol class="my-2" v-if="item.requirements.length > 0">
                  <li
                    v-for="requirementId in item.requirements"
                    :key="requirementId"
                  >
                    {{ requirements.find(o => o.id === requirementId).name }}
                  </li>
                </ol>
                <div v-else>
                  NINGUNO
                </div>
              </template>
              <template v-slot:[`item.actions`]="{ item }">
                <v-row dense no-gutters justify="space-around" align="center">
                  <v-col cols="auto">
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          v-bind="attrs"
                          v-on="on"
                          color="yellow"
                          class="px-0 py-4 mx-0 my-1"
                          @click="$refs.dialogProcedureTypeForm.showDialog(item)"
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
                      </template>
                      <span>Editar trámite</span>
                    </v-tooltip>
                  </v-col>
                  <v-col cols="auto" v-if="item.total_procedures == 0">
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          v-bind="attrs"
                          v-on="on"
                          dark
                          color="red"
                          class="px-0 py-4 mx-0 my-1"
                          @click="$refs.dialogProcedureTypeDelete.showDialog(item)"
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
                      </template>
                      <span>Eliminar trámite</span>
                    </v-tooltip>
                  </v-col>
                </v-row>
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <ProcedureTypeForm :requirements="requirements" ref="dialogProcedureTypeForm" v-on:updateList="fetchProcedureTypes"/>
    <ProcedureTypeDelete ref="dialogProcedureTypeDelete" v-on:updateList="fetchProcedureTypes"/>
  </div>
</template>

<script>
import ProcedureTypeForm from '@/components/procedure_types/ProcedureTypeForm'
import ProcedureTypeDelete from '@/components/procedure_types/ProcedureTypeDelete'
import SearchInput from '@/components/shared/SearchInput'
import AddButton from '@/components/shared/AddButton'

export default {
  name: 'ProcedureTypesList',
  components: {
    ProcedureTypeForm,
    ProcedureTypeDelete,
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
        sortBy: ['name'],
        sortDesc: [false]
      },
      totalProcedureTypes: 0,
      requirements: [],
      procedureTypes: [],
      headers: [
        {
          text: 'NOMBRE',
          align: 'center',
          sortable: false,
          value: 'name',
        }, {
          text: 'REQUISITOS',
          align: 'center',
          sortable: false,
          value: 'requirements',
        }, {
          text: 'CÓDIGO',
          align: 'center',
          sortable: false,
          value: 'code',
        }, {
          text: 'ACCIONES',
          align: 'center',
          value: 'actions',
          sortable: false,
          width: '13%',
        },
      ],
    }
  },
  mounted() {
    const table = document.getElementById('datatable').getElementsByTagName('table')[0]
    table.setAttribute('class', 'datatables')
    table.setAttribute('width', '100%')
  },
  created() {
    this.fetchProcedureTypes()
    this.fetchRequirements()
  },
  watch: {
    options: function(newVal, oldVal) {
      if (newVal.page != oldVal.page || newVal.itemsPerPage != oldVal.itemsPerPage || newVal.sortBy != oldVal.sortBy || newVal.sortDesc != oldVal.sortDesc) {
        this.fetchProcedureTypes()
      }
    },
    search: function() {
      this.fetchProcedureTypes()
    }
  },
  methods: {
    async fetchRequirements() {
      try {
        this.loading = true
        let response = await axios.get('requirement', {
          params: {
            all: true
          }
        })
        this.requirements = response.data.payload.requirements
      } catch(error) {
        console.error(error)
      } finally {
        this.loading = false
      }
    },
    async fetchProcedureTypes() {
      try {
        this.loading = true
        let response = await axios.get('procedure_type', {
          params: {
            page: this.options.page,
            per_page: this.options.itemsPerPage,
            sort_by: this.options.sortBy,
            sort_desc: this.options.sortDesc,
            search: this.search,
          },
        })
        this.procedureTypes = response.data.payload.data
        this.totalProcedureTypes = response.data.payload.total
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