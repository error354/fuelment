<template>
  <q-dialog ref="dialogRef" no-backdrop-dismiss @hide="onDialogHide">
    <q-card class="q-dialog-plugin">
      <q-card-section>
        <div class="text-h6">{{ $t("fuelingsTable.filterTitle") }}</div>
      </q-card-section>
      <q-card-section>
        <q-form @submit="applyFilters" @reset="resetForm" greedy id="form">
          <div class="row no-wrap justify-between form-row">
            <date-input
              v-model="dateFrom"
              :label="$t('fuelingsTable.dateFrom')"
              :placeholder="$t('fuelingsTable.datePlaceholder')"
            >
            </date-input>
            <date-input
              v-model="dateTo"
              :label="$t('fuelingsTable.dateTo')"
              :placeholder="$t('fuelingsTable.datePlaceholder')"
            >
            </date-input>
          </div>
          <div class="row no-wrap justify-between form-row">
            <dialog-number-input
              class="width-fix"
              v-model="mileageFrom"
              :label="$t('fuelingsTable.mileageFrom')"
            >
            </dialog-number-input>
            <dialog-number-input
              class="width-fix"
              v-model="mileageTo"
              :label="$t('fuelingsTable.mileageTo')"
            >
            </dialog-number-input>
          </div>
        </q-form>
      </q-card-section>
      <q-card-actions align="left">
        <q-btn
          color="accent"
          flat
          :label="$t('fuelingsTable.reset')"
          type="reset"
          form="form"
        />
        <q-space />
        <q-btn
          color="negative"
          flat
          :label="$t('fuelingsTable.cancel')"
          @click="onDialogCancel"
        />
        <q-btn
          flat
          :label="$t('fuelingsTable.apply')"
          :loading="addingFueling"
          type="submit"
          form="form"
        />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { ref, defineModel } from "vue";
import { useDialogPluginComponent, date } from "quasar";
import DateInput from "./DateInput.vue";
import DialogNumberInput from "./DialogNumberInput.vue";

const emit = defineEmits(["applyFilters", ...useDialogPluginComponent.emits]);

const dateFrom = defineModel("dateFrom");
const dateTo = defineModel("dateTo");
const mileageFrom = defineModel("mileageFrom");
const mileageTo = defineModel("mileageTo");
const { dialogRef, onDialogHide, onDialogOK, onDialogCancel } =
  useDialogPluginComponent();

const resetForm = () => {
  dateFrom.value = dateTo.value = mileageFrom.value = mileageTo.value = null;
};

const fuelingsFilterBuilder = () => {
  const filter = {};
  if (dateFrom.value || dateTo.value) filter.date = {};
  if (dateFrom.value) filter.date.from = dateFrom.value;
  if (dateTo.value) filter.date.to = dateTo.value;
  if (mileageFrom.value || mileageTo.value) filter.mileage = {};
  if (mileageFrom.value) filter.mileage.from = mileageFrom.value;
  if (mileageTo.value) filter.mileage.to = mileageTo.value;
  return filter;
};

const applyFilters = () => {
  emit("ok", fuelingsFilterBuilder());
  onDialogCancel();
};
</script>

<style scoped>
.form-row *:first-child {
  margin-right: 8px;
}

.width-fix {
  width: 100%;
}
</style>
