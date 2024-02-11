<template>
  <q-table
    square
    flat
    class="col-grow table"
    :loading="props.loading"
    :columns="columns"
    :rows="props.fuelings"
    :visible-columns="visibleColumns"
    :no-data-label="$t('fuelingsTable.empty')"
    v-model:pagination="pagination"
    :rows-per-page-options="[5, 10, 15, 20, 25, 50]"
    icon-first-page="first_page"
    icon-last-page="last_page"
    @request="$emit('pageChanged')"
  >
    <template v-slot:body-cell-full="props">
      <q-td :props="props">
        <q-icon :name="props.value ? 'mdi-check' : 'mdi-close'"></q-icon>
      </q-td>
    </template>
    <template v-slot:body-cell-pricePerLiter="props">
      <q-td :props="props">
        {{ pricePerLiter(props.row.price, props.row.amount) }}
      </q-td>
    </template>
    <template v-slot:body-cell-route="props">
      <q-td :props="props">
        <q-btn
          round
          flat
          dense
          color="primary"
          icon="mdi-flag-checkered"
          size="13px"
          v-if="props.row.isLastOfRoute"
          @click="showRouteDataDialog(props.row.route.id)"
        >
          <q-tooltip>Koniec trasy</q-tooltip>
        </q-btn>
      </q-td>
    </template>
    <template v-slot:body-cell-actions="props">
      <q-td :props="props" class="row justify-center content-center">
        <q-btn
          round
          flat
          color="primary"
          icon="mdi-pencil"
          size="xs"
          v-if="vehicle.canEdit"
          @click="
            showEditFuelingDialog(
              props.row.id,
              vehicle.name,
              vehicle.priceSetting,
              $t('fuelingsTable.editingDialogTitle'),
              props.row.date,
              props.row.mileage,
              props.row.amount,
              props.row.full,
              false,
              props.row.price
            )
          "
        >
          <q-tooltip>{{ $t("fuelingsTable.edit") }}</q-tooltip>
        </q-btn>
        <DeleteFuelingButton
          :vehicleId="vehicle.id"
          :fuelingId="props.row.id"
          v-if="vehicle.canDelete"
          @fuelingChanged="$emit('fuelingChanged')"
        />
      </q-td>
    </template>
  </q-table>
</template>

<script setup>
import { useQuasar } from "quasar";
import { toRefs, ref } from "vue";
import { i18n } from "../boot/i18n";
import RouteDataDialog from "src/components/RouteDataDialog.vue";
import DeleteFuelingButton from "src/components/DeleteFuelingButton.vue";
import EditFuelingDialog from "src/components/EditFuelingDialog.vue";

const $t = i18n.global.t;

const props = defineProps({
  fuelings: Array,
  vehicle: Object,
  loading: Boolean,
  pagination: Object,
});

const { pagination } = toRefs(props);

const emit = defineEmits(["fuelingChanged"]);

const $q = useQuasar();

const showEditFuelingDialog = (
  fuelingId,
  vehicleName,
  priceSetting,
  title,
  fuelingDate,
  mileage,
  amount,
  full,
  route,
  price
) =>
  $q
    .dialog({
      component: EditFuelingDialog,
      componentProps: {
        fuelingId: fuelingId,
        vehicle: props.vehicle,
        priceSetting: priceSetting,
        title: title,
        fuelingDate: fuelingDate,
        mileage: mileage,
        amount: amount,
        full: full,
        route: route,
        price: price,
      },
    })
    .onOk(() => {
      console.log("ok");
      emit("fuelingChanged");
    });

const showRouteDataDialog = (routeId) =>
  $q.dialog({
    component: RouteDataDialog,
    componentProps: {
      routeId: routeId,
    },
  });

const columns = ref([
  {
    name: "date",
    label: $t("fuelingsTable.date"),
    field: "date",
    required: true,
  },
  {
    name: "mileage",
    label: $t("fuelingsTable.mileage"),
    field: "mileage",
    required: true,
  },
  {
    name: "amount",
    label: $t("fuelingsTable.amount"),
    field: "amount",
    required: true,
  },
  {
    name: "full",
    label: $t("fuelingsTable.full"),
    field: "full",
    required: true,
    align: "center",
  },
  {
    name: "fuelConsumption",
    label: $t("fuelingsTable.fuelConsumption"),
    field: "fuelConsumption",
    required: true,
  },
  {
    name: "price",
    label: $t("fuelingsTable.price"),
    field: "price",
    required: true,
  },
  {
    name: "pricePerLiter",
    label: $t("fuelingsTable.pricePerLiter"),
    required: true,
  },
  { name: "route", label: "Trasa", align: "center", required: true },
  { name: "actions", label: "Akcje", align: "center" },
]);

const pricePerLiter = (price, amount) => {
  let result = (price / amount).toFixed(2);
  if (result == 0) {
    result = null;
  }
  return result;
};

const showActionsColumn = ref(props.vehicle.canEdit || props.vehicle.canDelete);
const visibleColumns = ref([]);
if (showActionsColumn.value) {
  visibleColumns.value.push("actions");
}
</script>
