<template>
  <data-list title="Obecny miesiąc">
    <data-list-element
      name="Średnie spalanie"
      :loading="loadingVehicle"
      :value="currentMonthData.avgFuelConsumption"
    />
    <data-list-element
      v-if="vehicle.priceSetting !== 'DISABLED'"
      name="Koszt 1 km"
      :value="currentMonthData.kilometerCost"
      :loading="loadingVehicle"
    />
    <data-list-element
      name="Przejechany dystans"
      :value="currentMonthData.distance"
      :loading="loadingVehicle"
    />
    <data-list-element
      name="Łącznie zatankowano"
      :value="currentMonthData.totalFuelAmount"
      :loading="loadingVehicle"
    />
    <data-list-element
      v-if="vehicle.priceSetting !== 'DISABLED'"
      name="Koszt wszystkich tankowań"
      :value="currentMonthData.totalCost"
      :loading="loadingVehicle"
    />
  </data-list>
  <data-list title="Poprzedni miesiąc">
    <data-list-element
      name="Średnie spalanie"
      :value="prevMonthData.avgFuelConsumption"
      :loading="loadingVehicle"
    />
    <data-list-element
      v-if="vehicle.priceSetting !== 'DISABLED'"
      name="Koszt 1 km"
      :value="prevMonthData.kilometerCost"
      :loading="loadingVehicle"
    />
    <data-list-element
      name="Przejechany dystans"
      :value="prevMonthData.distance"
      :loading="loadingVehicle"
    />
    <data-list-element
      name="Łącznie zatankowano"
      :value="prevMonthData.totalFuelAmount"
      :loading="loadingVehicle"
    />
    <data-list-element
      v-if="vehicle.priceSetting !== 'DISABLED'"
      name="Koszt wszystkich tankowań"
      :value="prevMonthData.totalCost"
      :loading="loadingVehicle"
    />
  </data-list>
  <data-list title="Od początku">
    <data-list-element
      name="Średnie spalanie"
      :value="allTimeData.avgFuelConsumption"
      :loading="loadingVehicle"
    />
    <data-list-element
      v-if="vehicle.priceSetting !== 'DISABLED'"
      name="Koszt 1 km"
      :value="allTimeData.kilometerCost"
      :loading="loadingVehicle"
    />
    <data-list-element
      name="Przejechany dystans"
      :value="allTimeData.distance"
      :loading="loadingVehicle"
    />
    <data-list-element
      name="Łącznie zatankowano"
      :value="allTimeData.totalFuelAmount"
      :loading="loadingVehicle"
    />
    <data-list-element
      v-if="vehicle.priceSetting != 'DISABLED'"
      name="Koszt wszystkich tankowań"
      :value="allTimeData.totalCost"
      :loading="loadingVehicle"
    />
  </data-list>
</template>

<script>
import { defineComponent, ref } from "vue";
import { apiClient, handleErrors } from "src/boot/apiClient";
import DataList from "../components/DataList.vue";
import DataListElement from "../components/DataListElement.vue";
import { i18n } from "../boot/i18n";

const $t = i18n.global.t;

export default defineComponent({
  name: "VehicleDataList",
  components: {
    DataList,
    DataListElement,
  },
  props: {
    vehicleId: Number,
  },
  setup(props) {
    const vehicle = ref({});
    const allTimeData = ref({});
    const currentMonthData = ref({});
    const prevMonthData = ref({});
    const loadingVehicle = ref(false);
    const getVehicleQuery = `
      query getVehicle ($id: ID!, $prevMonthFrom: Date, $prevMonthTo: Date, $currentMonthFrom: Date) {
        data: vehicle(id: $id) {
          id
          name
          fuel
          priceSetting
        }
        allTime: vehicle(id: $id) {
          avgFuelConsumption
          distance
          totalFuelAmount
          totalCost
          kilometerCost
        }
        currentMonth: vehicle(id: $id) {
          avgFuelConsumption(dateFrom: $currentMonthFrom)
          distance(dateFrom: $currentMonthFrom)
          totalFuelAmount(dateFrom: $currentMonthFrom)
          totalCost(dateFrom: $currentMonthFrom)
          kilometerCost(dateFrom: $currentMonthFrom)
        }
        prevMonth: vehicle(id: $id) {
          avgFuelConsumption(dateFrom: $prevMonthFrom, dateTo: $prevMonthTo)
          distance(dateFrom: $currentMonthFrom)
          totalFuelAmount(dateFrom: $prevMonthFrom, dateTo: $prevMonthTo)
          totalCost(dateFrom: $prevMonthFrom, dateTo: $prevMonthTo)
          kilometerCost(dateFrom: $prevMonthFrom, dateTo: $prevMonthTo)
        }
      }
    `;

    async function getVehicle() {
      loadingVehicle.value = true;
      const date = new Date();
      const currentMonthFrom = `${date.getFullYear()}-${date.getMonth() + 1}-1`;
      const prevMonthFrom = `${date.getFullYear()}-${date.getMonth()}-1`;
      date.setDate(1); // going to 1st of the month
      date.setHours(-1); // going to last hour before this date even started.
      const prevMonthTo = `${date.getFullYear()}-${
        date.getMonth() + 1
      }-${date.getDate()}`;
      console.log(currentMonthFrom);
      console.log(prevMonthFrom);
      console.log(prevMonthTo);
      await apiClient
        .executeQuery({
          query: getVehicleQuery,
          variables: {
            id: props.vehicleId,
            prevMonthFrom: prevMonthFrom,
            prevMonthTo: prevMonthTo,
            currentMonthFrom: currentMonthFrom,
          },
        })
        .then((response) => {
          if (response.error) {
            handleErrors(response.error);
          }
          vehicle.value = response.data.data;
          allTimeData.value = response.data.allTime;
          currentMonthData.value = response.data.currentMonth;
          prevMonthData.value = response.data.prevMonth;
        });
      loadingVehicle.value = false;
    }

    getVehicle();

    return {
      vehicle,
      loadingVehicle,
      allTimeData,
      prevMonthData,
      currentMonthData,
    };
  },
});
</script>
