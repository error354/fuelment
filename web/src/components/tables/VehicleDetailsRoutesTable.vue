<template>
  <q-table
    square
    flat
    class="col-grow table"
    :loading="props.loading"
    :columns="columns"
    :rows="props.routes"
    :visible-columns="visibleColumns"
    :no-data-label="$t('routesTable.empty')"
    v-model:pagination="pagination"
    :rows-per-page-options="[5, 10, 15, 20, 25, 50]"
    icon-first-page="first_page"
    icon-last-page="last_page"
    @request="$emit('paginationChanged', $event)"
  >
    <template v-slot:body-cell-fuelings="props">
      <q-td :props="props">
        <q-btn
          round
          flat
          dense
          color="primary"
          icon="format_list_bulleted"
          size="13px"
          @click="showFuelingsDataDialog(props.row.id)"
        >
          <q-tooltip>{{ $t("routesTable.fuelings") }}</q-tooltip>
        </q-btn>
      </q-td>
    </template>
    <template v-slot:body-cell-actions="props">
      <q-td :props="props" class="row justify-center content-center">
        <q-btn
          round
          flat
          color="primary"
          icon="mdi-delete"
          size="xs"
          v-if="vehicle.canDelete"
        >
          <q-tooltip>{{ $t("routesTable.delete") }}</q-tooltip>
        </q-btn>
      </q-td>
    </template>
  </q-table>
</template>

<script setup>
import { useQuasar } from "quasar";
import { toRefs, ref } from "vue";
import { i18n } from "src/boot/i18n";
import FuelingsTableDialog from "src/components/dialogs/FuelingsTableDialog.vue";

const $t = i18n.global.t;
const $q = useQuasar();

const props = defineProps({
  routes: Array,
  vehicle: Object,
  loading: Boolean,
  pagination: Object,
});
const { pagination } = toRefs(props);

const columns = ref([
  {
    name: "distance",
    label: $t("routesTable.distance"),
    field: "distance",
    required: true,
  },
  {
    name: "avgFuelConsumption",
    label: $t("routesTable.avgFuelConsumption"),
    field: "avgFuelConsumption",
    required: true,
  },
  {
    name: "kilometerCost",
    label: $t("routesTable.kilometerCost"),
    field: "kilometerCost",
    required: true,
  },
  {
    name: "totalCost",
    label: $t("routesTable.totalCost"),
    field: "totalCost",
    required: true,
  },
  {
    name: "totalAmount",
    label: $t("routesTable.totalAmount"),
    field: "totalAmount",
    required: true,
  },
  { name: "fuelings", label: "Tankowania", align: "center" },
  { name: "actions", label: "Akcje", align: "center" },
]);

const showActionsColumn = ref(props.routes.canDelete);
if (showActionsColumn.value) {
  visibleColumns.value.push("actions");
}

const showFuelingsDataDialog = (routeId) => {
  $q.dialog({
    component: FuelingsTableDialog,
    componentProps: {
      routeId: routeId,
    },
  });
};
</script>
