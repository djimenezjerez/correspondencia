<template>
  <v-dialog
    v-model="dialog"
    persistent
    max-width="600"
    @keydown.esc="closeDialog"
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
        <ToolBarTitle :title="edit ? 'Editar Trámite' : 'Agregar Trámite'"/>
        <v-spacer></v-spacer>
        <v-btn
          icon
          @click.stop="closeDialog"
        >
          <v-icon>
            mdi-close
          </v-icon>
        </v-btn>
      </v-toolbar>
      <div class="px-5 pb-5">
        <validation-observer ref="procedureTypeObserver" v-slot="{ invalid }">
          <form v-on:submit.prevent="submit">
            <v-card-text>
              <validation-provider
                v-slot="{ errors }"
                name="name"
                rules="required|min:3"
              >
                <v-text-field
                  label="Nombre"
                  v-model="procedureTypeForm.name"
                  data-vv-name="name"
                  :error-messages="errors"
                  prepend-icon="mdi-paperclip"
                  autofocus
                ></v-text-field>
              </validation-provider>
              <validation-provider
                v-slot="{ errors }"
                name="code"
                rules="required|min:2"
              >
                <v-text-field
                  label="Código"
                  v-model="procedureTypeForm.code"
                  data-vv-name="code"
                  :error-messages="errors"
                  prepend-icon="mdi-subtitles-outline"
                ></v-text-field>
              </validation-provider>
              <v-autocomplete
                clearable
                v-model="search"
                label="Añadir Requisito"
                :items="filteredRequirements"
                item-value="id"
                item-text="name"
                prepend-icon="mdi-card-account-details"
                @change="addRequirement"
              ></v-autocomplete>
              <v-simple-table>
                <template v-slot:default>
                  <thead>
                    <tr>
                      <th class="text-left">
                        #
                      </th>
                      <th class="text-left">
                        Requisitos
                      </th>
                      <th class="text-center">
                        Acciones
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="(requirementId, index) in procedureTypeForm.requirements"
                      :key="requirementId"
                    >
                      <td>{{ index + 1 }}</td>
                      <td>{{ requirements.find(o => o.id == requirementId).name }}</td>
                      <td class="text-center">
                        <v-btn icon>
                          <v-icon
                            color="error"
                            @click="removeRequirement(index)"
                          >
                            mdi-close
                          </v-icon>
                        </v-btn>
                      </td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
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
  name: 'ProcedureType',
  data: function() {
    return {
      dialog: false,
      edit: false,
      procedureTypeForm: {
        null: null,
        name: '',
        code: '',
        requirements: [],
      },
      search: '',
      loading: false,
    }
  },
  props: {
    requirements: {
      type: Array,
      required: true,
    },
  },
  computed: {
    filteredRequirements() {
      return this.requirements.filter(o => !this.procedureTypeForm.requirements.includes(o.id))
    },
  },
  methods: {
    removeRequirement(index) {
      this.procedureTypeForm.requirements.splice(index, 1)
      this.$nextTick(() => {
        this.search = ''
      })
    },
    addRequirement(value) {
      if (value != '' && value != null) {
        this.procedureTypeForm.requirements.unshift(value)
        this.$nextTick(() => {
          this.search = ''
        })
      }
    },
    closeDialog() {
      this.dialog = false
      this.$emit('updateList')
    },
    showDialog(procedureType = null) {
      if (procedureType) {
        this.edit = true
        this.procedureTypeForm = {
          ...procedureType
        }
      } else {
        this.edit = false
        this.procedureTypeForm = {
          null: null,
          name: '',
          code: '',
          requirements: [],
        }
      }
      this.dialog = true
      this.$nextTick(() => {
        this.search = ''
        this.$refs.procedureTypeObserver.reset()
      })
    },
    async submit() {
      try {
        let valid = await this.$refs.procedureTypeObserver.validate()
        if (valid) {
          this.loading = true
          if (this.edit) {
            const response = await axios.patch(`procedure_type/${this.procedureTypeForm.id}`, Object.fromEntries(Object.entries(this.procedureTypeForm).filter(([_, v]) => v != '')))
            this.$toast.success(response.data.message)
            this.$emit('updateList')
          } else {
            const response = await axios.post('procedure_type', this.procedureTypeForm)
            this.$toast.success(response.data.message)
            this.$emit('updateList')
          }
          this.dialog = false
        }
      } catch(error) {
        this.$refs.procedureTypeObserver.reset()
        if ('errors' in error.response.data) {
          this.$refs.procedureTypeObserver.setErrors(error.response.data.errors)
        }
      } finally {
        this.loading = false
      }
    }
  },
}
</script>