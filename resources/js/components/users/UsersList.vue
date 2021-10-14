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
            class="mt-1 px-4 pb-0 mb-0"
            dense
          >
            <v-col xl="9" lg="9" md="9" sm="8" xs="12" class="pb-0 mb-0">
            </v-col>
            <v-col xl="3" lg="3" md="3" sm="4" xs="12" class="pb-0 mb-0">
              <SearchInput v-model="search"/>
            </v-col>
          </v-row>
          <v-card-text class="pt-0 mt-0">
            <v-data-table
              :headers="headers"
              :items="users"
              :options.sync="options"
              :server-items-length="totalUsers"
              :loading="loading"
              :footer-props="{
                itemsPerPageOptions: [8, 15, 30]
              }"
              mobile-breakpoint="0"
              dense
              id="datatable"
            >
              <template v-slot:[`item.area_id`]="{ item }">
                {{ area(item.area_id) }}
              </template>
              <template v-slot:[`item.is_active`]="{ item }">
                {{ item.is_active ? 'ACTIVO' : 'INACTIVO' }}
              </template>
              <template v-slot:[`item.actions`]="{ item }">
                <v-row dense no-gutters justify="space-around" align="center">
                  <v-col cols="auto" v-if="item.is_active">
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          v-bind="attrs"
                          v-on="on"
                          color="yellow"
                          class="px-0 py-4 mx-0 my-1"
                          @click="$refs.dialogUserForm.showDialog(item)"
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
                      <span>Editar Usuario</span>
                    </v-tooltip>
                  </v-col>
                  <v-col cols="auto">
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          v-bind="attrs"
                          v-on="on"
                          :color="item.is_active ? 'red' : 'success'"
                          :dark="item.is_active"
                          class="px-0 py-4 mx-0 my-1"
                          @click="$refs.dialogUserSwitch.showDialog(item)"
                          :small="$vuetify.breakpoint.xl || $vuetify.breakpoint.lg"
                          :x-small="$vuetify.breakpoint.md || $vuetify.breakpoint.sm || $vuetify.breakpoint.xs"
                        >
                          <v-icon
                            dense
                            :small="$vuetify.breakpoint.md"
                            :x-small="$vuetify.breakpoint.md || $vuetify.breakpoint.sm || $vuetify.breakpoint.xs"
                            class="pa-0 ma-0"
                          >
                            {{ item.is_active ? 'mdi-delete' : 'mdi-restore' }}
                          </v-icon>
                        </v-btn>
                      </template>
                      <span>{{ item.is_active ? 'Desactivar usuario' : 'Reactivar usuario' }}</span>
                    </v-tooltip>
                  </v-col>
                </v-row>
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <UserForm ref="dialogUserForm" :areas="areas" v-on:updateList="fetchUsers"/>
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
          text: 'NOMBRE',
          align: 'center',
          sortable: false,
          value: 'name',
        }, {
          text: 'APELLIDO',
          align: 'center',
          sortable: false,
          value: 'last_name',
        }, {
          text: 'NOMBRE DE USUARIO',
          align: 'center',
          sortable: false,
          value: 'username',
        }, {
          text: 'DOCUMENTO DE IDENTIDAD',
          align: 'center',
          sortable: false,
          value: 'identity_card',
        }, {
          text: 'SECCIÓN',
          align: 'center',
          sortable: false,
          value: 'area_id',
        }, {
          text: 'DIRECCIÓN',
          align: 'center',
          sortable: false,
          value: 'address',
        }, {
          text: 'TELÉFONO',
          align: 'center',
          sortable: false,
          value: 'phone',
        }, {
          text: 'EMAIL',
          align: 'center',
          sortable: false,
          value: 'email',
        }, {
          text: 'ESTADO',
          align: 'center',
          sortable: false,
          value: 'is_active',
        }, {
          text: 'ACCIONES',
          align: 'center',
          value: 'actions',
          sortable: false,
          width: '12%',
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
    this.fetchUsers()
    this.fetchAreas()
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
        console.error(error)
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
        console.error(error)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>
