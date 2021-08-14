<template>
  <v-snackbar
    v-model="showSnackbar"
    :timeout="3000"
  >
    {{ text }}
    <template v-slot:action="{ attrs }">
      <v-btn
        icon
        v-bind="attrs"
        @click="showSnackbar = false"
      >
        <v-icon>
          mdi-close
        </v-icon>
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