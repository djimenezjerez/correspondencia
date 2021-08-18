<template>
  <v-text-field
    label="Buscar"
    prepend-icon="mdi-magnify"
    filled
    outlined
    single-line
    clearable
    dense
    @input="inputUpdated($event)"
  ></v-text-field>
</template>

<script>
export default {
  name: 'SearchInput',
  methods: {
    inputUpdated(value) {
      if (value == null || value.length >= 3 || value.length == 0) {
        this.$emit('input', value)
      }
    }
  }
}
</script>