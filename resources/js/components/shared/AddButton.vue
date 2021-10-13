<template>
  <v-btn
    :large="$vuetify.breakpoint.xl || $vuetify.breakpoint.lg"
    :small="$vuetify.breakpoint.sm"
    :x-small="$vuetify.breakpoint.xs"
    class="px-4 py-0 ma-0"
    color="info"
    @click="$emit('click', $event)"
  >
    <v-icon
      class="mr-3"
    >
      {{ icon }}
    </v-icon>
    <div class="text-responsive">
      {{ text }}
    </div>
  </v-btn>
</template>

<script>
export default {
  name: 'AddButton',
  props: {
    icon: {
      type: String,
      default: 'mdi-plus'
    },
    text: {
      type: String,
      default: 'Agregar'
    }
  }
}
</script>