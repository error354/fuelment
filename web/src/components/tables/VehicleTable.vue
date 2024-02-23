<template>
  <q-table
    square
    flat
    class="col-grow fuelings-table"
    :title="vehicle.name"
    :loading="loadingFuelings"
    :columns="columns"
    :rows="fuelings"
    :visible-columns="visibleColumns"
    :no-data-label="$t('fuelingsTable.empty')"
    v-model:pagination="paginationProps.pagination"
    rows-per-page-options="5"
    icon-first-page="first_page"
    icon-last-page="last_page"
    @request="getFuelings"
  >
    <template v-slot:top>
      <div
        class="relative-position row items-center handle cursor-grab full-width"
      >
        <FuelTypeDot :fuel="vehicle.fuel" />
        <span class="text-h6">{{ vehicle.name }}</span>
        <q-space />
        <span class="q-mr-sm" v-if="vehicle.avgFuelConsumption">
          {{
            `${$t("fuelingsTable.avgFuelConsumption")}: ${
              vehicle.avgFuelConsumption
            }`
          }}
        </span>
        <q-btn
          round
          flat
          color="secondary"
          icon="add"
          size="md"
          v-if="vehicle.canAdd"
          @click="
            showAddFuelingDialog(
              vehicle.id,
              vehicle.name,
              vehicle.priceSetting,
              $t('fuelingsTable.addingDialogTitle')
            ).onOk(getFuelings)
          "
        >
          <q-tooltip self="center middle">{{
            $t("fuelingsTable.add")
          }}</q-tooltip>
        </q-btn>
        <q-btn
          round
          flat
          color="primary"
          icon="read_more"
          size="md"
          @click="this.$router.push(`/vehicle/${vehicle.id}`)"
        >
          <q-tooltip self="center middle">{{
            $t("fuelingsTable.details")
          }}</q-tooltip>
        </q-btn>
      </div>
    </template>
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
  </q-table>
</template>

<script>
import { defineComponent, ref } from "vue";
import { useQuasar } from "quasar";
import { apiClient, handleErrors } from "src/boot/apiClient";
import { i18n } from "src/boot/i18n";
import FuelTypeDot from "src/components/FuelTypeDot.vue";
import { useFuelingDialog } from "src/composables/fuelingDialog";

const $t = i18n.global.t;

export default defineComponent({
  name: "VehicleTable",
  components: {
    FuelTypeDot,
  },
  props: {
    vehicle: Object,
  },
  setup(props, context) {
    const $q = useQuasar();

    const { showAddFuelingDialog } = useFuelingDialog(context);

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
    ]);

    const visibleColumns = ref(["pricePerLiter"]);

    const pricePerLiter = (price, amount) => {
      let result = (price / amount).toFixed(2);
      if (result == 0) {
        result = null;
      }
      return result;
    };

    const getFuelingsQuery = `
      query getFuelings ($vehicleId: ID! $page: Int, $first: Int) {
        fuelings(
          vehicleId: $vehicleId
          page: $page
          first: $first
        ) {
          data {
            id
            amount
            mileage
            full
            fuelConsumption
            date
            price
          }
          paginatorInfo {
            total
            currentPage
          }
        }
      }
    `;

    const fuelings = ref([]);
    const loadingFuelings = ref(false);

    const paginationProps = ref({
      pagination: {
        page: 1,
        rowsPerPage: 5,
        rowsNumber: 0,
      },
    });

    async function getFuelings(pagination = paginationProps.value) {
      loadingFuelings.value = true;
      await apiClient
        .executeQuery({
          query: getFuelingsQuery,
          variables: {
            vehicleId: props.vehicle.id,
            page: pagination.pagination.page,
            first: 5,
          },
          tags: [`vehicle_${props.vehicle.id}_fuelings`],
        })
        .then((response) => {
          if (response.error) {
            handleErrors(response.error);
          }
          const fuelingsData = response.data.fuelings.data;
          paginationProps.value.pagination.rowsNumber =
            response.data.fuelings.paginatorInfo.total;
          paginationProps.value.pagination.page =
            response.data.fuelings.paginatorInfo.currentPage;
          fuelings.value = fuelingsData;
        });
      loadingFuelings.value = false;
    }

    getFuelings();

    return {
      columns,
      fuelings,
      loadingFuelings,
      paginationProps,
      getFuelings,
      visibleColumns,
      pricePerLiter,
      showAddFuelingDialog,
    };
  },
});
</script>

<style>
.fuelings-table {
  height: 406px;
  border: 1px solid rgba(0, 0, 0, 0.12);
}

.q-table__top {
  padding-right: 16px;
}
</style>
