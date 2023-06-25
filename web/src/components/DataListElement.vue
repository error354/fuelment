<template>
  <div class="col-12 col-sm-6 col-md-12">
    <div class="fit row data-row q-pa-xs" :class="light ? 'light' : ''">
      <div class="col-8">
        <slot name="name">{{ name }}</slot>
      </div>
      <div class="col-4">
        <slot name="value" v-if="!loading">{{ value }}</slot>
        <q-skeleton v-else animation="fade" type="text" width="30px" />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "DataListElement",
  props: {
    name: String,
    value: String,
    loading: Boolean,
    light: Boolean,
  },
};
</script>

<style lang="scss" scoped>
.data-row {
  border-bottom: 1px $grey-13 solid;
  transition: background-color 0.2s;
}

.data-row:hover {
  background-color: $grey-4;
}

.data-row.light {
  border-bottom: 1px $grey-4 solid;
  transition: background-color 0.2s;
}

.data-row.light:hover {
  background-color: $grey-3;
}
</style>
