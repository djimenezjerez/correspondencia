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
                Users
              </v-toolbar-title>
              <template v-slot:extension>
                <v-btn
                  fab
                  color="success"
                  bottom
                  right
                  absolute
                  @click="dialog = !dialog"
                >
                  <v-icon>mdi-plus</v-icon>
                </v-btn>
              </template>
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
              ></v-data-table>
            </v-card-text>
          </v-card>
        </v-flex>
      </v-layout>
    </v-container>
  </v-main>
</template>

<script>
export default {
  name: 'Users',
  data: function() {
    return {
      loading: false,
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
          text: 'NAME',
          align: 'start',
          sortable: true,
          value: 'name',
        }, {
          text: 'USSERNAME',
          align: 'start',
          sortable: true,
          value: 'username',
        },
      ],
    }
  },
  created() {
    this.fetchUsers()
  },
  watch: {
    options: function(newVal, oldVal) {
      if (newVal.page != oldVal.page || newVal.itemsPerPage != oldVal.itemsPerPage || newVal.sortBy != oldVal.sortBy || newVal.sortDesc != oldVal.sortDesc) {
        this.fetchUsers()
      }
    },
  },
  methods: {
    async fetchUsers() {
      console.log(this.options)
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
        this.loading = false
      } catch(error) {
        console.log(error)
        this.loading = true
      }
    }
  }
}
</script>