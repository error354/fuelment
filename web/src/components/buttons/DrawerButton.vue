<template>
  <q-item clickable v-ripple>
    <q-item-section avatar v-if="!loading">
      <q-icon :name="icon" />
    </q-item-section>
    <q-item-section>
      <span v-if="!loading">{{ label }}</span>
      <q-inner-loading :showing="loading">
        <q-spinner size="sm" />
      </q-inner-loading>
    </q-item-section>
  </q-item>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
  name: "DrawerButton",
  props: {
    icon: String,
    label: String,
    loading: Boolean,
  },
});
</script>

<style lang="scss">
.q-item.q-router-link--active,
.q-item--active {
  color: #fff;
}
</style>
