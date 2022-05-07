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
        />
        <q-input
          v-model="newMileage"
          square
          filled
          class="q-mb-sm"
          :label="$t('fuelingsTable.mileage')"
        />
        <q-input
          v-model="newAmount"
          square
          filled
          class="q-mb-sm"
          :label="$t('fuelingsTable.amount')"
        />
        <q-input
          v-model="pricePerLiter"
          class="q-mb-sm"
          square
          filled
          :label="$t('fuelingsTable.price')"
          v-if="showPrice"
        />
        <q-input
          v-model="totalPrice"
          class="q-mb-sm"
          square
          filled
          :label="$t('fuelingsTable.totalPrice')"
          v-if="showPrice"
        />
        <q-checkbox v-model="newFull" :label="$t('fuelingsTable.full')" />
      </q-card-section>
      <q-card-actions align="right">
        <q-btn
          color="negative"
          flat
          :label="$t('fuelingsTable.cancel')"
          @click="onCancelClick"
        />
        <q-btn
          color="primary"
          flat
          :label="$t('fuelingsTable.save')"
          @click="onOKClick"
        />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script>
import { ref, computed } from "vue";
import { useDialogPluginComponent } from "quasar";

export default {
  props: {
    vehicleName: String,
    title: String,
    date: Date,
    mileage: Number,
    amount: Number,
    full: Boolean,
    price: Number,
    showPrice: Boolean,
  },

  emits: [
    // REQUIRED; need to specify some events that your
    // component will emit through useDialogPluginComponent()
    ...useDialogPluginComponent.emits,
  ],

  setup(props) {
    // REQUIRED; must be called inside of setup()
    const { dialogRef, onDialogHide, onDialogOK, onDialogCancel } =
      useDialogPluginComponent();
    // dialogRef      - Vue ref to be applied to QDialog
    // onDialogHide   - Function to be used as handler for @hide on QDialog
    // onDialogOK     - Function to call to settle dialog with "ok" outcome
    //                    example: onDialogOK() - no payload
    //                    example: onDialogOK({ /*.../* }) - with payload
    // onDialogCancel - Function to call to settle dialog with "cancel" outcome

    const calcTotalPrice = (pricePerLiter, amount) => {
      if (!pricePerLiter || !amount) {
        return null;
      }
      return (pricePerLiter * amount).toFixed(2);
    };

    const newDate = ref(props.date);
    const newMileage = ref(props.mileage);
    const newAmount = ref(props.amount);
    const newFull = ref(props.full);
    const newPrice = ref(props.price);
    const newTotalPrice = ref(calcTotalPrice(newPrice.value, newAmount.value));

    const pricePerLiter = computed({
      get() {
        return newPrice.value;
      },
      set(newValue) {
        newPrice.value = newValue;
        newTotalPrice.value = (newPrice.value * newAmount.value).toFixed(2);
      },
    });

    const totalPrice = computed({
      get() {
        return newTotalPrice.value;
      },
      set(newValue) {
        newTotalPrice.value = newValue;
        newPrice.value = (newTotalPrice.value / newAmount.value).toFixed(2);
      },
    });

    return {
      // This is REQUIRED;
      // Need to inject these (from useDialogPluginComponent() call)
      // into the vue scope for the vue html template
      dialogRef,
      onDialogHide,

      // other methods that we used in our vue html template;
      // these are part of our example (so not required)
      onOKClick() {
        // on OK, it is REQUIRED to
        // call onDialogOK (with optional payload)
        onDialogOK();
        // or with payload: onDialogOK({ ... })
        // ...and it will also hide the dialog automatically
      },

      // we can passthrough onDialogCancel directly
      onCancelClick: onDialogCancel,
      newDate,
      newMileage,
      newAmount,
      newFull,
      pricePerLiter,
      totalPrice,
    };
  },
};
</script>
