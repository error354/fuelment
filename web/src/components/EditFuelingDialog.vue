<template>
  <q-dialog ref="dialogRef" no-backdrop-dismiss @hide="onDialogHide">
    <fueling-dialog
      @submit="saveFueling"
      @cancel="onDialogCancel"
      :disabled="savingFueling"
      :vehicle-name="vehicle.name"
      :title="title"
      :price-setting="priceSetting"
      :date="fuelingDate"
      :mileage="mileage"
      :amount="amount"
      :full="full"
      :route="route"
      :price="price"
    />
  </q-dialog>
</template>

<script setup>
import { ref } from "vue";
import { useDialogPluginComponent } from "quasar";
import { apiClient, handleErrors } from "src/boot/apiClient";
import FuelingDialog from "./FuelingDialog.vue";

const props = defineProps({
  fuelingId: Number,
  vehicle: Object,
  title: String,
  priceSetting: String,
  fuelingDate: Date,
  mileage: Number,
  amount: Number,
  full: Boolean,
  route: Boolean,
  price: Number,
});

const emit = defineEmits(["saved", ...useDialogPluginComponent.emits]);

const { dialogRef, onDialogHide, onDialogOK, onDialogCancel } =
  useDialogPluginComponent();

const savingFueling = ref(false);

const editFuelingMutation = `
      mutation updateFueling($fuelingId: ID!, $amount: Float!, $mileage: Float!, $full: Boolean!, $date: Date, $price: Float, $newRoute: Boolean) {
        updateFueling(
          id: $fuelingId
          input: {amount: $amount, mileage: $mileage, full: $full, date: $date, price: $price, newRoute: $newRoute}
        ) {
          id
          vehicle {
            id
            name
          }
          amount
          mileage
          full
          fuelConsumption
          kilometerCost
          date
          price
          isLastOfRoute
          route {
            id
          }
        }
      }
    `;
async function saveFueling(data) {
  savingFueling.value = true;
  await apiClient
    .executeMutation({
      query: editFuelingMutation,
      variables: {
        fuelingId: props.fuelingId,
        amount: parseFloat(data.amount),
        mileage: parseFloat(data.mileage),
        full: Boolean(data.full),
        date: data.fuelingDate,
        price: parseFloat(data.price),
        newRoute: Boolean(data.route),
      },
      clearCacheTags: [`vehicle_${props.vehicle.id}_fuelings`, `route`],
    })
    .then((response) => {
      if (response.error) {
        handleErrors(response.error);
      } else {
        onDialogOK();
      }
    });
  savingFueling.value = false;
}
</script>
