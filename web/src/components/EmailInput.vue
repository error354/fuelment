<template>
  <q-input
    square
    outlined
    :value="value"
    type="email"
    class="q-mb-md"
    :label="label"
    :rules="[
      $rules.email($t('validation.email')),
      $rules.maxLength(255, $t('validation.maxLength', { number: 255 })),
      ...rules,
    ]"
    lazy-rules
  />
</template>

<script>
import { defineComponent } from "vue";
import { i18n } from "../boot/i18n";

const $t = i18n.global.t;

export default defineComponent({
  name: "EmailInput",
  props: {
    value: String,
    label: {
      type: String,
      default: $t("login.emailAddress"),
    },
    rules: {
      type: Array,
      required: false,
    },
  },
});
</script>

<style></style>
