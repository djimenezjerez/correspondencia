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
        <v-toolbar-title>
          Derivar hoja de ruta
        </v-toolbar-title>
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
      <div class="text-center text-xl-h5 text-lg-h6 text-md-h6 text-sm-h6 text-xs-subtitle-1 mt-2">
        {{ procedure.code }}
      </div>
      <div class="text-center text-xl-h6 text-lg-subtitle-1 text-md-subtitle-1 text-sm-subtitle-1 text-xs-body-1 font-weight-light mt-2">
        {{ procedureType }}
      </div>
      <div class="px-5 pb-5">
        <validation-observer ref="procedureFlowObserver" v-slot="{ invalid }">
          <form v-on:submit.prevent="submit">
            <v-card-text>
              <validation-provider
                v-slot="{ errors }"
                name="area_id"
                rules="required"
              >
                <v-select
                  :items="filteredAreas"
                  item-text="name"
                  item-value="id"
                  label="Sección destino"
                  v-model="selectedArea"
                  data-vv-name="area_id"
                  :error-messages="errors"
                  prepend-icon="mdi-briefcase"
                ></v-select>
              </validation-provider>
            </v-card-text>
            <v-card-actions>
              <v-btn
                block
                type="submit"
                color="primary"
                :disabled="invalid || loading"
              >
                <v-icon left>
                  mdi-send
                </v-icon>
                Derivar
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
  name: 'ProcedureFlow',
  props: {
    areas: {
      type: Array,
      required: true,
    },
  },
  data: function() {
    return {
      dialog: false,
      loading: false,
      procedure: {},
      selectedArea: null,
      procedureType: null,
    }
  },
  computed: {
    filteredAreas() {
      const area = this.areas.find(o => o.id == this.procedure.area_id)
      if (area.group > 1) {
        return this.areas.filter(o => o.group == area.group && o.id != this.procedure.area_id)
      } else {
        return this.areas.filter(o => o.group != 0 && o.id != this.procedure.area_id)
      }
    },
  },
  methods: {
    showDialog(procedure, procedureType) {
      this.procedureType = procedureType
      this.procedure = {
        ...procedure
      }
      this.dialog = true
      this.$nextTick(() => {
        this.$refs.procedureFlowObserver.reset()
      })
    },
    async submit() {
      try {
        let valid = await this.$refs.procedureFlowObserver.validate()
        if (valid) {
          this.loading = true
          const response = await axios.post(`procedure/${this.procedure.id}/flow`, {
            area_id: this.selectedArea
          })
          this.$toast.info(response.data.message)
          this.$emit('updateList')
          this.dialog = false
          this.edit = false
        }
        this.loading = true
      } catch(error) {
        this.$refs.procedureFlowObserver.reset()
        if ('errors' in error.response.data) {
          this.$refs.procedureFlowObserver.setErrors(error.response.data.errors)
        }
      } finally {
        this.loading = false
      }
    }
  },
}
</script>