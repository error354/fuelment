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
    v-model:pagination="props.pagination"
    :rows-per-page-options="[ 5, 10, 15, 20, 25, 50 ]"
    icon-first-page="first_page"
    icon-last-page="last_page"
    @request="$emit('pageChanged')"
  >
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

<script>
import { useQuasar } from "quasar";
import { defineComponent, ref } from "vue";
import { i18n } from "../boot/i18n";

const $t = i18n.global.t;

export default defineComponent({
  name: "VehicleDetailsRoutes",
  props: {
    routes: Array,
    vehicle: Object,
    loading: Boolean,
    pagination: Object,
  },
  async setup(props) {
    const $q = useQuasar();

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
      { name: "actions", label: "Akcje", align: "center" },
    ]);

    const showActionsColumn = ref(
      props.routes.canDelete
    );
    if (showActionsColumn.value) {
      visibleColumns.value.push("actions");
    }

    return {
      columns,
      props,
    };
  },
});
</script>
