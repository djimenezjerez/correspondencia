<template>
  <v-dialog
    v-model="dialog"
    persistent
    max-width="600"
    @keydown.esc="dialog = false"
  >
    <v-card
      :loading="loading"
    >
      <template slot="progress">
        <v-progress-linear
          color="secondary"
          height="10"
          indeterminate
        ></v-progress-linear>
      </template>
      <div class="px-5 pb-5">
        <v-card-text>
          <p class="text-center text-h5">
            ¿Seguro que desea eliminar el requisito
          </p>
          <p class="text-center text-h5">
            {{ requirement.name }}?
          </p>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            class="mr-2"
            color="success darken-1"
            large
            @click="deleteItem"
            :disabled="loading"
          >
            <v-icon left>
              mdi-check
            </v-icon>
            SI
          </v-btn>
          <v-btn
            class="ml-2"
            color="error"
            large
            @click="dialog = false"
            :disabled="loading"
          >
            <v-icon left>
              mdi-close
            </v-icon>
            NO
          </v-btn>
        </v-card-actions>
      </div>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  name: 'RequirementDelete',
  data: function() {
    return {
      dialog: false,
      loading: false,
      requirement: {},
    }
  },
  methods: {
    showDialog(requirement) {
      this.dialog = true
      this.requirement = requirement
    },
    async deleteItem() {
      try {
        this.loading = true
        await axios.delete(`requirement/${this.requirement.id}`)
        this.$toast.info('Requisito eliminado correctamente')
        this.$emit('updateList')
        this.dialog = false
      } catch(error) {
        console.log(error)
      } finally {
        this.loading = false
      }
    }
  },
}
</script>