<template>
  <div>
    <v-row>
      <v-col>
        <v-card>
          <v-toolbar
            color="secondary"
            dark
          >
            <v-toolbar-title>
              Usuarios
            </v-toolbar-title>
            <v-spacer></v-spacer>
            <SearchInput v-model="search"/>
            <v-divider class="mx-10" vertical></v-divider>
            <v-btn
              outlined
              :large="['xl', 'lg'].includes($vuetify.breakpoint.name)"
              dark
              @click.stop="$refs.dialogUserForm.showDialog()"
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
              <template v-slot:[`item.is_active`]="{ item }">
                {{ item.is_active ? 'ACTIVO' : 'INACTIVO' }}
              </template>
              <template v-slot:[`item.actions`]="{ item }">
                <v-row no-gutters>
                  <v-col
                    cols="6"
                  >
                    <v-tooltip bottom v-if="item.is_active">
                      <template #activator="{ on }">
                        <v-icon
                          class="mr-2"
                          color="info"
                          v-on="on"
                          @click="$refs.dialogUserForm.showDialog(item)"
                        >
                          mdi-pencil
                        </v-icon>
                      </template>
                      <span>Editar</span>
                    </v-tooltip>
                  </v-col>
                  <v-col
                    cols="6"
                  >
                    <v-tooltip bottom>
                    <template #activator="{ on }">
                      <v-icon
                        class="mr-2"
                        :color="item.is_active ? 'error' : 'success'"
                        v-on="on"
                        @click="$refs.dialogUserSwitch.showDialog(item)"
                      >
                        {{ item.is_active ? 'mdi-close' : 'mdi-restore' }}
                      </v-icon>
                    </template>
                    <span v-if="item.is_active">Desactivar</span>
                    <span v-else>Reactivar</span>
                  </v-tooltip>
                  </v-col>
                </v-row>
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

export default {
  name: 'UsersList',
  components: {
    UserForm,
    UserSwitch,
    SearchInput,
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
          text: 'Estado',
          align: 'center',
          sortable: false,
          value: 'is_active',
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