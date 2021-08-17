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
                <div>Libro de registro de correspondencia</div>
                <div>{{ currentArea }}</div>
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
              >
                <v-icon
                  class="mr-3"
                >
                  mdi-plus
                </v-icon>
                Agregar hoja de ruta
              </v-btn>
            </v-toolbar>
            <v-card-text>
              <v-data-table
                :headers="headers"
                :items="procedures"
                :options.sync="options"
                :server-items-length="totalProcedures"
                :loading="loading"
                :footer-props="{
                  itemsPerPageOptions: [8, 15, 30]
                }"
                class="elevation-1"
              >
              </v-data-table>
            </v-card-text>
          </v-card>
        </v-flex>
      </v-layout>
    </v-container>
  </v-main>
</template>

<script>
export default {
  name: 'ProceduresList',
  data: function() {
    return {
      loading: false,
      search: null,
      options: {
        page: 1,
        itemsPerPage: 8,
        sortBy: ['updated_at'],
        sortDesc: [false]
      },
      totalProcedures: 0,
      areas: [],
      procedures: [],
      headers: [
        {
          text: 'Hoja de ruta',
          align: 'start',
          sortable: true,
          value: 'code',
        },
      ],
    }
  },
  computed: {
    currentArea() {
      const area = this.areas.find(o => o.id == this.$store.getters.user.area_id)
      if (area) {
        return area.name
      } else {
        return ''
      }
    }
  },
  created() {
    this.fetchAreas()
    this.fetchProcedures()
  },
  watch: {
    options: function(newVal, oldVal) {
      if (newVal.page != oldVal.page || newVal.itemsPerPage != oldVal.itemsPerPage || newVal.sortBy != oldVal.sortBy || newVal.sortDesc != oldVal.sortDesc) {
        this.fetchProcedures()
      }
    },
    search: function(value) {
      if (value == null || value.length >= 3 || value.length == 0) {
        this.fetchProcedures()
      }
    }
  },
  methods: {
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
    async fetchProcedures() {
      try {
        this.loading = true
        let response = await axios.get('procedure', {
          params: {
            page: this.options.page,
            per_page: this.options.itemsPerPage,
            sort_by: this.options.sortBy,
            sort_desc: this.options.sortDesc,
            search: this.search,
          },
        })
        this.procedures = response.data.payload.data
        this.totalProcedures = response.data.payload.total
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