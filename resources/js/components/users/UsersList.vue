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
                Usuarios
              </v-toolbar-title>
              <v-spacer></v-spacer>
              <v-btn
                outlined
                x-large
                dark
                @click.stop="$refs.dialogUser.showDialog()"
              >
                <v-icon
                  class="mr-3"
                >
                  mdi-plus
                </v-icon>
                Agregar usuario
              </v-btn>
            </v-toolbar>
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
                class="elevation-1"
              >
                <template v-slot:[`item.document_type_id`]="{ item }">
                  {{ documentType(item.document_type_id) }}
                </template>
                <template v-slot:[`item.area_id`]="{ item }">
                  {{ area(item.area_id) }}
                </template>
                <template v-slot:[`item.actions`]="{ item }">
                  <v-tooltip bottom>
                    <template #activator="{ on }">
                      <v-icon
                        class="mr-2"
                        color="info"
                        v-on="on"
                        @click="$refs.dialogUser.showDialog(item)"
                      >
                        mdi-pencil
                      </v-icon>
                    </template>
                    <span>Editar</span>
                  </v-tooltip>
                  <v-icon
                    small
                  >
                    mdi-delete
                  </v-icon>
                </template>
              </v-data-table>
            </v-card-text>
          </v-card>
        </v-flex>
      </v-layout>
    </v-container>
    <UserForm ref="dialogUser" :roles="roles" :areas="areas" :document-types="documentTypes" v-on:updateList="fetchUsers"/>
  </v-main>
</template>

<script>
import UserForm from '@/components/users/UserForm'

export default {
  name: 'UsersList',
  components: {
    UserForm
  },
  data: function() {
    return {
      loading: false,
      roles: [],
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
          align: 'start',
          sortable: true,
          value: 'name',
        }, {
          text: 'Documento de identidad',
          align: 'center',
          sortable: true,
          value: 'username',
        }, {
          text: 'Tipo de documento',
          align: 'center',
          sortable: false,
          value: 'document_type_id',
        }, {
          text: 'Área',
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
          align: 'start',
          sortable: false,
          value: 'email',
        }, {
          text: 'Acciones',
          value: 'actions',
          sortable: false
        },
      ],
    }
  },
  created() {
    this.fetchUsers()
    this.fetchRoles()
    this.fetchAreas()
    this.fetchDocumentTypes()
  },
  watch: {
    options: function(newVal, oldVal) {
      if (newVal.page != oldVal.page || newVal.itemsPerPage != oldVal.itemsPerPage || newVal.sortBy != oldVal.sortBy || newVal.sortDesc != oldVal.sortDesc) {
        this.fetchUsers()
      }
    },
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
    async fetchRoles() {
      try {
        this.loading = true
        let response = await axios.get('role')
        this.roles = response.data.payload.roles
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
            sortBy: this.options.sortBy,
            sortDesc: this.options.sortDesc,
          }
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