<template>
  <v-main>
    <v-container grid-list-md>
      <v-layout wrap>
        <v-flex xs12>
          <v-card>
            <v-toolbar
              color="secondary"
              light
            >
              <v-toolbar-title class="white--text">
                Requisitos
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
              <v-divider class="mx-10" color="white" vertical></v-divider>
              <v-btn
                outlined
                x-large
                dark
                @click.stop="$refs.dialogRequirementForm.showDialog()"
              >
                <v-icon
                  class="mr-3"
                >
                  mdi-plus
                </v-icon>
                Agregar requisito
              </v-btn>
            </v-toolbar>
            <v-card-text>
              <v-data-table
                :headers="headers"
                :items="requirements"
                :options.sync="options"
                :server-items-length="totalRequirements"
                :loading="loading"
                :footer-props="{
                  itemsPerPageOptions: [8, 15, 30]
                }"
                class="elevation-1"
              >
                <template v-slot:[`item.actions`]="{ item }">
                  <v-tooltip bottom>
                    <template #activator="{ on }">
                      <v-icon
                        class="mr-2"
                        color="info"
                        v-on="on"
                        @click="$refs.dialogRequirementForm.showDialog(item)"
                      >
                        mdi-pencil
                      </v-icon>
                    </template>
                    <span>Editar</span>
                  </v-tooltip>
                  <v-tooltip bottom>
                    <template #activator="{ on }">
                      <v-icon
                        class="mr-2"
                        color="error"
                        v-on="on"
                        @click="$refs.dialogRequirementSwitch.showDialog(item)"
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
    <RequirementForm ref="dialogRequirementForm" v-on:updateList="fetchRequirements"/>
    <RequirementDelete ref="dialogRequirementSwitch" v-on:updateList="fetchRequirements"/>
  </v-main>
</template>

<script>
import RequirementForm from '@/components/requirements/RequirementForm'
import RequirementDelete from '@/components/requirements/RequirementDelete'

export default {
  name: 'RequirementsList',
  components: {
    RequirementForm,
    RequirementDelete
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
      totalRequirements: 0,
      requirements: [],
      headers: [
        {
          text: 'Nombre',
          align: 'start',
          sortable: true,
          value: 'name',
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
    this.fetchRequirements()
  },
  watch: {
    options: function(newVal, oldVal) {
      if (newVal.page != oldVal.page || newVal.itemsPerPage != oldVal.itemsPerPage || newVal.sortBy != oldVal.sortBy || newVal.sortDesc != oldVal.sortDesc) {
        this.fetchRequirements()
      }
    },
    search: function(value) {
      if (value == null || value.length >= 3 || value.length == 0) {
        this.fetchRequirements()
      }
    }
  },
  methods: {
    async fetchRequirements() {
      try {
        this.loading = true
        let response = await axios.get('requirement', {
          params: {
            page: this.options.page,
            per_page: this.options.itemsPerPage,
            sort_by: this.options.sortBy,
            sort_desc: this.options.sortDesc,
            search: this.search,
          },
        })
        this.requirements = response.data.payload.data
        this.totalRequirements = response.data.payload.total
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