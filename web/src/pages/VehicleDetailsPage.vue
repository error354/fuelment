<template>
  <q-page class="q-px-lg q-py-lg">
    <div class="fit row reverse">
      <div class="col-12 col-md-4 data-column">
        <VehicleDataList :vehicleId="route.params.id" />
      </div>
      <div class="col-12 col-md-8 q-pr-lg">
        <Suspense>
          <template #default>
            <KeepAlive>
              <VehicleDetailsTables :vehicleId="route.params.id" />
            </KeepAlive>
          </template>
          <template #fallback>
            <VehicleDetailsTablesLoadingSkeleton />
          </template>
        </Suspense>
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { useRoute } from "vue-router";
import VehicleDataList from "../components/VehicleDataList.vue";
import VehicleDetailsTables from "../components/VehicleDetailsTables.vue";
import VehicleDetailsTablesLoadingSkeleton from "../components/VehicleDetailsTablesLoadingSkeleton.vue";

const route = useRoute();
</script>

<style scoped>
.data-column {
  margin-top: 27px;
}
</style>
