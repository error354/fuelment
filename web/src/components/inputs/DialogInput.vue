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

<script>
import { defineComponent } from "vue";
import { i18n } from "src/boot/i18n";

const $t = i18n.global.t;

export default defineComponent({
  name: "DialogInput",
  props: {
    rules: {
      type: Array,
      default: Array,
    },
  },
});
</script>
