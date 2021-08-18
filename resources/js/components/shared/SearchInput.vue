<template>
  <v-text-field
    dark
    label="Buscar"
    :value="search"
    prepend-icon="mdi-magnify"
    filled
    outlined
    single-line
    clearable
    dense
    @input="inputUpdated($event)"
    class="mt-8 shrink"
  ></v-text-field>
</template>

<script>
export default {
  name: 'SearchInput',
  props: ['search'],
  methods: {
    inputUpdated(value) {
      if (value == null || value.length >= 3 || value.length == 0) {
        this.$emit('input', value)
      }
    }
  }
}
</script>