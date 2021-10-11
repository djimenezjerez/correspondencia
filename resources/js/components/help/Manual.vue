<template>
  <v-container fluid>
    <v-row>
      <v-col cols="12">
        <object style="width: 100%; height: 87vh;" data="http://localhost/assets/MANUAL.pdf" type="application/pdf">
          <embed src="http://localhost/assets/MANUAL.pdf" type="application/pdf" />
        </object>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  name: 'HelpManual',
}
</script>