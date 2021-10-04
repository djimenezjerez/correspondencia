<template>
  <v-toolbar-title class="text-xs-caption text-sm-caption text-sm-body-2 text-md-body-1 text-lg-h6 text-xl-h6">{{ title }}</v-toolbar-title>
</template>

<script>
export default {
  name: 'ToolBarTitle',
  props: {
    title: {
      type: String,
      required: true,
    },
  },
}
</script>