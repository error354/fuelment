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

<script>
import { defineComponent } from "vue";
import DialogInput from "./DialogInput.vue";

export default defineComponent({
  components: {
    DialogInput,
  },
  name: "DialogNumberInput",
  props: {
    props: {
      value: String,
    },
    rules: {
      type: Array,
      default: Array,
    },
  },
});
</script>
