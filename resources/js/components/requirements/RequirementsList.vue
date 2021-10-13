<template>
  <div>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-toolbar
            color="secondary"
            dark
          >
            <ToolBarTitle title="Requisitos"/>
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
          <v-card-text class="pt-0 mt-0">
            <v-data-table
              :headers="headers"
              :items="requirements"
              :options.sync="options"
              :server-items-length="totalRequirements"
              :loading="loading"
              :footer-props="{
                itemsPerPageOptions: [8, 15, 30]
              }"
              mobile-breakpoint="0"
              dense
              id="datatable"
            >
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
                          @click="$refs.dialogRequirementForm.showDialog(item)"
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
                      <span>Editar requisito</span>
                    </v-tooltip>
                  </v-col>
                  <v-col cols="auto" v-if="!item.is_used">
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          v-bind="attrs"
                          v-on="on"
                          color="red"
                          class="px-0 py-4 mx-0 my-1"
                          dark
                          @click="$refs.dialogRequirementDelete.showDialog(item)"
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
                      <span>Eliminar requisito</span>
                    </v-tooltip>
                  </v-col>
                </v-row>
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
          text: 'NOMBRE',
          align: 'center',
          sortable: false,
          value: 'name',
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
        console.error(error)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>