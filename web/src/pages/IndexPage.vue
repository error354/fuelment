<template>
  <q-page padding>
    <div class="row justify-center">
      <div class="full-width row justify-start q-col-gutter-lg">
        <div
          class="col-11 col-md-3"
          v-for="vehicle in vehicles"
          :key="vehicle.id"
        >
          <q-table class="col-grow" :title="vehicle.name" />
        </div>
      </div>
    </div>
  </q-page>
</template>

<script>
import { defineComponent, ref, watch } from "vue";
import { useProfileStore } from "../stores/profile.js";

export default defineComponent({
  name: "IndexPage",
  setup() {
    const profileStore = useProfileStore();
    return {
      vehicles: profileStore.getVehicles,
    };
  },
});
</script>
