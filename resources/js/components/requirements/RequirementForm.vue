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
      <v-toolbar dense dark color="secondary">
        <ToolBarTitle :title="edit ? 'Editar requisito' : 'Agregar requisito'"/>
        <v-spacer></v-spacer>
        <v-btn
          icon
          @click.stop="dialog = false"
        >
          <v-icon>
            mdi-close
          </v-icon>
        </v-btn>
      </v-toolbar>
      <div class="px-5 pb-5">
        <validation-observer ref="requirementObserver" v-slot="{ invalid }">
          <form v-on:submit.prevent="submit">
            <v-card-text>
              <validation-provider
                v-slot="{ errors }"
                name="name"
                rules="required|min:3"
              >
                <v-text-field
                  label="Nombre"
                  v-model="requirementForm.name"
                  data-vv-name="name"
                  :error-messages="errors"
                  prepend-icon="mdi-card-account-details"
                  autofocus
                ></v-text-field>
              </validation-provider>
            </v-card-text>
            <v-card-actions>
              <v-btn
                block
                type="submit"
                color="info"
                :disabled="invalid || loading"
              >
                Guardar
              </v-btn>
            </v-card-actions>
          </form>
        </validation-observer>
      </div>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  name: 'RequirementForm',
  data: function() {
    return {
      dialog: false,
      edit: false,
      requirementForm: {
        id: null,
        name: '',
      },
      loading: false,
    }
  },
  methods: {
    showDialog(requirement = null) {
      if (requirement) {
        this.edit = true
        this.requirementForm = {
          id: null,
          ...requirement
        }
      } else {
        this.edit = false
        this.requirementForm = {
          name: '',
        }
      }
      this.dialog = true
      this.$nextTick(() => {
        this.$refs.requirementObserver.reset()
      })
    },
    async submit() {
      try {
        let valid = await this.$refs.requirementObserver.validate()
        if (valid) {
          this.loading = true
          if (this.edit) {
            const response = await axios.patch(`requirement/${this.requirementForm.id}`, Object.fromEntries(Object.entries(this.requirementForm).filter(([_, v]) => v != '')))
            this.$toast.success(response.data.message)
          } else {
            const response = await axios.post('requirement', this.requirementForm)
            this.$toast.success(response.data.message)
          }
          this.$emit('updateList')
          this.dialog = false
        }
      } catch(error) {
        this.$refs.requirementObserver.reset()
        if ('errors' in error.response.data) {
          this.$refs.requirementObserver.setErrors(error.response.data.errors)
        }
      } finally {
        this.loading = false
      }
    }
  },
}
</script>