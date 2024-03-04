<template>
  <q-input
    square
    filled
    class="q-mb-sm"
    :rules="[
      $rules.maxLength(255, $t('validation.maxLength', { number: 255 })),
      ...rules,
    ]"
    lazy-rules
  >
    <template v-slot:append>
      <slot name="append"></slot>
    </template>
  </q-input>
</template>

<script setup>
import { i18n } from "src/boot/i18n";

const $t = i18n.global.t;

const props = defineProps({
  rules: {
    type: Array,
    default: Array,
  },
});
</script>
