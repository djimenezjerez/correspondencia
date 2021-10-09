<template>
  <div>
    <audio id="notification" src="/audio/notification.mp3" type="audio/mp3"></audio>
    <div v-if="badge > 0">
      <v-tooltip bottom>
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            v-bind="attrs"
            v-on="on"
            icon
            @click.stop="receiveProcedures"
          >
            <v-badge
              overlap
              color="red"
              :content="badge"
            >
              <v-icon>mdi-bell</v-icon>
            </v-badge>
          </v-btn>
        </template>
        <span>Recibir</span>
      </v-tooltip>
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
      badge: 0,
    }
  },
  mounted() {
    bus.$on('updateProcedureNotification', (value) => {
      document.getElementById('notification').play()
      this.badge = value
    })
    this.fetchPendingBadge()
  },
  methods: {
    async receiveProcedures() {
      try {
        await axios.post('procedure/tray/receive')
      } catch(error) {
        console.log(error)
      } finally {
        this.fetchPendingBadge()
        bus.$emit('updateProcedureList')
      }
    },
    async fetchPendingBadge() {
      try {
        let response = await axios.get('procedure/tray/pending')
        this.badge = response.data.payload.badge
      } catch(error) {
        console.log(error)
      }
    },
  },
}
</script>