<template>
  <div>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-toolbar
            color="secondary"
            dark
          >
            <ToolBarTitle title="Usuarios"/>
            <v-spacer></v-spacer>
            <v-divider class="mx-5" vertical></v-divider>
            <AddButton text="Agregar usuario" @click="$refs.dialogUserForm.showDialog()"/>
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
              :items="users"
              :options.sync="options"
              :server-items-length="totalUsers"
              :loading="loading"
              :footer-props="{
                itemsPerPageOptions: [8, 15, 30]
              }"
              id="datatable"
            >
              <template v-slot:[`item.document_type_id`]="{ item }">
                {{ documentType(item.document_type_id) }}
              </template>
              <template v-slot:[`item.area_id`]="{ item }">
                {{ area(item.area_id) }}
              </template>
              <template v-slot:[`item.is_active`]="{ item }">
                {{ item.is_active ? 'ACTIVO' : 'INACTIVO' }}
              </template>
              <template v-slot:[`item.actions`]="{ item }">
                <v-container style="width: 10em;">
                  <v-row dense no-gutters justify="space-around">
                    <v-col cols="auto" v-if="item.is_active">
                      <v-btn
                        color="yellow"
                        class="py-6"
                        small
                        @click="$refs.dialogUserForm.showDialog(item)"
                      >
                        <v-icon>
                          mdi-pencil
                        </v-icon>
                      </v-btn>
                    </v-col>
                    <v-col cols="auto">
                      <v-btn
                        :color="item.is_active ? 'red' : 'success'"
                        :dark="item.is_active"
                        class="py-6"
                        small
                        @click="$refs.dialogUserSwitch.showDialog(item)"
                      >
                        <v-icon>
                          {{ item.is_active ? 'mdi-delete' : 'mdi-restore' }}
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
    <UserForm ref="dialogUserForm" :areas="areas" :document-types="documentTypes" v-on:updateList="fetchUsers"/>
    <UserSwitch ref="dialogUserSwitch" v-on:updateList="fetchUsers"/>
  </div>
</template>

<script>
import UserForm from '@/components/users/UserForm'
import UserSwitch from '@/components/users/UserSwitch'
import SearchInput from '@/components/shared/SearchInput'
import AddButton from '@/components/shared/AddButton'

export default {
  name: 'UsersList',
  components: {
    UserForm,
    UserSwitch,
    SearchInput,
    AddButton,
  },
  data: function() {
    return {
      loading: false,
      search: null,
      areas: [],
      documentTypes: [],
      options: {
        page: 1,
        itemsPerPage: 8,
        sortBy: ['name'],
        sortDesc: [false]
      },
      totalUsers: 0,
      users: [],
      headers: [
        {
          text: 'Nombre',
          align: 'center',
          sortable: true,
          value: 'name',
        }, {
          text: 'Apellido',
          align: 'center',
          sortable: true,
          value: 'last_name',
        }, {
          text: 'Nombre de usuario',
          align: 'center',
          sortable: true,
          value: 'username',
        }, {
          text: 'Documento de identidad',
          align: 'center',
          sortable: true,
          value: 'identity_card',
        }, {
          text: 'Tipo de documento',
          align: 'center',
          sortable: false,
          value: 'document_type_id',
        }, {
          text: 'Sección',
          align: 'center',
          sortable: false,
          value: 'area_id',
        }, {
          text: 'Dirección',
          align: 'center',
          sortable: false,
          value: 'address',
        }, {
          text: 'Teléfono',
          align: 'center',
          sortable: false,
          value: 'phone',
        }, {
          text: 'Email',
          align: 'center',
          sortable: false,
          value: 'email',
        }, {
          text: 'Estado',
          align: 'center',
          sortable: false,
          value: 'is_active',
        }, {
          text: 'Acciones',
          align: 'center',
          value: 'actions',
          sortable: false,
        },
      ],
    }
  },
  mounted() {
    const table = document.getElementById('datatable').getElementsByTagName('table')[0]
    table.setAttribute('class', 'datatables')
  },
  created() {
    this.fetchUsers()
    this.fetchAreas()
    this.fetchDocumentTypes()
  },
  watch: {
    options: function(newVal, oldVal) {
      if (newVal.page != oldVal.page || newVal.itemsPerPage != oldVal.itemsPerPage || newVal.sortBy != oldVal.sortBy || newVal.sortDesc != oldVal.sortDesc) {
        this.fetchUsers()
      }
    },
    search: function() {
      this.fetchUsers()
    }
  },
  methods: {
    documentType(value) {
      const documentType = this.documentTypes.find(o => o.id === value)
      if (documentType) {
        return documentType.code
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
    async fetchDocumentTypes() {
      try {
        this.loading = true
        let response = await axios.get('document_type')
        this.documentTypes = response.data.payload.document_types
      } catch(error) {
        console.log(error)
      } finally {
        this.loading = false
      }
    },
    async fetchUsers() {
      try {
        this.loading = true
        let response = await axios.get('user', {
          params: {
            page: this.options.page,
            per_page: this.options.itemsPerPage,
            sort_by: this.options.sortBy,
            sort_desc: this.options.sortDesc,
            search: this.search,
          },
        })
        this.users = response.data.payload.data
        this.totalUsers = response.data.payload.total
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