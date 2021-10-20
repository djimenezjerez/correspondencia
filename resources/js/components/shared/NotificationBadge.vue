<template>
  <div>
    <audio id="notification" src="/audio/notification.mp3" type="audio/mp3"></audio>
    <div v-if="badge > 0">
      <v-menu offset-y v-model="menu" :disabled="$route.name != 'procedures'">
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            v-bind="attrs"
            v-on="on"
            icon
          >
            <v-badge
              overlap
              color="red"
              :content="badge"
            >
              <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                  <v-icon
                    v-bind="attrs"
                    v-on="on"
                    style="font-size: 30px;"
                  >mdi-bell</v-icon>
                </template>
                <span>Recibir</span>
              </v-tooltip>
            </v-badge>
          </v-btn>
        </template>
        <v-list
          v-for="procedure in tray"
          :key="procedure.id"
        >
          <v-list-item
            :disabled="loading"
            @click.stop="receiveProcedure(procedure.id)"
          >
            <v-list-item-title>{{ procedure.code }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
    </div>
    <div v-else>
      <v-icon disabled>mdi-bell</v-icon>
    </div>
  </div>
</template>

<script>
import { bus } from '@/app'

export default {
  name: 'NotificationBadge',
  data: function() {
    return {
      loading: true,
      menu: false,
      badge: 0,
      tray: [],
    }
  },
  mounted() {
    bus.$on('updateProcedureNotification', (value) => {
      document.getElementById('notification').play()
      this.badge = value
      this.fetchPendingBadge()
    })
    this.fetchPendingBadge()
  },
  methods: {
    async receiveProcedure(procedure_id) {
      try {
        this.loading = true
        const response = await axios.post(`procedure/tray/receive/${procedure_id}`)
        this.menu = false
        this.$toast.success(`${response.data.message}: ${response.data.payload.procedure.code}`)
      } catch(error) {
        console.error(error)
      } finally {
        this.fetchPendingBadge()
        this.loading = false
      }
    },
    async fetchPendingBadge() {
      try {
        this.loading = true
        let response = await axios.get('procedure/tray/pending')
        this.badge = response.data.payload.badge
        this.tray = response.data.payload.procedures
      } catch(error) {
        console.error(error)
      } finally {
        bus.$emit('updateProcedureList')
        this.loading = false
      }
    },
  },
}
</script>

<style scoped>
.v-icon {
    font-size: calc(1em + 0.15vh + 0.3vw) !important;
}
</style>