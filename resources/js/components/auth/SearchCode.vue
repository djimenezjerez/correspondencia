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
        <v-container>
          <v-row
            justify="center"
            no-gutters
          >
            <v-col
              cols="12"
            >
              <v-img
                class="mt-4"
                contain
                :max-height="imageHeight"
                :height="imageHeight"
                src="/img/logo.png"
              ></v-img>
            </v-col>
            <v-col cols="12">

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
                        autofocus
                        v-if="timeline.length == 0"
                      ></v-text-field>
                      <v-simple-table class="mt-3" v-else>
                        <template v-slot:default>
                          <tbody>
                            <tr>
                              <td class="text-right text-body-1">Hoja de ruta: </td>
                              <td class="font-weight-bold text-body-1">{{ procedure.code }}</td>
                            </tr>
                            <tr>
                              <td class="text-right text-body-1">Tipo de trámite: </td>
                              <td class="font-weight-bold text-body-1">{{ procedure.procedure_type.name }}</td>
                            </tr>
                            <tr>
                              <td class="text-right text-body-1">Nombre: </td>
                              <td class="font-weight-bold text-body-1">{{ procedure.origin }}</td>
                            </tr>
                            <tr>
                              <td class="text-right text-body-1">Su trámite se encuentra en: </td>
                              <td class="font-weight-bold text-body-1">{{ timeline[0].to_area }}</td>
                            </tr>
                          </tbody>
                        </template>
                      </v-simple-table>
                    </validation-provider>
                    <v-divider></v-divider>
                    <div class="text-center mt-6 text-h6" v-show="timeline.length > 0">
                      Detalle de derivaciones
                    </div>
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
            </v-col>
          </v-row>
        </v-container>
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
      procedure: {
        id: null
      },
      searchForm: {
        code: '',
      },
      loading: false,
    }
  },
  computed: {
    imageHeight() {
      switch (this.$vuetify.breakpoint.name) {
        case 'xs': return '80px'
        case 'sm': return '110px'
        case 'md': return '140px'
        default: return '150'
      }
    },
  },
  methods: {
    showDialog() {
      this.timeline = []
      this.procedure = {
        id: null
      }
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
          this.procedure = response.data.payload.procedure
          response = await axios.get(`procedure/${response.data.payload.procedure.id}/flow`)
          this.timeline = response.data.payload.timeline
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