<template>
  <dialog-input
    autofocus
    v-model="model"
    mask="####-##-##"
    :label="labelWithDefault"
    :disable="false"
    :placeholder="placeholderWithDefault"
  >
    <template v-slot:append>
      <q-icon name="event" class="cursor-pointer">
        <q-popup-proxy
          v-model="showCalendar"
          cover
          transition-show="scale"
          transition-hide="scale"
        >
          <q-date
            v-model="model"
            color="secondary"
            today-btn
            :mask="mask"
            :options="disableFutureDates"
            @update:model-value="showCalendar = false"
          >
          </q-date>
        </q-popup-proxy>
      </q-icon>
    </template>
  </dialog-input>
</template>

<script setup>
import { defineModel, ref } from "vue";
import { useI18n } from "vue-i18n";
import DialogInput from "./DialogInput.vue";

const props = defineProps({
  label: String,
  placeholder: String,
  mask: {
    type: String,
    default: "YYYY-MM-DD",
  },
});

const { t } = useI18n();
const labelWithDefault = props.label || t("fuelingsTable.date");
const placeholderWithDefault =
  props.placeholder || t("fuelingsTable.datePlaceholder");

const model = defineModel();
const showCalendar = ref(false);
</script>
