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
                <tr v-for="requirement in procedureRequirements" :key="requirement.id">
                  <td>{{ requirement.name }}</td>
                  <td class="text-center">
                    <v-btn
                      text
                      icon
                      :color="requirement.validated ? 'success darken-1' : 'warning darken-1'"
                      @click.stop="switchRequirement(requirement.id)"
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
            color="primary"
            :disabled="loading || !validProcedure"
            @click="submit"
          >
            <v-icon left>
              mdi-check
            </v-icon>
            Guardar
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
    requirements: {
      type: Array,
      required: true,
    },
  },
  data: function() {
    return {
      dialog: false,
      loading: false,
      procedure: {},
      procedureType: {
        requirements: [],
      },
      procedureRequirements: [],
    }
  },
  computed: {
    validProcedure() {
      return this.procedureRequirements.every(o => o.validated)
    },
  },
  methods: {
    showDialog(procedure) {
      this.fetchProcedureType(procedure.procedure_type_id)
      this.procedure = {
        ...procedure
      }
      this.dialog = true
    },
    switchRequirement(requirementId) {
      const index = this.procedureRequirements.findIndex(o => o.id == requirementId)
      this.procedureRequirements[index].validated = !this.procedureRequirements[index].validated
    },
    async fetchProcedureType(procedureTypeId) {
      try {
        this.loading = true
        let response = await axios.get(`procedure_type/${procedureTypeId}`)
        this.procedureType = response.data.payload.procedure_type
        this.procedureRequirements = this.requirements.filter(o => response.data.payload.procedure_type.requirements.includes(o.id)).map(o => ({
          ...o,
          validated: false,
        }))
      } catch(error) {
        console.log(error)
      } finally {
        this.loading = false
      }
    },
    async submit() {
      try {
        if (this.validProcedure) {
          this.loading = true
          const response = await axios.patch(`procedure/${this.procedure.id}/requirement`, {
            requirements: this.procedureRequirements,
          })
          if (response.data.payload.procedure.validated) {
            this.$toast.info(response.data.message)
            this.$emit('updateList')
            this.dialog = false
          }
        }
      } catch(error) {
        console.log(error)
      } finally {
        this.loading = false
      }
    }
  },
}
</script>

<style lang="scss" scoped>
  tbody {
    tr:hover {
      background-color: transparent !important;
    }
  };
  // table {
  //   table-layout: fixed;
  //   width: 100%;
  // };
  // td {
  //   width: 50%;
  // };
</style>