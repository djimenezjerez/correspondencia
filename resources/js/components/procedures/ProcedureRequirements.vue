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
        <ToolBarTitle title="Revisión de requisitos"/>
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
      <v-simple-table class="mt-3">
        <template v-slot:default>
          <tbody>
            <tr>
              <td class="text-right text-body-1">Hoja de ruta: </td>
              <td class="font-weight-bold text-body-1">{{ procedure.code }}</td>
            </tr>
            <tr>
              <td class="text-right text-body-1">Tipo de trámite: </td>
              <td class="font-weight-bold text-body-1">{{ procedureType.name || '' }}</td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>
      <v-divider></v-divider>
      <div class="px-5 pb-5 mt-2">
        <v-card-text>
          <div class="font-weight-bold text-body-1 text-center">
            Listado de requisitos
          </div>
          <v-simple-table>
            <template v-slot:default>
              <thead>
                <tr>
                  <th>Requisito</th>
                  <th class="text-center">Validación</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="requirement in requirements" :key="requirement.id">
                  <td>{{ requirement.name }}</td>
                  <td class="text-center">
                    <v-btn
                      text
                      icon
                      :color="requirement.validated ? 'success darken-1' : 'warning darken-1'"
                      @click.stop="switchRequirement(requirement.id)"
                      :disabled="!requirement.editable"
                    >
                      <v-icon>{{ requirement.validated ? 'mdi-check' : 'mdi-close' }}</v-icon>
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
            color="info"
            :disabled="loading"
            @click="submit"
          >
            <v-icon left>
              mdi-check
            </v-icon>
            <div v-if="procedure.verified === null && !validProcedure">
              Enviar a regularización
            </div>
            <div v-else>
              Guardar
            </div>
          </v-btn>
        </v-card-actions>
      </div>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  name: 'ProcedureRequirements',
  props: {
    procedureTypes: {
      type: Array,
      required: true,
    }
  },
  data: function() {
    return {
      dialog: false,
      loading: false,
      procedure: {
        procedure_type_id: null,
      },
      requirements: [],
    }
  },
  computed: {
    validProcedure() {
      return this.requirements.every(o => o.validated)
    },
    procedureType() {
      return this.procedureTypes.find(o => o.id == this.procedure.procedure_type_id)
    }
  },
  methods: {
    showDialog(procedure) {
      this.procedure = {
        ...procedure
      }
      this.fetchProcedureRequirements(procedure.id)
      this.dialog = true
    },
    switchRequirement(requirementId) {
      const index = this.requirements.findIndex(o => o.id == requirementId)
      this.requirements[index].validated = !this.requirements[index].validated
    },
    async fetchProcedureRequirements(procedureId) {
      try {
        this.loading = true
        let response = await axios.get(`procedure/${procedureId}/requirement`)
        response.data.payload.procedure.requirements.forEach(requirement => {
          requirement.editable = !requirement.validated
        })
        this.requirements = response.data.payload.procedure.requirements
      } catch(error) {
        console.error(error)
      } finally {
        this.loading = false
      }
    },
    async submit() {
      try {
        this.loading = true
        const response = await axios.patch(`procedure/${this.procedure.id}/requirement`, {
          requirements: this.requirements,
        })
        this.$toast.success(response.data.message)
        this.$emit('updateList')
        this.dialog = false
      } catch(error) {
        console.error(error)
      } finally {
        this.loading = false
      }
    }
  },
}
</script>

<style lang="css" scoped>
tr:hover {
  background-color: transparent !important;
}
</style>