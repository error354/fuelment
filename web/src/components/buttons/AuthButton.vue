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

<script setup>
const props = defineProps({
  label: {
    type: String,
    required: true,
  },
  loading: {
    type: Boolean,
    default: false,
  },
});
</script>

<style></style>
