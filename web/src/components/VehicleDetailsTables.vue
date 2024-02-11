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
    <q-btn
      round
      flat
      color="primary"
      icon="mdi-arrow-left"
      size="md"
      @click="$router.back()"
    >
      <q-tooltip self="center middle">{{
        $t("vehicleDetails.return")
      }}</q-tooltip>
    </q-btn>
    <FuelTypeDot :fuel="vehicle.fuel" />
    <span class="text-h6 text-black">{{ vehicle.name }}</span>
    <q-btn
      round
      flat
      color="primary"
      icon="mdi-tune"
      size="md"
      class="q-ml-sm"
      :to="`/administration/vehicle/${vehicle.id}`"
    >
      <q-tooltip self="center middle">{{
        $t("vehicleDetails.settings")
      }}</q-tooltip>
    </q-btn>
    <q-space />
    <q-btn
      round
      flat
      color="primary"
      icon="mdi-filter"
      size="md"
      @click="showFuelingsFiltersDialog()"
    >
      <q-badge
        color="secondary"
        rounded
        floating
        class="q-mr-sm q-mt-sm"
        v-if="fuelingsFilters && Object.keys(fuelingsFilters).length"
      />
      <q-tooltip self="center middle">{{
        $t("fuelingsTable.filter")
      }}</q-tooltip>
    </q-btn>
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
        )
      "
    >
      <q-tooltip self="center middle">{{ $t("fuelingsTable.add") }}</q-tooltip>
    </q-btn>
    <q-tab name="fuelings" label="Tankowania" />
    <q-tab name="routes" label="Trasy" />
  </q-tabs>
  <q-tab-panels v-model="tab" animated>
    <q-tab-panel name="fuelings" class="q-pa-none">
      <VehicleDetailsFuelingsTable
        :fuelings="fuelings"
        :vehicle="vehicle"
        :loading="loadingFuelings"
        :pagination="fuelingsPaginationProps"
        @fuelingChanged="fuelingChanged()"
      />
    </q-tab-panel>

    <q-tab-panel name="routes" class="q-pa-none">
      <VehicleDetailsRoutesTable
        :routes="routes"
        :vehicle="vehicle"
        :loading="loadingRoutes"
        :pagination="routesPaginationProps"
      />
    </q-tab-panel>
  </q-tab-panels>
</template>

<script>
import { defineComponent, ref } from "vue";
import { useQuasar } from "quasar";
import { apiClient, handleErrors } from "src/boot/apiClient";
import { i18n } from "../boot/i18n";
import FuelTypeDot from "src/components/FuelTypeDot.vue";
import VehicleDetailsFuelingsTable from "src/components/VehicleDetailsFuelingsTable.vue";
import VehicleDetailsRoutesTable from "src/components/VehicleDetailsRoutesTable.vue";
import FuelingsFiltersDialog from "src/components/FuelingsFiltersDialog.vue";
import { useFuelingDialog } from "src/composables/fuelingDialog";

const $t = i18n.global.t;

export default defineComponent({
  name: "VehicleDetailsTables",
  components: {
    FuelTypeDot,
    VehicleDetailsFuelingsTable,
    VehicleDetailsRoutesTable,
  },
  props: {
    vehicleId: Number,
  },
  async setup(props, context) {
    const $q = useQuasar();

    const fuelings = ref([]);
    const routes = ref([]);
    const vehicle = ref({});
    const loadingFuelings = ref(false);
    const loadingRoutes = ref(false);
    const loadingVehicle = ref(false);
    const tab = ref("fuelings");

    const { showAddFuelingDialog } = useFuelingDialog(context);
    const fuelingsFilters = ref({});

    const showFuelingsFiltersDialog = () => {
      $q.dialog({
        component: FuelingsFiltersDialog,
        componentProps: {
          dateFrom: fuelingsFilters?.value?.date?.from,
          dateTo: fuelingsFilters?.value?.date?.to,
          mileageFrom: fuelingsFilters?.value?.mileage?.from,
          mileageTo: fuelingsFilters?.value?.mileage?.to,
        },
      }).onOk(async (filters) => {
        await getFuelings(
          {
            page: 1,
            rowsPerPage: fuelingsPaginationProps.value.rowsPerPage,
            rowsNumber: fuelingsPaginationProps.value.rowsNumber,
          },
          filters
        );
      });
    };

    const fuelingChanged = async (filters) => {
      await getFuelings(
        {
          page: 1,
          rowsPerPage: fuelingsPaginationProps.value.rowsPerPage,
          rowsNumber: fuelingsPaginationProps.value.rowsNumber,
        },
        filters
      );
    };

    const getFuelingsQuery = `
      query getFuelings ($vehicleId: ID! $page: Int, $first: Int, $filter: FuelingsFilters) {
        fuelings(
          vehicleId: $vehicleId
          page: $page
          first: $first
          filter: $filter
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
      page: 1,
      rowsPerPage: 20,
      rowsNumber: 0,
    });

    const routesPaginationProps = ref({
      page: 1,
      rowsPerPage: 20,
      rowsNumber: 0,
    });

    async function getFuelings(
      pagination = fuelingsPaginationProps.value,
      filters = null
    ) {
      loadingFuelings.value = true;
      fuelingsFilters.value = filters;
      console.log(filters);
      console.log(fuelingsFilters.value);
      console.log(!!fuelingsFilters.value);
      console.log(Boolean(fuelingsFilters.value));
      await apiClient
        .executeQuery({
          query: getFuelingsQuery,
          variables: {
            vehicleId: props.vehicleId,
            page: pagination.page,
            first: 20,
            filter: filters,
          },
          tags: [`vehicle_${props.vehicleId}_fuelings`],
        })
        .then((response) => {
          if (response.error) {
            handleErrors(response.error);
          }
          const fuelingsData = response.data.fuelings.data;
          fuelingsPaginationProps.value.rowsNumber =
            response.data.fuelings.paginatorInfo.total;
          fuelingsPaginationProps.value.page =
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
            page: pagination.page,
            first: 20,
          },
        })
        .then((response) => {
          if (response.error) {
            handleErrors(response.error);
          }
          const routesData = response.data.routes.data;
          routesPaginationProps.value.rowsNumber =
            response.data.routes.paginatorInfo.total;
          routesPaginationProps.value.page =
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
      routes,
      showAddFuelingDialog,
      showFuelingsFiltersDialog,
      fuelingsFilters,
      fuelingChanged,
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
