<template>
  <q-tabs
    v-model="tab"
    dense
    class="text-grey"
    active-color="primary"
    indicator-color="primary"
    align="right"
    narrow-indicator
    @update:model-value="tabChanged"
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
      v-if="tab === 'fuelings'"
    >
      <q-badge
        color="secondary"
        rounded
        floating
        class="q-mr-sm q-mt-sm"
        v-if="areFiltersActive()"
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
        @fuelingChanged="fuelingChanged"
        @paginationChanged="fuelingsPaginationChanged"
      />
    </q-tab-panel>

    <q-tab-panel name="routes" class="q-pa-none">
      <VehicleDetailsRoutesTable
        :routes="routes"
        :vehicle="vehicle"
        :loading="loadingRoutes"
        :pagination="routesPaginationProps"
        @paginationChanged="routesPaginationChanged"
      />
    </q-tab-panel>
  </q-tab-panels>
</template>

<script>
import { defineComponent, ref } from "vue";
import { useQuasar } from "quasar";
import { useRoute } from "vue-router";
import { apiClient, handleErrors } from "src/boot/apiClient";
import { i18n } from "src/boot/i18n";
import FuelTypeDot from "src/components/FuelTypeDot.vue";
import VehicleDetailsFuelingsTable from "src/components/tables/VehicleDetailsFuelingsTable.vue";
import VehicleDetailsRoutesTable from "src/components/tables/VehicleDetailsRoutesTable.vue";
import FuelingsFiltersDialog from "src/components/dialogs/FuelingsFiltersDialog.vue";
import { useFuelingDialog } from "src/composables/fuelingDialog";
import { useUrlHelper } from "src/composables/urlHelper";

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
    const route = useRoute();
    const urlHelper = useUrlHelper();

    const fuelings = ref([]);
    const routes = ref([]);
    const vehicle = ref({});
    const loadingFuelings = ref(false);
    const loadingRoutes = ref(false);
    const loadingVehicle = ref(false);
    const tab = route.query.tab ? ref(route.query.tab) : ref("fuelings");

    const { showAddFuelingDialog } = useFuelingDialog(context);

    const queryParamsToFilters = (
      dateTo = route.query.dateTo,
      dateFrom = route.query.dateFrom,
      mileageTo = route.query.mileageTo
        ? Number(route.query.mileageTo)
        : route.query.mileageTo,
      mileageFrom = route.query.mileageFrom
        ? Number(route.query.mileageFrom)
        : route.query.mileageFrom
    ) => {
      return {
        date: {
          to: dateTo,
          from: dateFrom,
        },
        mileage: {
          to: mileageTo,
          from: mileageFrom,
        },
      };
    };
    const fuelingsFilters = ref(queryParamsToFilters());

    const isObjectEmpty = (obj) => {
      let result = false;
      Object.values(obj).every((o) => {
        if (o === null || o === undefined || o === "") {
          result = true;
        }
        if (typeof o == "object") {
          result = isObjectEmpty(o);
        }
      });
      return result;
    };

    const areFiltersActive = () => {
      return !isObjectEmpty(queryParamsToFilters());
    };

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
        setQueryParams(
          route.query.fuelingsPage || 1,
          route.query.routesPage || 1,
          route.query.tab || "fuelings",
          filters
        );
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
            totalCost
            totalAmount
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

    async function getFuelings(
      pagination = fuelingsPaginationProps.value,
      filters = fuelingsFilters.value
    ) {
      loadingFuelings.value = true;
      fuelingsFilters.value = filters;
      await apiClient
        .executeQuery({
          query: getFuelingsQuery,
          variables: {
            vehicleId: props.vehicleId,
            page: pagination.page,
            first: pagination.rowsPerPage,
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

    const getOtherTab = () => {
      if (route.query.tab === "fuelings") return "routes";
      return "fuelings";
    };

    const setQueryParams = (fuelingsPage, routesPage, tab, filters = {}) => {
      urlHelper.setQueryParams({
        fuelingsPage: fuelingsPage,
        routesPage: routesPage,
        tab: tab,
        dateFrom: filters.date?.from,
        dateTo: filters.date?.to,
        mileageFrom: filters.mileage?.from,
        mileageTo: filters.mileage?.to,
      });
    };

    setQueryParams(
      route.query.fuelingsPage || 1,
      route.query.routesPage || 1,
      route.query.tab || "fuelings",
      queryParamsToFilters()
    );

    const fuelingsPaginationProps = ref({
      page: Number(route.query.fuelingsPage),
      rowsPerPage: Number($q.localStorage.getItem("fuelingsRowsPerPage")) || 20,
      rowsNumber: 0,
    });
    const routesPaginationProps = ref({
      page: Number(route.query.routesPage),
      rowsPerPage: Number($q.localStorage.getItem("routesRowsPerPage")) || 20,
      rowsNumber: 0,
    });

    $q.localStorage.set(
      "fuelingsRowsPerPage",
      fuelingsPaginationProps.value.rowsPerPage
    );
    $q.localStorage.set(
      "routesRowsPerPage",
      routesPaginationProps.value.rowsPerPage
    );

    const tabChanged = () => {
      setQueryParams(
        route.query.fuelingsPage,
        route.query.routesPage,
        getOtherTab(),
        queryParamsToFilters()
      );
    };

    const fuelingsPaginationChanged = (e) => {
      fuelingsPaginationProps.value.page = e.pagination.page;
      fuelingsPaginationProps.value.rowsPerPage = e.pagination.rowsPerPage;
      fuelingsPaginationProps.value.rowsNumber = e.pagination.rowsNumber;
      $q.localStorage.set(
        "fuelingsRowsPerPage",
        fuelingsPaginationProps.value.rowsPerPage
      );
      setQueryParams(
        fuelingsPaginationProps.value.page || 1,
        route.query.routesPage || 1,
        route.query.tab || "fuelings"
      );
      getFuelings();
    };

    const routesPaginationChanged = (e) => {
      routesPaginationProps.value.page = e.pagination.page;
      routesPaginationProps.value.rowsPerPage = e.pagination.rowsPerPage;
      routesPaginationProps.value.rowsNumber = e.pagination.rowsNumber;
      $q.localStorage.set(
        "routesRowsPerPage",
        routesPaginationProps.value.rowsPerPage
      );
      setQueryParams(
        routesPaginationProps.value.page || 1,
        route.query.routesPage || 1,
        route.query.tab || "routes"
      );
      getRoutes();
    };

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
      tabChanged,
      fuelingsPaginationChanged,
      routesPaginationChanged,
      areFiltersActive,
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
