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
        <v-toolbar-title>Seguimiento de hoja de ruta</v-toolbar-title>
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
        <validation-observer ref="searchbserver" v-slot="{ invalid }">
          <form v-on:submit.prevent="submit">
            <v-card-text>
              <validation-provider
                v-slot="{ errors }"
                name="code"
                rules="required|min:3"
              >
                <v-text-field
                  label="Código de hoja de ruta"
                  v-model="searchForm.code"
                  data-vv-name="code"
                  :error-messages="errors"
                  prepend-icon="mdi-barcode-scan"
                  :readonly="timeline.length > 0"
                  autofocus
                ></v-text-field>
              </validation-provider>
              <v-divider></v-divider>
              <Timeline :timeline="timeline" v-show="timeline.length > 0"/>
            </v-card-text>
            <v-card-actions>
              <v-btn
                block
                type="submit"
                color="primary"
                :disabled="invalid || loading"
                v-if="timeline.length == 0"
              >
                <v-icon class="mr-2">
                  mdi-magnify
                </v-icon>
                Buscar
              </v-btn>
              <v-btn
                block
                color="primary"
                :disabled="loading"
                v-else
                @click.stop="showDialog"
              >
                <v-icon class="mr-2">
                  mdi-autorenew
                </v-icon>
                Nueva búsqueda
              </v-btn>
            </v-card-actions>
          </form>
        </validation-observer>
      </div>
    </v-card>
  </v-dialog>
</template>

<script>
import Timeline from '@/components/shared/Timeline'

export default {
  name: 'SearchCode',
  components: {
    Timeline,
  },
  data: function() {
    return {
      dialog: false,
      timeline: [],
      searchForm: {
        code: '',
      },
      loading: false,
    }
  },
  methods: {
    showDialog() {
      this.timeline = []
      this.searchForm = {
        code: '',
      }
      this.dialog = true
      this.$nextTick(() => {
        this.$refs.searchbserver.reset()
      })
    },
    async submit() {
      try {
        let valid = await this.$refs.searchbserver.validate()
        if (valid) {
          this.loading = true
          await axios.get('sanctum/csrf-cookie')
          let response = await axios.get(`procedure_code`, {
            params: {
              code: this.searchForm.code
            }
          })
          response = await axios.get(`procedure/${response.data.payload.procedure.id}/flow`)
          this.timeline = response.data.payload.timeline
          console.log(this.timeline)
        }
      } catch(error) {
        if ('errors' in error.response.data) {
          this.$refs.searchbserver.setErrors(error.response.data.errors)
        }
      } finally {
        this.loading = false
      }
    },
  }
}
</script>