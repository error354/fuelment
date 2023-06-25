<template>
  <q-tabs
    v-model="tab"
    dense
    class="text-grey"
    active-color="primary"
    indicator-color="primary"
    align="right"
    narrow-indicator
  >
    <FuelTypeDot :fuel="vehicle.fuel" />
    <span class="text-h6 text-black">{{ vehicle.name }}</span>
    <q-space />
    <q-tab name="fuelings" label="Tankowania" />
    <q-tab name="routes" label="Trasy" />
  </q-tabs>
  <q-tab-panels v-model="tab" animated>
    <q-tab-panel name="fuelings" class="q-pa-none">
      <VehicleDetailsFuelingsTable
        :fuelings="fuelings"
        :vehicle="vehicle"
        :loading="loadingFuelings"
        :pagination="fuelingsPaginationProps.pagination"
      />
    </q-tab-panel>

    <q-tab-panel name="routes" class="q-pa-none">
      <VehicleDetailsRoutesTable
        :routes="routes"
        :vehicle="vehicle"
        :loading="loadingRoutes"
        :pagination="routesPaginationProps.pagination"
      />
    </q-tab-panel>
  </q-tab-panels>
</template>

<script>
import { defineComponent, ref } from "vue";
import { apiClient, handleErrors } from "src/boot/apiClient";
import { i18n } from "../boot/i18n";
import FuelTypeDot from "src/components/FuelTypeDot.vue";
import VehicleDetailsFuelingsTable from "src/components/VehicleDetailsFuelingsTable.vue";
import VehicleDetailsRoutesTable from "src/components/VehicleDetailsRoutesTable.vue";

const $t = i18n.global.t;

export default defineComponent({
  name: "VehicleDetailsTables",
  components: {
    FuelTypeDot,
    VehicleDetailsFuelingsTable,
    VehicleDetailsRoutesTable
  },
  props: {
    vehicleId: Number,
  },
  async setup(props) {
    const fuelings = ref([]);
    const routes = ref([]);
    const vehicle = ref({});
    const loadingFuelings = ref(false);
    const loadingRoutes = ref(false);
    const loadingVehicle = ref({});
    const tab = ref("fuelings");

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
            isLastOfRoute
            route {
              id
            }
          }
          paginatorInfo {
            total
            currentPage
          }
        }
      }
    `;

    const getRoutesQuery = `
      query getRoutes ($vehicleId: ID! $page: Int, $first: Int) {
        routes(
          vehicleId: $vehicleId
          page: $page
          first: $first
        ) {
          data {
            id
            avgFuelConsumption
            distance
            kilometerCost
          }
          paginatorInfo {
            total
            currentPage
          }
        }
      }
    `;

    const getVehicleQuery = `
      query getVehicle ($vehicleId: ID!) {
        vehicle(
          id: $vehicleId
        ) {
          id
          name
          fuel
          avgFuelConsumption
          priceSetting
          canAdd
          canEdit
          canDelete
        }
      }
    `;

    async function getVehicle() {
      loadingVehicle.value = true;
      await apiClient
        .executeQuery({
          query: getVehicleQuery,
          variables: {
            vehicleId: props.vehicleId,
          },
        })
        .then((response) => {
          if (response.error) {
            handleErrors(response.error);
          }
          vehicle.value = response.data.vehicle;
        });
      loadingVehicle.value = false;
    }

    const fuelingsPaginationProps = ref({
      pagination: {
        page: 1,
        rowsPerPage: 20,
        rowsNumber: 0,
      },
    });

    const routesPaginationProps = ref({
      pagination: {
        page: 1,
        rowsPerPage: 20,
        rowsNumber: 0,
      },
    });

    async function getFuelings(pagination = fuelingsPaginationProps.value) {
      loadingFuelings.value = true;
      await apiClient
        .executeQuery({
          query: getFuelingsQuery,
          variables: {
            vehicleId: props.vehicleId,
            page: pagination.pagination.page,
            first: 20,
          },
        })
        .then((response) => {
          if (response.error) {
            handleErrors(response.error);
          }
          const fuelingsData = response.data.fuelings.data;
          fuelingsPaginationProps.value.pagination.rowsNumber =
            response.data.fuelings.paginatorInfo.total;
          fuelingsPaginationProps.value.pagination.page =
            response.data.fuelings.paginatorInfo.currentPage;
          fuelings.value = fuelingsData;
        });
      loadingFuelings.value = false;
    }

    async function getRoutes(pagination = routesPaginationProps.value) {
      loadingRoutes.value = true;
      await apiClient
        .executeQuery({
          query: getRoutesQuery,
          variables: {
            vehicleId: props.vehicleId,
            page: pagination.pagination.page,
            first: 20,
          },
        })
        .then((response) => {
          if (response.error) {
            handleErrors(response.error);
          }
          const routesData = response.data.routes.data;
          routesPaginationProps.value.pagination.rowsNumber =
            response.data.routes.paginatorInfo.total;
            routesPaginationProps.value.pagination.page =
            response.data.routes.paginatorInfo.currentPage;
            routes.value = routesData;
        });
      loadingRoutes.value = false;
    }

    await getVehicle();
    getFuelings();
    getRoutes();

    return {
      fuelings,
      loadingFuelings,
      tab,
      fuelingsPaginationProps,
      routesPaginationProps,
      getFuelings,
      vehicle,
      getVehicle,
      loadingVehicle,
      loadingRoutes,
      routes
    };
  },
});
</script>

<style scoped>
.table {
  height: calc(100vh - 150px);
  min-height: 600px;
  border: 1px solid rgba(0, 0, 0, 0.12);
}
</style>

<style>
.text-black {
  color: #000 !important;
}
.bg-black {
  background: #000 !important;
}
</style>
