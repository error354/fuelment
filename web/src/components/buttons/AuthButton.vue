<template>
  <q-btn
    color="green"
    text-color="white"
    size="lg"
    class="full-width auth-button no-border-radius q-mb-md"
    :label="label"
    unelevated
    :loading="loading"
    type="submit"
  />
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
  name: "AuthButton",
  props: {
    label: {
      type: String,
      required: true,
    },
    loading: {
      type: Boolean,
      default: false,
    },
  },
});
</script>

<style></style>
