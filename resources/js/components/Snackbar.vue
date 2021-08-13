<template>
  <v-snackbar
    v-model="showSnackbar"
    :timeout="3000"
  >
    {{ text }}
    <template v-slot:action="{ attrs }">
      <v-btn
        color="grey lighten-2"
        text
        v-bind="attrs"
        @click="showSnackbar = false"
      >
        CLOSE
      </v-btn>
      </template>
  </v-snackbar>
</template>

<script>
export default {
  name: 'vtoast',
  data: function() {
    return {
      showSnackbar: false,
      text: '',
    }
  },
  methods:{
    show(text) {
      this.showSnackbar = true
      this.text = text
    }
  }
}
</script>