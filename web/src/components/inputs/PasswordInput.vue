<template>
  <q-input
    square
    outlined
    :value="value"
    :type="showPassword ? 'text' : 'password'"
    class="q-mb-md"
    :label="labelWithDefault"
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

<script setup>
import { ref, computed } from "vue";
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
  return props.label || $t("login.password");
});

const showPassword = ref(false);
</script>

<style></style>
