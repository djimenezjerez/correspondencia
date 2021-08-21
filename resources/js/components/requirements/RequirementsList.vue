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
              Requisitos
            </v-toolbar-title>
            <v-spacer></v-spacer>
            <v-divider class="mx-5" vertical></v-divider>
            <AddButton text="Agregar requisito" @click="$refs.dialogRequirementForm.showDialog()"/>
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
              :items="requirements"
              :options.sync="options"
              :server-items-length="totalRequirements"
              :loading="loading"
              :footer-props="{
                itemsPerPageOptions: [8, 15, 30]
              }"
              id="datatable"
            >
              <template v-slot:[`item.actions`]="{ item }">
                <v-container style="width: 20em;">
                  <v-row dense justify="space-around">
                    <v-col cols="auto">
                      <v-btn
                        dark
                        color="blue"
                        @click="$refs.dialogRequirementForm.showDialog(item)"
                      >
                        Editar
                        <v-icon right>
                          mdi-pencil
                        </v-icon>
                      </v-btn>
                    </v-col>
                    <v-col cols="auto" v-if="!item.is_used">
                      <v-btn
                        dark
                        color="red"
                        @click="$refs.dialogRequirementDelete.showDialog(item)"
                      >
                        Eliminar
                        <v-icon right>
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
    <RequirementForm ref="dialogRequirementForm" v-on:updateList="fetchRequirements"/>
    <RequirementDelete ref="dialogRequirementDelete" v-on:updateList="fetchRequirements"/>
  </div>
</template>

<script>
import RequirementForm from '@/components/requirements/RequirementForm'
import RequirementDelete from '@/components/requirements/RequirementDelete'
import SearchInput from '@/components/shared/SearchInput'
import AddButton from '@/components/shared/AddButton'

export default {
  name: 'RequirementsList',
  components: {
    RequirementForm,
    RequirementDelete,
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
          sortable: false,
          width: '20em',
        },
      ],
    }
  },
  mounted() {
    const table = document.getElementById('datatable').getElementsByTagName('table')[0]
    table.setAttribute('class', 'datatables')
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
    search: function() {
      this.fetchRequirements()
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