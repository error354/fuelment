<template>
  <q-table
    square
    flat
    class="col-grow fuelings-table"
    :title="vehicle.name"
    :loading="loadingFuelings"
    :columns="columns"
    :rows="fuelings"
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
        <div>
          <q-icon
            name="mdi-circle-medium"
            class="handle cursor-default"
            size="md"
            :color="color"
          >
            <q-tooltip self="center middle">
              {{ $t(`fuelingsTable.fuel.${vehicle.fuel}`) }}
            </q-tooltip>
          </q-icon>
        </div>
        <span class="text-h6">{{ vehicle.name }}</span>
        <q-space />
        {{
          vehicle.avgFuelConsumption
            ? `${$t("fuelingsTable.avgFuelConsumption")}: ${
                vehicle.avgFuelConsumption
              }`
            : null
        }}
        <q-icon name="mdi-menu" size="sm" class="q-ml-md" />
      </div>
    </template>
    <template v-slot:body-cell-full="props">
      <q-td :props="props">
        <q-icon :name="props.value ? 'mdi-check' : 'mdi-close'"></q-icon>
      </q-td>
    </template>
    <template v-slot:loading>
      <q-inner-loading showing color="secondary" />
    </template>
  </q-table>
</template>

<script>
import { defineComponent, ref } from "vue";
import { apiClient, handleErrors } from "src/boot/apiClient";
import { i18n } from "../boot/i18n";

const $t = i18n.global.t;

export default defineComponent({
  name: "VehicleTable",
  props: {
    vehicle: Object,
  },
  setup(props) {
    let color = ref("blue-grey");
    switch (props.vehicle.fuel) {
      case "DIESEL":
        color = "dark";
        break;
      case "PETROL":
        color = "green";
        break;
      case "LPG":
        color = "amber";
        break;
      case "CNG":
        color = "brown";
        break;
      case "HYDROGEN":
        color = "light-blue";
        break;
      case "ELECTRICITY":
        color = "yellow";
        break;
      default:
        color = "blue-grey";
        break;
    }

    const columns = ref([
      { name: "date", label: $t("fuelingsTable.date"), field: "date" },
      { name: "mileage", label: $t("fuelingsTable.mileage"), field: "mileage" },
      { name: "amount", label: $t("fuelingsTable.amount"), field: "amount" },
      { name: "full", label: $t("fuelingsTable.full"), field: "full" },
      {
        name: "fuelConsumption",
        label: $t("fuelingsTable.fuelConsumption"),
        field: "fuelConsumption",
      },
      { name: "price", label: $t("fuelingsTable.price"), field: "price" },
    ]);

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
      color,
      paginationProps,
      getFuelings,
    };
  },
});
</script>

<style>
.fuelings-table {
  height: 396px;
  border: 1px solid rgba(0, 0, 0, 0.12);
}
</style>
