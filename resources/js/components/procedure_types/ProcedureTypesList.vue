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
          <v-card-text>
            <v-data-table
              :headers="headers"
              :items="procedureTypes"
              :options.sync="options"
              :server-items-length="totalProcedureTypes"
              :loading="loading"
              :footer-props="{
                itemsPerPageOptions: [8, 15, 30]
              }"
              id="datatable"
            >
              <template v-slot:[`item.requirements`]="{ item }" v-if="requirements.length">
                <ol class="my-2">
                  <li
                    v-for="requirementId in item.requirements"
                    :key="requirementId"
                  >
                    {{ requirements.find(o => o.id === requirementId).name }}
                  </li>
                </ol>
              </template>
              <template v-slot:[`item.actions`]="{ item }">
                <v-container style="width: 10em;">
                  <v-row dense no-gutters justify="space-around">
                    <v-col cols="auto">
                      <v-tooltip bottom>
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn
                            v-bind="attrs"
                            v-on="on"
                            color="yellow"
                            class="py-6"
                            small
                            @click="$refs.dialogProcedureTypeForm.showDialog(item)"
                          >
                            <v-icon>
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
                            class="py-6"
                            small
                            @click="$refs.dialogProcedureTypeDelete.showDialog(item)"
                          >
                            <v-icon>
                              mdi-delete
                            </v-icon>
                          </v-btn>
                        </template>
                        <span>Eliminar trámite</span>
                      </v-tooltip>
                    </v-col>
                  </v-row>
                </v-container>
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
          text: 'Nombre',
          align: 'center',
          sortable: true,
          value: 'name',
        }, {
          text: 'Requisitos',
          align: 'center',
          sortable: false,
          value: 'requirements',
        }, {
          text: 'Código',
          align: 'center',
          sortable: false,
          value: 'code',
        }, {
          text: 'Acciones',
          align: 'center',
          value: 'actions',
          sortable: false,
          width: '10em',
        },
      ],
    }
  },
  mounted() {
    const table = document.getElementById('datatable').getElementsByTagName('table')[0]
    table.setAttribute('class', 'datatables')
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
        console.log(error)
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
        console.log(error)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>