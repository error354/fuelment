<template>
  <DialogInput
    v-model.number="value"
    type="number"
    min="0.01"
    step="0.01"
    inputmode="numeric"
    :rules="rules"
  />
</template>

<script setup>
import DialogInput from "./DialogInput.vue";

const props = defineProps({
  props: {
    value: String,
  },
  rules: {
    type: Array,
    default: Array,
  },
});
</script>
