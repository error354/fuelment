<template>
  <q-input
    square
    outlined
    :value="value"
    type="email"
    class="q-mb-md"
    :label="labelWithDefault"
    :rules="[
      $rules.email($t('validation.email')),
      $rules.maxLength(255, $t('validation.maxLength', { number: 255 })),
      ...rules,
    ]"
    lazy-rules
  />
</template>

<script setup>
import { computed } from "vue";
import { i18n } from "src/boot/i18n";

const $t = i18n.global.t;

const props = defineProps({
  value: String,
  label: {
    type: String,
    default: "",
  },
  rules: {
    type: Array,
    required: false,
  },
});
const labelWithDefault = computed(() => {
  return props.label || $t("login.emailAddress");
});
</script>

<style></style>
