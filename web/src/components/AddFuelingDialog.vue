<template>
  <q-dialog ref="dialogRef" no-backdrop-dismiss @hide="onDialogHide">
    <fueling-dialog
      @submit="addFueling"
      @cancel="onDialogCancel"
      :disabled="addingFueling"
      :vehicle-name="vehicleName"
      :title="title"
      :price-setting="priceSetting"
      :fueling-date="fuelingDate"
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
  vehicleId: Number,
  vehicleName: String,
  title: String,
  priceSetting: String,
  fuelingDate: Date,
  mileage: Number,
  amount: Number,
  full: Boolean,
  route: Boolean,
  price: Number,
});

const emit = defineEmits([...useDialogPluginComponent.emits]);

const { dialogRef, onDialogHide, onDialogOK, onDialogCancel } =
  useDialogPluginComponent();

const addingFueling = ref(false);

const addFuelingMutation = `
      mutation createFueling($vehicleId: ID!, $amount: Float!, $mileage: Float!, $full: Boolean!, $date: Date, $price: Float, $newRoute: Boolean) {
        createFueling(
          input: {vehicleId: $vehicleId, amount: $amount, mileage: $mileage, full: $full, date: $date, price: $price, newRoute: $newRoute}
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
async function addFueling(data) {
  addingFueling.value = true;
  await apiClient
    .executeMutation({
      query: addFuelingMutation,
      variables: {
        vehicleId: props.vehicleId,
        amount: parseFloat(data.amount),
        mileage: parseFloat(data.mileage),
        full: Boolean(data.full),
        date: data.fuelingDate,
        price: parseFloat(data.price),
        newRoute: Boolean(data.route),
      },
      clearCacheTags: [`vehicle_${props.vehicleId}_fuelings`],
    })
    .then((response) => {
      if (response.error) {
        handleErrors(response.error);
      } else {
        onDialogOK();
      }
    });
  addingFueling.value = false;
}
</script>

<style scoped>
.full-width {
  width: 100%;
}
</style>
