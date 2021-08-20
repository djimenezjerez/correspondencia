<template>
  <v-timeline :dense="$vuetify.breakpoint.smAndDown">
    <v-timeline-item v-for="(flow, index) in timeline" :key="index" :class="{ 'text-right': index%2 }">
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