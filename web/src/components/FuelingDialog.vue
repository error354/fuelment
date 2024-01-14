<template>
  <q-dialog ref="dialogRef" no-backdrop-dismiss @hide="onDialogHide">
    <q-card class="q-dialog-plugin">
      <q-card-section>
        <div class="text-h6" v-if="vehicleName">{{ vehicleName }}</div>
        <div class="text-h6" v-if="title">{{ title }}</div>
      </q-card-section>
      <q-card-section>
        <q-form @submit="addFueling" greedy id="form">
          <dialog-input
            autofocus
            v-model="fuelingDate"
            mask="####-##-##"
            :label="$t('fuelingsTable.date')"
            :disable="addingFueling"
            :placeholder="$t('fuelingsTable.datePlaceholder')"
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
                    v-model="fuelingDate"
                    color="secondary"
                    today-btn
                    mask="YYYY-MM-DD"
                    :options="disableFutureDates"
                    @update:model-value="showCalendar = false"
                  >
                  </q-date>
                </q-popup-proxy>
              </q-icon>
            </template>
          </dialog-input>
          <dialog-number-input
            v-model="mileage"
            :label="$t('fuelingsTable.mileage')"
            :disable="addingFueling"
            step="0.01"
            :rules="[$rules.required($t('validation.required'))]"
          />
          <dialog-number-input
            v-model="amount"
            :label="$t('fuelingsTable.amount')"
            :disable="addingFueling"
            :rules="[$rules.required($t('validation.required'))]"
          />
          <dialog-number-input
            v-model="totalPrice"
            :label="$t('fuelingsTable.price')"
            v-if="showPrice()"
            :rules="
              isPriceRequired()
                ? [$rules.required($t('validation.required'))]
                : []
            "
            :disable="addingFueling"
          />
          <dialog-number-input
            v-model="pricePerLiter"
            :label="$t('fuelingsTable.pricePerLiter')"
            v-if="showPrice()"
            :rules="
              isPriceRequired()
                ? [$rules.required($t('validation.required'))]
                : []
            "
            :disable="addingFueling"
          />
          <q-checkbox
            class="full-width"
            v-model="full"
            :label="$t('fuelingsTable.full')"
            :disable="addingFueling"
          />
          <q-checkbox
            v-model="route"
            :label="$t('fuelingsTable.newRoute')"
            :disable="addingFueling"
          />
        </q-form>
      </q-card-section>
      <q-card-actions align="right">
        <q-btn
          color="negative"
          flat
          :label="$t('fuelingsTable.cancel')"
          @click="onDialogCancel"
        />
        <q-btn
          flat
          :label="$t('fuelingsTable.save')"
          :loading="addingFueling"
          type="submit"
          form="form"
        />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { ref, computed } from "vue";
import { useDialogPluginComponent, date } from "quasar";
import { apiClient, handleErrors } from "src/boot/apiClient";
import DialogInput from "./DialogInput.vue";
import DialogNumberInput from "./DialogNumberInput.vue";

const props = defineProps({
  vehicleId: Number,
  vehicleName: String,
  title: String,
  priceSetting: String,
});
const fuelingDate = defineModel("date", { type: Date });
const mileage = defineModel("mileage", { type: Number });
const amount = defineModel("amount", { type: Number });
const full = defineModel("full", { type: Boolean });
const route = defineModel("route", { type: Boolean });
const price = defineModel("price", { type: Number });

const emit = defineEmits([...useDialogPluginComponent.emits]);

const { dialogRef, onDialogHide, onDialogOK, onDialogCancel } =
  useDialogPluginComponent();

const calcPricePerLiter = (pricePerLiter, amount) => {
  if (!pricePerLiter || !amount) {
    return null;
  }
  return (pricePerLiter / amount).toFixed(2);
};

const newPricePerLiter = ref(calcPricePerLiter(price.value, amount.value));
const pricePerLiter = computed({
  get() {
    return newPricePerLiter.value;
  },
  set(newValue) {
    newPricePerLiter.value = newValue;
    price.value = (newPricePerLiter.value * amount.value).toFixed(2);
  },
});

const addingFueling = ref(false);
const showCalendar = ref(false);

const showPrice = () => {
  if (props.priceSetting == "DISABLED") {
    return false;
  }
  return true;
};

const isPriceRequired = () => {
  if (props.priceSetting == "REQUIRED") {
    return true;
  }
  return false;
};

const totalPrice = computed({
  get() {
    return price.value;
  },
  set(newValue) {
    price.value = newValue;
    newPricePerLiter.value = (price.value / amount.value).toFixed(2);
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
        amount: parseFloat(amount.value),
        mileage: parseFloat(mileage.value),
        full: Boolean(full.value),
        date: fuelingDate.value,
        price: parseFloat(price.value),
        newRoute: Boolean(route.value),
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

function disableFutureDates(d) {
  const today = new Date();
  return date.formatDate(d) <= date.formatDate(today);
}
</script>

<style scoped>
.full-width {
  width: 100%;
}
</style>
