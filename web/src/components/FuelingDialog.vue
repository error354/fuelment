<template>
  <q-dialog ref="dialogRef" @hide="onDialogHide">
    <q-card class="q-dialog-plugin">
      <q-card-section>
        <div class="text-h6" v-if="vehicleName">{{ vehicleName }}</div>
        <div class="text-h6" v-if="title">{{ title }}</div>
      </q-card-section>
      <q-card-section>
        <q-input
          v-model="newDate"
          square
          filled
          class="q-mb-sm"
          :label="$t('fuelingsTable.date')"
          :disable="addingFueling"
        />
        <q-input
          v-model="newMileage"
          square
          filled
          class="q-mb-sm"
          :label="$t('fuelingsTable.mileage')"
          :disable="addingFueling"
        />
        <q-input
          v-model="newAmount"
          square
          filled
          class="q-mb-sm"
          :label="$t('fuelingsTable.amount')"
          :disable="addingFueling"
        />
        <q-input
          v-model="totalPrice"
          class="q-mb-sm"
          square
          filled
          :label="$t('fuelingsTable.price')"
          v-if="showPrice"
          :disable="addingFueling"
        />
        <q-input
          v-model="pricePerLiter"
          class="q-mb-sm"
          square
          filled
          :label="$t('fuelingsTable.pricePerLiter')"
          v-if="showPrice"
          :disable="addingFueling"
        />
        <q-checkbox
          class="full-width"
          v-model="newFull"
          :label="$t('fuelingsTable.full')"
          :disable="addingFueling"
        />
        <q-checkbox
          v-model="newRoute"
          :label="$t('fuelingsTable.newRoute')"
          :disable="addingFueling"
        />
      </q-card-section>
      <q-card-actions align="right">
        <q-btn
          color="negative"
          flat
          :label="$t('fuelingsTable.cancel')"
          @click="onDialogCancel"
        />
        <q-btn
          color="primary"
          flat
          :label="$t('fuelingsTable.save')"
          :loading="addingFueling"
          @click="addFueling"
        />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script>
import { ref, computed } from "vue";
import { useDialogPluginComponent } from "quasar";
import { apiClient, handleErrors } from "src/boot/apiClient";

export default {
  props: {
    vehicleId: Number,
    vehicleName: String,
    title: String,
    date: Date,
    mileage: Number,
    amount: Number,
    full: Boolean,
    route: Boolean,
    price: Number,
    showPrice: Boolean,
  },

  emits: [...useDialogPluginComponent.emits],

  setup(props) {
    const { dialogRef, onDialogHide, onDialogOK, onDialogCancel } =
      useDialogPluginComponent();
    const calcPricePerLiter = (pricePerLiter, amount) => {
      if (!pricePerLiter || !amount) {
        return null;
      }
      return (pricePerLiter / amount).toFixed(2);
    };

    const newDate = ref(props.date);
    const newMileage = ref(props.mileage);
    const newAmount = ref(props.amount);
    const newFull = ref(props.full);
    const newRoute = ref(props.route);
    const newPrice = ref(props.price);
    const newPricePerLiter = ref(
      calcPricePerLiter(newPrice.value, newAmount.value)
    );

    const addingFueling = ref(false);

    const totalPrice = computed({
      get() {
        return newPrice.value;
      },
      set(newValue) {
        newPrice.value = newValue;
        newPricePerLiter.value = (newPrice.value / newAmount.value).toFixed(2);
      },
    });

    const pricePerLiter = computed({
      get() {
        return newPricePerLiter.value;
      },
      set(newValue) {
        newPricePerLiter.value = newValue;
        newPrice.value = (newPricePerLiter.value * newAmount.value).toFixed(2);
      },
    });

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

    async function addFueling() {
      addingFueling.value = true;
      await apiClient
        .executeMutation({
          query: addFuelingMutation,
          variables: {
            vehicleId: props.vehicleId,
            amount: parseFloat(newAmount.value),
            mileage: parseFloat(newMileage.value),
            full: Boolean(newFull.value),
            date: newDate.value,
            price: parseFloat(newPrice.value),
            newRoute: Boolean(newRoute.value),
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

    return {
      dialogRef,
      onDialogHide,
      onDialogCancel,
      addFueling,
      newDate,
      newMileage,
      newAmount,
      newFull,
      newRoute,
      pricePerLiter,
      totalPrice,
      addingFueling,
    };
  },
};
</script>

<style scoped>
.full-width {
  width: 100%;
}
</style>
