<template>
  <q-input
    square
    outlined
    :value="value"
    :type="showPassword ? 'text' : 'password'"
    class="q-mb-md"
    :label="label"
    :rules="[
      $rules.maxLength(255, $t('validation.maxLength', { number: 255 })),
      ...rules,
    ]"
    lazy-rules
  >
    <template v-slot:append>
      <q-icon
        :name="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
        class="cursor-pointer"
        @click="showPassword = !showPassword"
      />
    </template>
  </q-input>
</template>

<script>
import { defineComponent } from "vue";
import { i18n } from "../boot/i18n";

const $t = i18n.global.t;

export default defineComponent({
  name: "PasswordInput",
  props: {
    value: String,
    label: {
      type: String,
      default: $t("login.password"),
    },
    rules: {
      type: Array,
      required: false,
    },
  },
  data() {
    return {
      showPassword: false,
    };
  },
});
</script>

<style></style>
