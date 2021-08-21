<template>
  <v-timeline :dense="$vuetify.breakpoint.smAndDown">
    <v-row justify="center">
      <v-col cols="12">
        <v-timeline-item
          v-for="(flow, index) in timeline"
          :key="index"
          class="text-center"
          :icon="index > 0 ? 'mdi-arrow-up-bold' : 'mdi-map-marker-radius'"
        >
          <v-card
            :dark="index == 0"
            :color="index > 0 ? '' : '#666'"
            elevation="5"
            :class="index > 0 || 'pulse'"
            class="rounded-card"
          >
            <v-card-text>
              <div class="font-weight-bold">
                {{ flow.to_area }}
              </div>
              <div class="font-weight-medium">
                {{ flow.action }}
              </div>
              <div class="font-weight-normal">
                {{ flow.date | moment('L LT') }}
              </div>
            </v-card-text>
          </v-card>
        </v-timeline-item>
      </v-col>
    </v-row>
  </v-timeline>
</template>

<script>
export default {
  name: 'Timeline',
  props: {
    timeline: {
      type: Array,
      default: []
    }
  }
}
</script>

<style scoped>
.rounded-card {
  border-radius: 15px;
}

.pulse {
	animation: pulse 5s infinite;
}

@keyframes pulse {
  0% {
		transform: scale(1);
	}
	25% {
		transform: scale(1.02);
	}
	50% {
		transform: scale(1);
	}
	75% {
		transform: scale(1.02);
	}
  100% {
		transform: scale(1);
	}
}
</style>