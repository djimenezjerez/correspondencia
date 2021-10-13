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
        <ToolBarTitle :title="edit ? 'Editar hoja de ruta' : 'Agregar hoja de ruta'"/>
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
        <validation-observer ref="procedureObserver" v-slot="{ invalid }">
          <form v-on:submit.prevent="submit">
            <v-card-text>
              <v-text-field
                label="Fecha de recepción"
                :value="$moment(procedureForm.created_at || new Date()).format('L')"
                prepend-icon="mdi-calendar"
                outlined
                readonly
              ></v-text-field>
              <validation-provider
                v-slot="{ errors }"
                name="procedure_type_id"
                rules="required"
              >
                <v-select
                  :items="procedureTypes"
                  item-text="name"
                  item-value="id"
                  label="Tipo de trámite"
                  v-model="procedureForm.procedure_type_id"
                  data-vv-name="procedure_type_id"
                  :error-messages="errors"
                  prepend-icon="mdi-paperclip"
                ></v-select>
              </validation-provider>
              <validation-provider
                v-slot="{ errors }"
                name="code"
                rules="required|min:2"
              >
                <v-text-field
                  label="Código de hoja de ruta"
                  v-model="procedureForm.code"
                  data-vv-name="code"
                  :error-messages="errors"
                  prepend-icon="mdi-barcode-scan"
                  ref="codeField"
                  clearable
                ></v-text-field>
              </validation-provider>
              <validation-provider
                v-slot="{ errors }"
                name="origin"
                rules="required|min:3"
              >
                <v-text-field
                  label="Procedencia"
                  v-model="procedureForm.origin"
                  data-vv-name="origin"
                  :error-messages="errors"
                  prepend-icon="mdi-email-open"
                  ref="originField"
                ></v-text-field>
              </validation-provider>
              <validation-provider
                v-slot="{ errors }"
                name="detail"
                rules="required|min:3"
              >
                <v-text-field
                  label="Detalle/Asunto"
                  v-model="procedureForm.detail"
                  data-vv-name="detail"
                  :error-messages="errors"
                  prepend-icon="mdi-message-processing"
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
  name: 'ProcedureForm',
  props: {
    procedureTypes: {
      type: Array,
      required: true,
    },
  },
  data: function() {
    return {
      dialog: false,
      edit: false,
      procedureForm: {
        id: null,
        code: '',
        origin: '',
        detail: '',
        procedure_type_id: null,
        created_at: null,
      },
      loading: false,
    }
  },
  computed: {
    procedureTypeSelected() {
      return this.procedureForm.procedure_type_id
    },
  },
  watch: {
    procedureTypeSelected(value) {
      if (this.procedureForm.code == null || this.procedureForm.code == '') {
        this.fetchCode(value)
        this.$nextTick(() => {
          this.$refs.originField.$refs.input.focus()
        })
      } else {
        this.$nextTick(() => {
          this.$refs.codeField.$refs.input.focus()
        })
      }
    },
  },
  methods: {
    async fetchCode(value) {
      if (this.procedureForm.procedure_type_id) {
        try {
          this.loading = true
          let response = await axios.get(`procedure_type/${value}/code`)
          this.procedureForm.code = response.data.payload.code
        } catch(error) {
          console.error(error)
        } finally {
          this.loading = false
        }
      }
    },
    showDialog(procedure = null) {
      if (procedure) {
        this.edit = true
        this.procedureForm = {
          ...procedure
        }
      } else {
        this.edit = false
        this.procedureForm = {
          id: null,
          code: '',
          origin: '',
          detail: '',
          procedure_type_id: null,
          created_at: null,
        }
      }
      this.dialog = true
      this.$nextTick(() => {
        this.$refs.procedureObserver.reset()
      })
    },
    async submit() {
      try {
        let valid = await this.$refs.procedureObserver.validate()
        if (valid) {
          this.loading = true
          if (this.edit) {
            const response = await axios.patch(`procedure/${this.procedureForm.id}`, Object.fromEntries(Object.entries(this.procedureForm).filter(([_, v]) => v != '')))
            this.$toast.success(response.data.message)
          } else {
            const response = await axios.post('procedure', this.procedureForm)
            this.$toast.success(response.data.message)
          }
          this.$emit('updateList')
          this.dialog = false
        }
      } catch(error) {
        this.$refs.procedureObserver.reset()
        if ('errors' in error.response.data) {
          this.$refs.procedureObserver.setErrors(error.response.data.errors)
        }
      } finally {
        this.loading = false
      }
    }
  },
}
</script>