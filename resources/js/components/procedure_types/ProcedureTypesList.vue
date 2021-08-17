<template>
  <v-main>
    <v-container grid-list-md>
      <v-layout wrap>
        <v-flex xs12>
          <v-card>
            <v-toolbar
              color="secondary"
              dark
            >
              <v-toolbar-title>
                Trámites
              </v-toolbar-title>
              <v-spacer></v-spacer>
              <v-text-field
                dark
                label="Buscar"
                v-model="search"
                prepend-icon="mdi-magnify"
                filled
                outlined
                single-line
                clearable
                class="mt-8 shrink"
              ></v-text-field>
              <v-divider class="mx-10" vertical></v-divider>
              <v-btn
                outlined
                x-large
                dark
                @click.stop="$refs.dialogProcedureTypeForm.showDialog()"
              >
                <v-icon
                  class="mr-3"
                >
                  mdi-plus
                </v-icon>
                Agregar trámite
              </v-btn>
            </v-toolbar>
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
                class="elevation-1"
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
                  <v-tooltip bottom>
                    <template #activator="{ on }">
                      <v-icon
                        class="mr-2"
                        color="info"
                        v-on="on"
                        @click="$refs.dialogProcedureTypeForm.showDialog(item)"
                      >
                        mdi-pencil
                      </v-icon>
                    </template>
                    <span>Editar</span>
                  </v-tooltip>
                  <v-tooltip bottom v-if="item.total_procedures == 0">
                    <template #activator="{ on }">
                      <v-icon
                        class="mr-2"
                        color="error"
                        v-on="on"
                        @click="$refs.dialogProcedureTypeDelete.showDialog(item)"
                      >
                        mdi-close
                      </v-icon>
                    </template>
                    <span>Eliminar</span>
                  </v-tooltip>
                </template>
              </v-data-table>
            </v-card-text>
          </v-card>
        </v-flex>
      </v-layout>
    </v-container>
    <ProcedureTypeForm :requirements="requirements" ref="dialogProcedureTypeForm" v-on:updateList="fetchProcedureTypes"/>
    <ProcedureTypeDelete ref="dialogProcedureTypeDelete" v-on:updateList="fetchProcedureTypes"/>
  </v-main>
</template>

<script>
import ProcedureTypeForm from '@/components/procedure_types/ProcedureTypeForm'
import ProcedureTypeDelete from '@/components/procedure_types/ProcedureTypeDelete'

export default {
  name: 'ProcedureTypesList',
  components: {
    ProcedureTypeForm,
    ProcedureTypeDelete
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
          align: 'start',
          sortable: true,
          value: 'name',
        }, {
          text: 'Requisitos',
          align: 'start',
          sortable: false,
          value: 'requirements',
        }, {
          text: 'Acciones',
          align: 'center',
          value: 'actions',
          sortable: false
        },
      ],
    }
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
    search: function(value) {
      if (value == null || value.length >= 3 || value.length == 0) {
        this.fetchProcedureTypes()
      }
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