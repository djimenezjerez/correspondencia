<template>
  <v-btn
    :large="$vuetify.breakpoint.xl || $vuetify.breakpoint.lg"
    :small="$vuetify.breakpoint.sm"
    :x-small="$vuetify.breakpoint.xs"
    class="py-4"
    color="info"
    @click="$emit('click', $event)"
  >
    <v-icon
      class="mr-3"
    >
      {{ icon }}
    </v-icon>
    {{ text }}
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