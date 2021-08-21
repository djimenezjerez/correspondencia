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
            class="rounded-card"
            elevation="5"
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
.rounded-card{
  border-radius: 15px;
}
</style>