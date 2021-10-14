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
        <ToolBarTitle title="Registro de derivaciones"/>
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
        <v-card-text>
          <Timeline :timeline="timeline"/>
        </v-card-text>
        <v-card-actions>
          <v-btn
            block
            type="submit"
            color="info"
            :disabled="loading"
            @click="dialog = false"
          >
            <v-icon left>
              mdi-close
            </v-icon>
            Cerrar
          </v-btn>
        </v-card-actions>
      </div>
    </v-card>
  </v-dialog>
</template>

<script>
import Timeline from '@/components/shared/Timeline'

export default {
  name: 'ProcedureTimeline',
  components: {
    Timeline
  },
  data: function() {
    return {
      dialog: false,
      loading: true,
      timeline: [],
      procedure: {},
    }
  },
  methods: {
    showDialog(procedure) {
      this.timeline = []
      this.fetchFlows(procedure.id)
      this.procedure = {
        ...procedure
      }
      this.dialog = true
    },
    async fetchFlows(procedureId) {
      try {
        this.loading = true
        const response = await axios.get(`tracking/${procedureId}`)
        this.timeline = response.data.payload.timeline
      } catch (error) {
        console.error(error)
      } finally {
        this.loading = false
      }
    }
  },
}
</script>