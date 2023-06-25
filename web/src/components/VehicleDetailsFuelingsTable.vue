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
    v-model:pagination="props.pagination"
    :rows-per-page-options="[ 5, 10, 15, 20, 25, 50 ]"
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
              vehicle.name,
              props.row.date,
              props.row.mileage,
              props.row.amount,
              props.row.full,
              showPrice(vehicle.priceSetting),
              props.row.price
            )
          "
        >
          <q-tooltip>{{ $t("fuelingsTable.edit") }}</q-tooltip>
        </q-btn>
        <q-btn
          round
          flat
          color="primary"
          icon="mdi-delete"
          size="xs"
          v-if="vehicle.canDelete"
        >
          <q-tooltip>{{ $t("fuelingsTable.delete") }}</q-tooltip>
        </q-btn>
      </q-td>
    </template>
  </q-table>
</template>

<script>
import { useQuasar } from "quasar";
import { defineComponent, ref } from "vue";
import { i18n } from "../boot/i18n";
import RouteDataDialog from "src/components/RouteDataDialog.vue";

const $t = i18n.global.t;

export default defineComponent({
  name: "VehicleDetailsFuelings",
  props: {
    fuelings: Array,
    vehicle: Object,
    loading: Boolean,
    pagination: Object,
  },
  async setup(props) {
    const $q = useQuasar();

    const showRouteDataDialog = (routeId) =>
      $q
        .dialog({
          component: RouteDataDialog,
          componentProps: {
            routeId: routeId,
          },
        })
        .onOk(() => {
          console.log("OK");
        })
        .onCancel(() => {
          console.log("Cancel");
        })
        .onDismiss(() => {
          console.log("Called on OK or Cancel");
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
      },
      { name: "route", label: "Trasa", align: "center" },
      { name: "actions", label: "Akcje", align: "center" },
    ]);

    const pricePerLiter = (price, amount) => {
      let result = (price / amount).toFixed(2);
      if (result == 0) {
        result = null;
      }
      return result;
    };

    const showActionsColumn = ref(
      props.vehicle.canEdit || props.vehicle.canDelete
    );
    const visibleColumns = ref(["pricePerLiter, route"]);
    if (showActionsColumn.value) {
      visibleColumns.value.push("actions");
    }

    return {
      columns,
      pricePerLiter,
      props,
      showRouteDataDialog,
    };
  },
});
</script>