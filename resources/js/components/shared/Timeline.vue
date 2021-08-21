<template>
  <v-timeline :dense="$vuetify.breakpoint.smAndDown">
    <v-row justify="center">
      <v-col cols="12">
        <v-timeline-item
          v-for="(flow, index) in timeline"
          :key="index"
          :class="{ 'text-right': index%2 && $vuetify.breakpoint.mdAndUp }"
          :icon="index > 0 ? 'mdi-arrow-up-bold' : 'mdi-map-marker-radius'"
        >
          <div class="font-weight-medium">
            {{ flow.to_area }}
          </div>
          <div class="font-weight-regular">
            {{ flow.action }}
          </div>
          <div class="font-weight-light">
            {{ flow.date | moment('L LT') }}
          </div>
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