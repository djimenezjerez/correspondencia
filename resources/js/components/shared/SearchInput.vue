<template>
  <v-text-field
    label="Buscar"
    prepend-icon="mdi-magnify"
    filled
    outlined
    single-line
    clearable
    dense
    v-model="searchValue"
    @input="inputUpdated($event)"
    v-on:keyup.enter="searchValue && $emit('input', searchValue)"
  ></v-text-field>
</template>

<script>
export default {
  name: 'SearchInput',
  data: function() {
    return {
      searchValue: '',
    }
  },
  methods: {
    inputUpdated(value) {
      if (value == null || value.length >= 3 || value.length == 0) {
        this.$emit('input', value)
      }
    }
  }
}
</script>