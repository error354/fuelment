<template>
  <data-list :title="$t('vehicleDetails.currentMonth')">
    <data-list-element
      :name="$t('vehicleDetails.avgFuelConsumption')"
      :loading="loadingVehicle"
      :value="currentMonthData.avgFuelConsumption"
    />
    <data-list-element
      v-if="vehicle.priceSetting !== 'DISABLED'"
      :name="$t('vehicleDetails.kilometerCost')"
      :value="currentMonthData.kilometerCost"
      :loading="loadingVehicle"
    />
    <data-list-element
      :name="$t('vehicleDetails.distance')"
      :value="currentMonthData.distance"
      :loading="loadingVehicle"
    />
    <data-list-element
      :name="$t('vehicleDetails.totalFuelAmount')"
      :value="currentMonthData.totalFuelAmount"
      :loading="loadingVehicle"
    />
    <data-list-element
      v-if="vehicle.priceSetting !== 'DISABLED'"
      :name="$t('vehicleDetails.totalCost')"
      :value="currentMonthData.totalCost"
      :loading="loadingVehicle"
    />
  </data-list>
  <data-list :title="$t('vehicleDetails.prevMonth')">
    <data-list-element
      :name="$t('vehicleDetails.avgFuelConsumption')"
      :value="prevMonthData.avgFuelConsumption"
      :loading="loadingVehicle"
    />
    <data-list-element
      v-if="vehicle.priceSetting !== 'DISABLED'"
      :name="$t('vehicleDetails.kilometerCost')"
      :value="prevMonthData.kilometerCost"
      :loading="loadingVehicle"
    />
    <data-list-element
      :name="$t('vehicleDetails.distance')"
      :value="prevMonthData.distance"
      :loading="loadingVehicle"
    />
    <data-list-element
      :name="$t('vehicleDetails.totalFuelAmount')"
      :value="prevMonthData.totalFuelAmount"
      :loading="loadingVehicle"
    />
    <data-list-element
      v-if="vehicle.priceSetting !== 'DISABLED'"
      :name="$t('vehicleDetails.totalCost')"
      :value="prevMonthData.totalCost"
      :loading="loadingVehicle"
    />
  </data-list>
  <data-list :title="$t('vehicleDetails.ever')">
    <data-list-element
      :name="$t('vehicleDetails.avgFuelConsumption')"
      :value="allTimeData.avgFuelConsumption"
      :loading="loadingVehicle"
    />
    <data-list-element
      v-if="vehicle.priceSetting !== 'DISABLED'"
      :name="$t('vehicleDetails.kilometerCost')"
      :value="allTimeData.kilometerCost"
      :loading="loadingVehicle"
    />
    <data-list-element
      :name="$t('vehicleDetails.distance')"
      :value="allTimeData.distance"
      :loading="loadingVehicle"
    />
    <data-list-element
      :name="$t('vehicleDetails.totalFuelAmount')"
      :value="allTimeData.totalFuelAmount"
      :loading="loadingVehicle"
    />
    <data-list-element
      v-if="vehicle.priceSetting != 'DISABLED'"
      :name="$t('vehicleDetails.totalCost')"
      :value="allTimeData.totalCost"
      :loading="loadingVehicle"
    />
  </data-list>
</template>

<script setup>
import { ref } from "vue";
import { apiClient, handleErrors } from "src/boot/apiClient";
import DataList from "../components/DataList.vue";
import DataListElement from "../components/DataListElement.vue";
import { i18n } from "../boot/i18n";

const $t = i18n.global.t;
const props = defineProps({
  vehicleId: Number,
});
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
</script>
